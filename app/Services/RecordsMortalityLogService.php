<?php

namespace App\Services;

use App\Enums\AutopsyReferralStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\MortalityCaseStatus;
use App\Enums\MortalityVictimKind;
use App\Models\MortalityCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RecordsMortalityLogService
{
    /** @return array<string, mixed> */
    public function indexViewData(Request $request, string $portalBase, bool $readOnly = false): array
    {
        $query = MortalityCase::query()
            ->with(['animal', 'autopsyReferral'])
            ->where('victim_kind', MortalityVictimKind::ZooAnimal)
            ->whereDoesntHave('animal.hospitalCases', function (Builder $builder) {
                $builder->where('status', HospitalCaseStatus::Slaughtered->value);
            })
            ->orderByDesc('death_date')
            ->orderByDesc('id');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($autopsy = $request->query('autopsy')) {
            if ($autopsy === 'yes') {
                $query->where(function (Builder $builder) {
                    $builder->where('status', MortalityCaseStatus::ReferredForAutopsy)
                        ->orWhereHas('autopsyReferral');
                });
            } elseif ($autopsy === 'no') {
                $query->where('status', '!=', MortalityCaseStatus::ReferredForAutopsy->value)
                    ->whereDoesntHave('autopsyReferral');
            }
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('case_number', 'like', "%{$search}%")
                    ->orWhere('subject_code', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
                    ->orWhere('death_cause', 'like', "%{$search}%")
                    ->orWhereHas('animal', function (Builder $animalQuery) use ($search) {
                        $animalQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('species', 'like', "%{$search}%");
                    });
            });
        }

        $this->applyDeathDateFilter($query, $request);

        return [
            'cases' => $query->paginate(20)->withQueryString(),
            'portalBase' => $portalBase,
            'readOnly' => $readOnly,
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'autopsy' => $request->query('autopsy', ''),
                'period' => $request->query('period', ''),
                'date' => $request->query('date', ''),
            ],
        ];
    }

    public function hasAutopsy(MortalityCase $case): bool
    {
        return $case->status === MortalityCaseStatus::ReferredForAutopsy
            || $case->autopsyReferral !== null;
    }

    public function finalCauseFor(MortalityCase $case): string
    {
        $autopsy = $case->autopsyReferral;

        if ($autopsy?->status === AutopsyReferralStatus::Documented && $autopsy->final_death_cause) {
            return $autopsy->final_death_cause.' — نتيجة تشريح';
        }

        if ($case->status === MortalityCaseStatus::Approved && $case->death_cause) {
            return $case->death_cause.' — اعتماد مباشر';
        }

        return $case->displayCause();
    }

    public function documentationDateFor(MortalityCase $case): ?string
    {
        if ($case->autopsyReferral?->documented_at) {
            return $case->autopsyReferral->documented_at->format('Y-m-d');
        }

        if ($case->status === MortalityCaseStatus::Approved && $case->reviewed_at) {
            return $case->reviewed_at->format('Y-m-d');
        }

        return null;
    }

    private function applyDeathDateFilter(Builder $query, Request $request): void
    {
        $period = $request->query('period');
        $date = $request->query('date');

        match ($period) {
            'today' => $query->whereDate('death_date', today()),
            'month' => $query->whereMonth('death_date', now()->month)
                ->whereYear('death_date', now()->year),
            'year' => $query->whereYear('death_date', now()->year),
            'custom' => $date ? $query->whereDate('death_date', $date) : null,
            default => null,
        };
    }
}
