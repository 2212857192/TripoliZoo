<?php

namespace App\Services;

use App\Enums\FieldCaseStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\MedicalCaseResult;
use App\Enums\ReceivingTaskType;
use App\Models\FieldCase;
use App\Models\HospitalCase;
use App\Models\MedicalCaseProcedure;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MedicalCaseProcedureService
{
    public function __construct(
        private HospitalCaseNotificationService $hospitalNotifier,
        private ReceivingTaskService $receivingTasks,
        private AnimalLifecycleService $animalLifecycle,
        private SupervisorNotificationService $supervisorNotifier,
    ) {}

    public function record(
        Model $case,
        User $vet,
        string $diagnosis,
        string $treatment,
        MedicalCaseResult $result,
        ?string $note = null,
        ?array $nutrition = null,
    ): MedicalCaseProcedure {
        $this->authorizeVetForCase($case, $vet);
        $this->ensureCaseAcceptsProcedures($case);

        $procedure = null;

        DB::transaction(function () use ($case, $vet, $diagnosis, $treatment, $result, $note, $nutrition, &$procedure) {
            $procedure = $case->procedures()->create([
                'recorded_by' => $vet->id,
                'diagnosis' => $diagnosis,
                'treatment' => $treatment,
                'note' => $note,
                'case_result' => $result,
                'recorded_at' => now(),
            ]);

            if ($nutrition !== null) {
                $procedure->nutritionRecommendation()->create($nutrition);
            }

            if ($case instanceof HospitalCase) {
                $this->applyHospitalResult($case, $vet, $result);
            }
        });

        if ($nutrition !== null && $procedure) {
            $procedure->load('nutritionRecommendation');
            if ($procedure->nutritionRecommendation) {
                $this->supervisorNotifier->notifyNutritionRecommendation($procedure->nutritionRecommendation);
            }
        }

        /** @var MedicalCaseProcedure $procedure */
        return $procedure->fresh(['nutritionRecommendation', 'recorder']);
    }

    public function closeFieldCase(FieldCase $case, User $vet, ?string $closingNote = null): FieldCase
    {
        $this->authorizeVetForCase($case, $vet);

        if ($case->status !== FieldCaseStatus::Active) {
            abort(422, 'لا يمكن إغلاق هذه الحالة.');
        }

        $case->loadMissing('animal');
        if ($case->animal) {
            $this->animalLifecycle->assertAnimalCanReceiveActions($case->animal);
        }

        $case->update([
            'status' => FieldCaseStatus::CompletedTreatment,
            'closed_at' => now(),
            'closing_note' => $closingNote ?: AnimalLifecycleService::FIELD_CLOSED_MANUAL,
        ]);

        return $case->fresh(['animal', 'opener', 'procedures.nutritionRecommendation']);
    }

    public function approveDischarge(HospitalCase $case, User $vetHead): HospitalCase
    {
        return $this->issueVetHeadDecision($case, $vetHead, 'discharge');
    }

    public function approveSlaughter(HospitalCase $case, User $vetHead): HospitalCase
    {
        return $this->issueVetHeadDecision($case, $vetHead, 'slaughter');
    }

    public function issueVetHeadDecision(
        HospitalCase $case,
        User $vetHead,
        string $decision,
        ?string $note = null,
    ): HospitalCase {
        $case->loadMissing('animal');
        if ($case->animal) {
            $this->animalLifecycle->assertAnimalCanReceiveActions($case->animal);
        }

        if (! in_array($case->status, HospitalCaseStatus::awaitingVetHeadDecision(), true)) {
            abort(422, 'لا يمكن إصدار قرار على هذه الحالة حالياً.');
        }

        match ($decision) {
            'discharge' => (function () use ($case, $vetHead, $note) {
                $case->update([
                    'status' => HospitalCaseStatus::PendingHandover,
                ]);

                $case->load(['animal', 'procedures']);

                $this->receivingTasks->createFromHospitalDecision(
                    $case,
                    $vetHead,
                    ReceivingTaskType::AfterTreatment,
                    $case->procedures,
                    $note ? trim($note) : 'تم اعتماد خروج الحيوان بعد العلاج.',
                );
            })(),
            'slaughter' => (function () use ($case) {
                $case->update([
                    'status' => HospitalCaseStatus::Slaughtered,
                    'closed_at' => now(),
                    'closing_outcome' => AnimalLifecycleService::HOSPITAL_CLOSED_SLAUGHTER,
                ]);

                $case->loadMissing('animal');
                if ($case->animal) {
                    $this->animalLifecycle->finalizeAfterEmergencySlaughter($case->animal, $case);
                }
            })(),
            default => abort(422, 'نوع القرار غير صالح.'),
        };

        return $case->fresh(['animal', 'procedures.nutritionRecommendation']);
    }

    public function resolveCase(string $caseKey, User $vet): FieldCase|HospitalCase
    {
        [$type, $caseNumber] = $this->parseCaseKey($caseKey);

        if ($type === 'field') {
            $case = FieldCase::query()
                ->with(['animal', 'opener', 'procedures.nutritionRecommendation'])
                ->where('case_number', $caseNumber)
                ->where('group', $vet->assigned_group)
                ->first();
        } else {
            $case = HospitalCase::query()
                ->with(['animal', 'admitter', 'healthCase', 'procedures.nutritionRecommendation'])
                ->where('case_number', $caseNumber)
                ->where('group', $vet->assigned_group)
                ->first();
        }

        if (! $case) {
            abort(404, 'الحالة غير موجودة.');
        }

        return $case;
    }

    private function applyHospitalResult(HospitalCase $case, User $vet, MedicalCaseResult $result): void
    {
        match ($result) {
            MedicalCaseResult::ContinueTreatment => $case->update([
                'status' => HospitalCaseStatus::UnderTreatment,
            ]),
            MedicalCaseResult::NoResponse => (function () use ($case, $vet) {
                $case->update(['status' => HospitalCaseStatus::PendingSlaughterApproval]);
                $this->hospitalNotifier->notifyPendingSlaughter($case->fresh(), $vet);
            })(),
            MedicalCaseResult::ReadyForDischarge => (function () use ($case, $vet) {
                $case->update(['status' => HospitalCaseStatus::PendingDischargeApproval]);
                $this->hospitalNotifier->notifyPendingDischarge($case->fresh(), $vet);
            })(),
        };
    }

    private function authorizeVetForCase(Model $case, User $vet): void
    {
        $group = $case->group ?? null;

        if ($group !== $vet->assigned_group) {
            abort(403, 'هذه الحالة لا تخص مجموعتك.');
        }
    }

    private function ensureCaseAcceptsProcedures(Model $case): void
    {
        $case->loadMissing('animal');
        if ($case->animal) {
            $this->animalLifecycle->assertAnimalCanReceiveActions($case->animal);

            $exceptFieldCaseId = $case instanceof FieldCase ? $case->id : null;
            $this->animalLifecycle->assertNoOpenFieldCase($case->animal, $exceptFieldCaseId);
        }

        if ($case instanceof FieldCase && $case->status !== FieldCaseStatus::Active) {
            abort(422, 'لا يمكن تسجيل إجراء على حالة مغلقة.');
        }

        if ($case instanceof HospitalCase) {
            if (in_array($case->status, HospitalCaseStatus::completed(), true)) {
                abort(422, 'لا يمكن تسجيل إجراء على حالة منتهية.');
            }

            if (! in_array($case->status->value, HospitalCaseStatus::visibleToDoctorValues(), true)) {
                abort(422, 'لا يمكن تسجيل إجراء على هذه الحالة حالياً.');
            }
        }
    }

    /** @return array{0: string, 1: string} */
    private function parseCaseKey(string $caseKey): array
    {
        if (! preg_match('/^(field|hospital)-(.+)$/', $caseKey, $matches)) {
            abort(404, 'الحالة غير موجودة.');
        }

        return [$matches[1], $matches[2]];
    }
}
