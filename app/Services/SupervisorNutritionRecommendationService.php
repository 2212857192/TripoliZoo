<?php

namespace App\Services;

use App\Models\FieldCase;
use App\Models\HospitalCase;
use App\Models\MedicalNutritionRecommendation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SupervisorNutritionRecommendationService
{
    /** @return Collection<int, MedicalNutritionRecommendation> */
    public function activeForGroup(?string $group): Collection
    {
        if (! $group) {
            return collect();
        }

        return MedicalNutritionRecommendation::query()
            ->with(['procedure.recorder', 'procedure.caseable.animal'])
            ->whereDate('end_date', '>=', now()->toDateString())
            ->whereHas('procedure', function ($procedureQuery) use ($group) {
                $procedureQuery->whereHasMorph(
                    'caseable',
                    [FieldCase::class, HospitalCase::class],
                    fn ($caseQuery) => $caseQuery->where('group', $group),
                );
            })
            ->orderBy('end_date')
            ->orderByDesc('id')
            ->get();
    }

    /** @return list<array<string, mixed>> */
    public function dashboardItemsForGroup(?string $group): array
    {
        return $this->activeForGroup($group)
            ->map(fn (MedicalNutritionRecommendation $nutrition) => $this->toDashboardItem($nutrition))
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    public function toDashboardItem(MedicalNutritionRecommendation $nutrition): array
    {
        $procedure = $nutrition->procedure;
        $case = $procedure?->caseable;
        $animal = $case?->animal;
        $endDate = $nutrition->end_date instanceof Carbon
            ? $nutrition->end_date
            : Carbon::parse($nutrition->end_date);
        $daysRemaining = max(0, (int) now()->startOfDay()->diffInDays($endDate->startOfDay(), false));

        $doctorNote = trim((string) ($nutrition->note ?? ''));
        if ($doctorNote === '' && $procedure?->recorder) {
            $doctorNote = 'د. '.$procedure->recorder->name;
        }

        return [
            'id' => (string) $nutrition->id,
            'animal_id' => $animal?->code ?? '',
            'animal_name' => $animal?->name ?: ($animal?->species ?? '—'),
            'instruction' => $nutrition->recommendation_text,
            'doctor_note' => $doctorNote ?: '—',
            'start_date' => $nutrition->start_date?->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'days_remaining' => $daysRemaining,
        ];
    }
}
