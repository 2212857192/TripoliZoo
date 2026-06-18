<?php

namespace App\Services;

use App\Enums\FieldCaseStatus;
use App\Enums\HospitalCaseStatus;
use App\Models\FieldCase;
use App\Models\HospitalCase;
use App\Models\User;
use Illuminate\Support\Collection;

class DoctorMedicalCaseService
{
    /** @return Collection<int, array{sort_at: \Illuminate\Support\Carbon, resource: \App\Http\Resources\DoctorMedicalCaseResource}> */
    public function listForVet(User $vet): Collection
    {
        $group = $vet->assigned_group;

        $fieldCases = FieldCase::query()
            ->with(['animal', 'opener', 'healthReport', 'procedures.nutritionRecommendation', 'procedures.recorder'])
            ->where('group', $group)
            ->orderByDesc('opened_at')
            ->get()
            ->map(fn (FieldCase $case) => [
                'sort_at' => $case->opened_at,
                'resource' => \App\Http\Resources\DoctorMedicalCaseResource::fromFieldCase($case),
            ]);

        $hospitalCases = HospitalCase::query()
            ->with(['animal', 'admitter', 'healthCase', 'procedures.nutritionRecommendation', 'procedures.recorder'])
            ->where('group', $group)
            ->whereIn('status', HospitalCaseStatus::visibleToDoctorValues())
            ->orderByDesc('admitted_at')
            ->get()
            ->map(fn (HospitalCase $case) => [
                'sort_at' => $case->admitted_at,
                'resource' => \App\Http\Resources\DoctorMedicalCaseResource::fromHospitalCase($case),
            ]);

        return $fieldCases
            ->concat($hospitalCases)
            ->sortByDesc('sort_at')
            ->values();
    }

    public function findForVet(User $vet, string $caseKey): ?\App\Http\Resources\DoctorMedicalCaseResource
    {
        [$type, $caseNumber] = $this->parseCaseKey($caseKey);
        $group = $vet->assigned_group;

        if ($type === 'field') {
            $case = FieldCase::query()
                ->with(['animal', 'opener', 'healthReport', 'procedures.nutritionRecommendation', 'procedures.recorder'])
                ->where('case_number', $caseNumber)
                ->where('group', $group)
                ->first();

            return $case ? \App\Http\Resources\DoctorMedicalCaseResource::fromFieldCase($case) : null;
        }

        $case = HospitalCase::query()
            ->with(['animal', 'admitter', 'healthCase', 'procedures.nutritionRecommendation', 'procedures.recorder'])
            ->where('case_number', $caseNumber)
            ->where('group', $group)
            ->whereIn('status', HospitalCaseStatus::visibleToDoctorValues())
            ->first();

        return $case ? \App\Http\Resources\DoctorMedicalCaseResource::fromHospitalCase($case) : null;
    }

    public function activeFieldCount(string $group): int
    {
        return FieldCase::query()
            ->where('group', $group)
            ->where('status', FieldCaseStatus::Active)
            ->count();
    }

    public function activeHospitalCount(string $group): int
    {
        return HospitalCase::query()
            ->where('group', $group)
            ->whereIn('status', HospitalCaseStatus::visibleToDoctorValues())
            ->count();
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
