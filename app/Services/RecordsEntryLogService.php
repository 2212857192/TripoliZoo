<?php

namespace App\Services;

use App\Models\Quarantine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RecordsEntryLogService
{
    /** @return array<string, mixed> */
    public function indexViewData(Request $request, string $portalBase, bool $readOnly = false): array
    {
        $query = Quarantine::query()
            ->passedQuarantine()
            ->with(['animal', 'receivingTask'])
            ->orderByDesc('entry_date')
            ->orderByDesc('id');

        if ($group = $request->query('group')) {
            $query->whereHas('animal', fn (Builder $animalQuery) => $animalQuery->where('group', $group));
        }

        if ($receipt = $request->query('receipt')) {
            if ($receipt === 'yes') {
                $query->whereHas('receivingTask', fn (Builder $taskQuery) => $taskQuery->whereNotNull('received_at'));
            } elseif ($receipt === 'no') {
                $query->where(function (Builder $builder) {
                    $builder->whereDoesntHave('receivingTask')
                        ->orWhereHas('receivingTask', fn (Builder $taskQuery) => $taskQuery->whereNull('received_at'));
                });
            }
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('case_number', 'like', "%{$search}%")
                    ->orWhereHas('animal', function (Builder $animalQuery) use ($search) {
                        $animalQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('species', 'like', "%{$search}%")
                            ->orWhere('group', 'like', "%{$search}%");
                    });
            });
        }

        $this->applyEntryDateFilter($query, $request);

        return [
            'entries' => $query->paginate(20)->withQueryString(),
            'portalBase' => $portalBase,
            'readOnly' => $readOnly,
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'receipt' => $request->query('receipt', ''),
                'period' => $request->query('period', ''),
                'date' => $request->query('date', ''),
            ],
        ];
    }

    public function receiptDateFor(Quarantine $entry): ?string
    {
        return $entry->receivingTask?->received_at?->format('Y-m-d');
    }

    private function applyEntryDateFilter(Builder $query, Request $request): void
    {
        $period = $request->query('period');
        $date = $request->query('date');

        match ($period) {
            'today' => $query->whereDate('entry_date', today()),
            'month' => $query->whereMonth('entry_date', now()->month)
                ->whereYear('entry_date', now()->year),
            'year' => $query->whereYear('entry_date', now()->year),
            'custom' => $date ? $query->whereDate('entry_date', $date) : null,
            default => null,
        };
    }
}
