<?php

namespace App\Services;

use App\Enums\AnimalExitType;
use App\Models\AnimalExit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RecordsExitLogService
{
    /** @return array<string, mixed> */
    public function indexViewData(Request $request, string $portalBase, bool $readOnly = false): array
    {
        $query = AnimalExit::query()
            ->with('animal')
            ->orderByDesc('exit_date')
            ->orderByDesc('id');

        if ($group = $request->query('group')) {
            $query->whereHas('animal', fn (Builder $animalQuery) => $animalQuery->where('group', $group));
        }

        if ($exitType = $request->query('exit_type')) {
            $query->where('exit_type', $exitType);
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('recipient', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('animal', function (Builder $animalQuery) use ($search) {
                        $animalQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('species', 'like', "%{$search}%");
                    });
            });
        }

        $this->applyExitDateFilter($query, $request);

        return [
            'exits' => $query->paginate(20)->withQueryString(),
            'portalBase' => $portalBase,
            'readOnly' => $readOnly,
            'exitTypes' => AnimalExitType::options(),
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'exit_type' => $request->query('exit_type', ''),
                'period' => $request->query('period', ''),
                'date' => $request->query('date', ''),
            ],
        ];
    }

    public function exitTypeBadgeClass(AnimalExit $exit): string
    {
        return $exit->exit_type === AnimalExitType::Return ? 'badge-none' : 'badge-completed';
    }

    private function applyExitDateFilter(Builder $query, Request $request): void
    {
        $period = $request->query('period');
        $date = $request->query('date');

        match ($period) {
            'today' => $query->whereDate('exit_date', today()),
            'month' => $query->whereMonth('exit_date', now()->month)
                ->whereYear('exit_date', now()->year),
            'year' => $query->whereYear('exit_date', now()->year),
            'custom' => $date ? $query->whereDate('exit_date', $date) : null,
            default => null,
        };
    }
}
