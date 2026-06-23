<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\MortalityVictimKind;
use App\Models\Animal;
use App\Models\AnimalExit;
use App\Models\HospitalCase;
use App\Models\MortalityCase;
use App\Models\Quarantine;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RecordsDashboardService
{
    /** @return array<string, int> */
    public function stats(): array
    {
        return [
            'active_animals' => Animal::query()->insideZooOfficially()->count(),
            'total_profiles' => Animal::withQuarantine()->count(),
            'births' => Animal::withQuarantine()
                ->whereNotNull('birth_registration_id')
                ->count(),
            'stillbirths' => MortalityCase::query()
                ->where('victim_kind', MortalityVictimKind::NewbornUnderFollowUp)
                ->count(),
            'entries' => Quarantine::query()->passedQuarantine()->count(),
            'mortality' => MortalityCase::query()
                ->where('victim_kind', MortalityVictimKind::ZooAnimal)
                ->whereDoesntHave('animal.hospitalCases', function ($builder) {
                    $builder->where('status', HospitalCaseStatus::Slaughtered->value);
                })
                ->count(),
            'slaughter' => HospitalCase::query()
                ->where('status', HospitalCaseStatus::Slaughtered)
                ->count(),
            'exits' => AnimalExit::query()->count(),
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function recentRecords(int $limit = 10, string $portalBase = '/records'): Collection
    {
        $items = collect();

        Animal::withQuarantine()
            ->where('source', 'records')
            ->whereNull('birth_registration_id')
            ->orderByDesc('registered_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->each(function (Animal $animal) use ($items, $portalBase) {
                $items->push($this->recordItem(
                    sortAt: $animal->registered_at ?? $animal->created_at,
                    typeLabel: 'إضافة حيوان',
                    badgeClass: 'badge-success',
                    animal: $animal,
                    portalBase: $portalBase,
                ));
            });

        Animal::withQuarantine()
            ->whereNotNull('birth_registration_id')
            ->orderByDesc('birth_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->each(function (Animal $animal) use ($items, $portalBase) {
                $items->push($this->recordItem(
                    sortAt: $animal->birth_date ?? $animal->registered_at ?? $animal->created_at,
                    typeLabel: 'ولادة',
                    badgeClass: 'badge-primary',
                    animal: $animal,
                    portalBase: $portalBase,
                    sourceTag: $animal->status === AnimalStatus::UnderBirthFollowUp->value
                        ? 'قيد المتابعة'
                        : null,
                ));
            });

        MortalityCase::query()
            ->with('animal')
            ->orderByDesc('death_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->each(function (MortalityCase $case) use ($items, $portalBase) {
                $isStillborn = $case->victim_kind === MortalityVictimKind::NewbornUnderFollowUp;

                $items->push($this->recordItem(
                    sortAt: $case->death_date ?? $case->created_at,
                    typeLabel: $isStillborn ? 'ولادة نافقة' : 'نفوق',
                    badgeClass: $isStillborn ? 'badge-warning' : 'badge-danger',
                    animal: $case->animal,
                    group: $case->group,
                    animalCode: $case->animal?->code ?? $case->subject_code,
                    species: $case->animal?->species ?? $case->subject_type,
                    portalBase: $portalBase,
                    url: $isStillborn
                        ? $portalBase.'/logs/stillbirths'
                        : $portalBase.'/logs/mortality',
                ));
            });

        Quarantine::query()
            ->passedQuarantine()
            ->with('animal')
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->each(function (Quarantine $entry) use ($items, $portalBase) {
                $items->push($this->recordItem(
                    sortAt: $entry->entry_date ?? $entry->created_at,
                    typeLabel: 'دخول حجر صحي',
                    badgeClass: 'badge-success',
                    animal: $entry->animal,
                    group: $entry->animal?->group,
                    portalBase: $portalBase,
                    url: $portalBase.'/logs/entries',
                ));
            });

        AnimalExit::query()
            ->with('animal')
            ->orderByDesc('exit_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->each(function (AnimalExit $exit) use ($items, $portalBase) {
                $items->push($this->recordItem(
                    sortAt: $exit->exit_date ?? $exit->created_at,
                    typeLabel: 'خروج',
                    badgeClass: 'badge-gray',
                    animal: $exit->animal,
                    portalBase: $portalBase,
                    url: $portalBase.'/logs/exits',
                ));
            });

        HospitalCase::query()
            ->with('animal')
            ->where('status', HospitalCaseStatus::Slaughtered)
            ->orderByDesc('closed_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->each(function (HospitalCase $case) use ($items, $portalBase) {
                $items->push($this->recordItem(
                    sortAt: $case->closed_at ?? $case->updated_at,
                    typeLabel: 'ذبح اضطراري',
                    badgeClass: 'badge-danger',
                    animal: $case->animal,
                    group: $case->group,
                    portalBase: $portalBase,
                    url: $portalBase.'/logs/slaughter',
                ));
            });

        return $items
            ->sortByDesc(fn (array $item) => $item['sort_at'] instanceof Carbon ? $item['sort_at']->timestamp : 0)
            ->take($limit)
            ->values()
            ->map(fn (array $item) => collect($item)->except('sort_at')->all());
    }

    /**
     * @return array<string, mixed>
     */
    private function recordItem(
        Carbon|string|null $sortAt,
        string $typeLabel,
        string $badgeClass,
        ?Animal $animal,
        string $portalBase,
        ?string $group = null,
        ?string $animalCode = null,
        ?string $species = null,
        ?string $sourceTag = null,
        ?string $url = null,
    ): array {
        $code = $animalCode ?? $animal?->code;

        return [
            'sort_at' => $sortAt ? Carbon::parse($sortAt) : now(),
            'date' => $sortAt ? Carbon::parse($sortAt)->format('Y-m-d') : '—',
            'type_label' => $typeLabel,
            'badge_class' => $badgeClass,
            'group' => $group ?? $animal?->group ?? '—',
            'animal_name' => $animal?->name,
            'animal_code' => $code,
            'species' => $species ?? $animal?->species,
            'image' => $animal?->displayPhotoUrl(),
            'source_tag' => $sourceTag,
            'url' => $url ?? ($code ? $portalBase.'/animals/'.$code : $portalBase.'/animals'),
        ];
    }
}
