<?php

namespace App\Services;

use App\Enums\HospitalCaseStatus;
use App\Models\HospitalCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RecordsSlaughterLogService
{
    /** @return array<string, mixed> */
    public function indexViewData(Request $request, string $portalBase, bool $readOnly = false): array
    {
        $query = HospitalCase::query()
            ->with(['animal', 'admitter', 'procedures.recorder'])
            ->where('status', HospitalCaseStatus::Slaughtered)
            ->orderByDesc('closed_at')
            ->orderByDesc('id');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('case_number', 'like', "%{$search}%")
                    ->orWhere('chief_complaint', 'like', "%{$search}%")
                    ->orWhere('closing_outcome', 'like', "%{$search}%")
                    ->orWhereHas('animal', function (Builder $animalQuery) use ($search) {
                        $animalQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('species', 'like', "%{$search}%");
                    })
                    ->orWhereHas('admitter', fn (Builder $vetQuery) => $vetQuery
                        ->where('name', 'like', "%{$search}%"));
            });
        }

        $this->applyDecisionDateFilter($query, $request);

        return [
            'cases' => $query->paginate(20)->withQueryString(),
            'portalBase' => $portalBase,
            'readOnly' => $readOnly,
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'period' => $request->query('period', ''),
                'date' => $request->query('date', ''),
            ],
        ];
    }

    public function decisionDateFor(HospitalCase $case): ?string
    {
        return $case->closed_at?->format('Y-m-d');
    }

    public function decisionReasonFor(HospitalCase $case): string
    {
        return $case->closing_outcome ?: ($case->chief_complaint ?: '—');
    }

    public function responsibleVetFor(HospitalCase $case): string
    {
        return $case->admitter?->name ?? '—';
    }

    public function approvingHeadFor(HospitalCase $case): string
    {
        return $case->procedures
            ->sortByDesc('recorded_at')
            ->first()
            ?->recorder
            ?->name ?? '—';
    }

    private function applyDecisionDateFilter(Builder $query, Request $request): void
    {
        $period = $request->query('period');
        $date = $request->query('date');

        match ($period) {
            'today' => $query->whereDate('closed_at', today()),
            'month' => $query->whereMonth('closed_at', now()->month)
                ->whereYear('closed_at', now()->year),
            'year' => $query->whereYear('closed_at', now()->year),
            'custom' => $date ? $query->whereDate('closed_at', $date) : null,
            default => null,
        };
    }
}
