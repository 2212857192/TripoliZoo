<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Enums\ReceivingTaskSource;
use App\Enums\ReceivingTaskStatus;
use App\Enums\ReceivingTaskType;
use App\Enums\UserRole;
use App\Enums\HospitalCaseStatus;
use App\Models\Animal;
use App\Models\HospitalCase;
use App\Models\Quarantine;
use App\Models\ReceivingTask;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReceivingTaskService
{
    public function __construct(
        private ReceivingTaskNumberGenerator $numbers,
        private SupervisorNotificationService $supervisorNotifier,
        private CareNotificationService $careNotifier,
        private VetNotificationService $vetNotifier,
        private MedicalDecisionTreatmentCollector $treatmentCollector,
        private AnimalLifecycleService $animalLifecycle,
    ) {}

    public function createFromQuarantineRelease(Quarantine $quarantine, User $issuer): ?ReceivingTask
    {
        $quarantine->loadMissing('animal');
        $animal = $quarantine->animal;
        $group = $animal->group;

        $supervisor = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::GroupSupervisor->value)
            ->where('assigned_group', $group)
            ->first();

        if (! $supervisor) {
            return null;
        }

        $task = null;

        $treatments = $this->treatmentCollector->fromQuarantine($quarantine);

        DB::transaction(function () use ($quarantine, $animal, $supervisor, $issuer, $treatments, &$task) {
            Animal::withQuarantine()
                ->whereKey($animal->id)
                ->update(['status' => AnimalStatus::PendingReceipt->value]);

            $task = ReceivingTask::create([
                'task_number' => $this->numbers->next(),
                'animal_id' => $animal->id,
                'quarantine_id' => $quarantine->id,
                'supervisor_id' => $supervisor->id,
                'status' => ReceivingTaskStatus::Pending,
                'task_type' => ReceivingTaskType::AfterHealthRelease,
                'source' => ReceivingTaskSource::Quarantine,
                'decision_date' => now()->toDateString(),
                'decision_issued_by' => $issuer->id,
                'decision_notes' => 'اكتملت فترة الحجر الصحي وصدر قرار الإفراج.',
                'decision_treatments' => $treatments ?: null,
            ]);
        });

        if ($task) {
            $fresh = $task->fresh(['animal', 'decisionIssuer', 'supervisor']);
            $this->supervisorNotifier->notifyReceivingTask($fresh);
            $this->careNotifier->notifyMedicalDecision($fresh);
            $this->vetNotifier->notifyMedicalDecision($fresh);
        }

        return $task;
    }

    /**
     * @param  iterable<int, array{treatment?: string|null}|object>  $followUps
     */
    public function createFromHospitalDecision(
        HospitalCase $hospitalCase,
        User $issuer,
        ReceivingTaskType $taskType,
        iterable $followUps,
        ?string $decisionNotes = null,
    ): ?ReceivingTask {
        $hospitalCase->loadMissing('animal');
        $animal = $hospitalCase->animal;

        if (! $animal) {
            return null;
        }

        $supervisor = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::GroupSupervisor->value)
            ->where('assigned_group', $animal->group)
            ->first();

        if (! $supervisor) {
            return null;
        }

        $treatments = $this->treatmentCollector->fromFollowUps($followUps);
        $task = null;

        DB::transaction(function () use ($hospitalCase, $animal, $issuer, $supervisor, $taskType, $decisionNotes, $treatments, &$task) {
            Animal::withQuarantine()
                ->whereKey($animal->id)
                ->update(['status' => AnimalStatus::PendingReceipt->value]);

            $task = ReceivingTask::create([
                'task_number' => $this->numbers->next(),
                'animal_id' => $animal->id,
                'hospital_case_id' => $hospitalCase->id,
                'supervisor_id' => $supervisor->id,
                'status' => ReceivingTaskStatus::Pending,
                'task_type' => $taskType,
                'source' => ReceivingTaskSource::Hospital,
                'decision_date' => now()->toDateString(),
                'decision_issued_by' => $issuer->id,
                'decision_notes' => $decisionNotes,
                'decision_treatments' => $treatments ?: null,
            ]);
        });

        if ($task) {
            $fresh = $task->fresh(['animal', 'decisionIssuer', 'supervisor']);
            $this->supervisorNotifier->notifyReceivingTask($fresh);
            $this->careNotifier->notifyMedicalDecision($fresh);
            $this->vetNotifier->notifyMedicalDecision($fresh);
        }

        return $task;
    }

    public function collectTreatmentsFromFollowUps(iterable $followUps): array
    {
        return $this->treatmentCollector->fromFollowUps($followUps);
    }

    public function confirmReceipt(ReceivingTask $task, User $supervisor, ?string $note = null): void
    {
        DB::transaction(function () use ($task, $note) {
            $task->update([
                'status' => ReceivingTaskStatus::Received,
                'receipt_note' => $note,
                'received_at' => now(),
                'delay_reason' => null,
                'delay_extra_note' => null,
                'delay_recorded_at' => null,
            ]);

            Animal::withQuarantine()
                ->whereKey($task->animal_id)
                ->update(['status' => AnimalStatus::Active->value]);

            $this->finalizeHospitalCaseAfterReceipt($task, $note);
        });

        $fresh = $task->fresh(['animal', 'supervisor', 'decisionIssuer']);
        $this->careNotifier->notifyReceivingCompleted($fresh);
        $this->vetNotifier->notifyReceivingCompleted($fresh);
    }

    public function recordTemporaryDelay(ReceivingTask $task, string $reason, ?string $extraNote = null): void
    {
        DB::transaction(function () use ($task, $reason, $extraNote) {
            $task->update([
                'status' => ReceivingTaskStatus::TemporarilyUnable,
                'delay_reason' => $reason,
                'delay_extra_note' => $extraNote,
                'delay_recorded_at' => now(),
            ]);

            $this->markHospitalCaseHandoverDelayed($task);
        });

        $fresh = $task->fresh(['animal', 'supervisor', 'decisionIssuer']);
        $this->careNotifier->notifyReceivingDelay($fresh);
        $this->vetNotifier->notifyReceivingDelay($fresh);
    }

    private function finalizeHospitalCaseAfterReceipt(ReceivingTask $task, ?string $note): void
    {
        if ($task->source !== ReceivingTaskSource::Hospital) {
            return;
        }

        $outcome = $note !== null && trim($note) !== ''
            ? trim($note)
            : 'تم استلام الحيوان في المجموعة.';

        $case = $this->resolveLinkedHospitalCase($task);

        if (! $case) {
            return;
        }

        $case->update([
            'status' => HospitalCaseStatus::Discharged,
            'closed_at' => $task->received_at ?? now(),
            'closing_outcome' => $outcome,
        ]);

        $case->loadMissing('animal');
        if ($case->animal) {
            $this->animalLifecycle->finalizeAfterHospitalDischarge($case->animal, $case);
        }
    }

    private function markHospitalCaseHandoverDelayed(ReceivingTask $task): void
    {
        if ($task->source !== ReceivingTaskSource::Hospital) {
            return;
        }

        $case = $this->resolveLinkedHospitalCase($task);

        if (! $case || $case->status !== HospitalCaseStatus::PendingHandover) {
            return;
        }

        $case->update(['status' => HospitalCaseStatus::HandoverDelayed]);
    }

    private function resolveLinkedHospitalCase(ReceivingTask $task): ?HospitalCase
    {
        if ($task->hospital_case_id) {
            $case = HospitalCase::query()->find($task->hospital_case_id);

            if ($case && in_array($case->status, $this->openHospitalHandoverStatusEnums(), true)) {
                return $case;
            }
        }

        return HospitalCase::query()
            ->where('animal_id', $task->animal_id)
            ->whereIn('status', $this->openHospitalHandoverStatuses())
            ->orderByDesc('id')
            ->first();
    }

    /** @return list<HospitalCaseStatus> */
    private function openHospitalHandoverStatusEnums(): array
    {
        return [
            HospitalCaseStatus::PendingHandover,
            HospitalCaseStatus::HandoverDelayed,
            HospitalCaseStatus::ReadyForDischarge,
        ];
    }

    /** @return list<string> */
    private function openHospitalHandoverStatuses(): array
    {
        return array_map(
            fn (HospitalCaseStatus $status) => $status->value,
            [
                HospitalCaseStatus::PendingHandover,
                HospitalCaseStatus::HandoverDelayed,
                HospitalCaseStatus::ReadyForDischarge,
            ],
        );
    }
}
