<?php

namespace App\Services;

use App\Enums\HospitalCaseStatus;
use App\Models\HospitalCase;
use App\Models\TreatmentReferral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HospitalCaseService
{
    public function __construct(
        private HospitalCaseNumberGenerator $numbers,
        private AnimalLifecycleService $animalLifecycle,
    ) {}

    public function createFromReferral(TreatmentReferral $referral, User $vetHead): HospitalCase
    {
        $referral->loadMissing(['healthCase', 'animal']);

        if ($referral->animal) {
            $this->animalLifecycle->assertAnimalCanReceiveActions($referral->animal);
        }

        if ($referral->hospitalCase) {
            return $referral->hospitalCase;
        }

        $hospitalCase = null;

        DB::transaction(function () use ($referral, $vetHead, &$hospitalCase) {
            $hospitalCase = HospitalCase::create([
                'case_number' => $this->numbers->next(),
                'treatment_referral_id' => $referral->id,
                'health_case_id' => $referral->health_case_id,
                'animal_id' => $referral->animal_id,
                'group' => $referral->group,
                'chief_complaint' => $referral->healthCase?->description ?? '—',
                'status' => HospitalCaseStatus::UnderTreatment,
                'admitted_by' => $vetHead->id,
                'admitted_at' => now(),
            ]);

            if ($referral->animal) {
                $this->animalLifecycle->referActiveFieldCasesToHospital($referral->animal, $hospitalCase);
            }
        });

        /** @var HospitalCase $hospitalCase */
        return $hospitalCase->fresh(['animal', 'healthCase', 'treatmentReferral', 'admitter']);
    }

    /** @return array<string, mixed> */
    public function indexViewData(Request $request, string $portalBase, bool $readOnly = false): array
    {
        $query = HospitalCase::query()
            ->with(['animal', 'admitter', 'treatmentReferral'])
            ->orderByDesc('admitted_at');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($status = $request->query('status')) {
            if (in_array($status, array_column(HospitalCaseStatus::cases(), 'value'), true)) {
                $query->where('status', $status);
            }
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('case_number', 'like', "%{$search}%")
                    ->orWhere('chief_complaint', 'like', "%{$search}%")
                    ->orWhereHas('animal', function ($animalQuery) use ($search) {
                        $animalQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('species', 'like', "%{$search}%");
                    });
            });
        }

        $cases = $query->get();

        return [
            'portalBase' => $portalBase,
            'readOnly' => $readOnly,
            'activeCases' => $cases->filter(fn (HospitalCase $case) => in_array($case->status, HospitalCaseStatus::active(), true))->values(),
            'pendingHandoverCases' => $cases->filter(fn (HospitalCase $case) => in_array($case->status, HospitalCaseStatus::pendingHandover(), true))->values(),
            'completedCases' => $cases->filter(fn (HospitalCase $case) => in_array($case->status, HospitalCaseStatus::completed(), true))->values(),
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'status' => $request->query('status', ''),
            ],
        ];
    }
}
