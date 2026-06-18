<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Models\Animal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RecordsBirthLogService
{
    public function __construct(
        private BirthRegistrationService $births,
    ) {}

    /** @return array<string, mixed> */
    public function indexViewData(Request $request, string $portalBase, bool $readOnly = false): array
    {
        $this->births->promoteAllExpired();

        $query = Animal::withQuarantine()
            ->with(['mother', 'birthRegistration.supervisor'])
            ->whereNotNull('birth_registration_id')
            ->orderByDesc('birth_date')
            ->orderByDesc('id');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($status = $request->query('status')) {
            if ($status === 'monitoring') {
                $query->where('status', AnimalStatus::UnderBirthFollowUp->value);
            } elseif ($status === 'completed') {
                $query->where('status', AnimalStatus::Active->value);
            }
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhere('species', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('mother', function (Builder $motherQuery) use ($search) {
                        $motherQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        $this->applyBirthDateFilter($query, $request);

        return [
            'newborns' => $query->paginate(20)->withQueryString(),
            'portalBase' => $portalBase,
            'readOnly' => $readOnly,
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'status' => $request->query('status', ''),
                'period' => $request->query('period', ''),
                'date' => $request->query('date', ''),
            ],
        ];
    }

    public function isMonitoring(Animal $newborn): bool
    {
        return $this->births->isMonitoring($newborn);
    }

    public function followUpCompletionDate(Animal $newborn): ?string
    {
        if ($newborn->status === AnimalStatus::UnderBirthFollowUp->value) {
            return null;
        }

        if ($newborn->status === AnimalStatus::Active->value && $newborn->birth_date) {
            return $newborn->birth_date
                ->copy()
                ->addDays(BirthRegistrationService::FOLLOW_UP_DAYS)
                ->format('Y-m-d');
        }

        return null;
    }

    private function applyBirthDateFilter(Builder $query, Request $request): void
    {
        $period = $request->query('period');
        $date = $request->query('date');

        match ($period) {
            'today' => $query->whereDate('birth_date', today()),
            'month' => $query->whereMonth('birth_date', now()->month)
                ->whereYear('birth_date', now()->year),
            'year' => $query->whereYear('birth_date', now()->year),
            'custom' => $date ? $query->whereDate('birth_date', $date) : null,
            default => null,
        };
    }
}
