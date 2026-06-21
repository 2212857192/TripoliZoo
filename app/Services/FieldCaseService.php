<?php

namespace App\Services;

use App\Enums\FieldCaseStatus;
use App\Models\Animal;
use App\Models\FieldCase;
use App\Models\HealthReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FieldCaseService
{
    public function __construct(
        private FieldCaseNumberGenerator $numbers,
        private AnimalLifecycleService $animalLifecycle,
    ) {}

    public function openManually(
        User $vet,
        Animal $animal,
        string $openReason,
        ?string $initialNote = null,
    ): FieldCase {
        $this->animalLifecycle->assertAnimalCanReceiveActions($animal);
        $this->animalLifecycle->assertNoOpenFieldCase($animal);

        $fieldCase = null;

        DB::transaction(function () use ($vet, $animal, $openReason, $initialNote, &$fieldCase) {
            $fieldCase = FieldCase::create([
                'case_number' => $this->numbers->next(),
                'animal_id' => $animal->id,
                'group' => $animal->group,
                'open_reason' => $openReason,
                'initial_note' => $initialNote,
                'status' => FieldCaseStatus::Active,
                'opened_by' => $vet->id,
                'opened_at' => now(),
            ]);
        });

        return $fieldCase->fresh(['animal', 'opener']);
    }

    public function createFromHealthReport(HealthReport $report, User $vet): ?FieldCase
    {
        if ($report->fieldCase) {
            return $report->fieldCase;
        }

        $report->loadMissing('animal');

        if ($report->animal) {
            $this->animalLifecycle->assertAnimalCanReceiveActions($report->animal);
            $this->animalLifecycle->assertNoOpenFieldCase($report->animal);
        }

        $fieldCase = null;

        DB::transaction(function () use ($report, $vet, &$fieldCase) {
            $fieldCase = FieldCase::create([
                'case_number' => $this->numbers->next(),
                'animal_id' => $report->animal_id,
                'group' => $report->group,
                'open_reason' => $report->description,
                'initial_note' => $report->doctor_note,
                'status' => FieldCaseStatus::Active,
                'opened_by' => $vet->id,
                'health_report_id' => $report->id,
                'opened_at' => now(),
            ]);
        });

        return $fieldCase->fresh(['animal', 'opener', 'healthReport']);
    }

    /** @return array<string, mixed> */
    public function indexViewData(Request $request, string $portalBase, bool $readOnly = false): array
    {
        $query = FieldCase::query()
            ->with(['animal.profile', 'opener', 'procedures'])
            ->orderByDesc('opened_at');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('case_number', 'like', "%{$search}%")
                    ->orWhere('open_reason', 'like', "%{$search}%")
                    ->orWhereHas('animal', function ($animalQuery) use ($search) {
                        $animalQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('species', 'like', "%{$search}%");
                    });
            });
        }

        if ($date = $request->query('date')) {
            $query->whereDate('opened_at', $date);
        }

        $cases = $query->get();

        return [
            'portalBase' => $portalBase,
            'vetBase' => $portalBase,
            'readOnly' => $readOnly,
            'activeCases' => $cases
                ->filter(fn (FieldCase $case) => $case->status === FieldCaseStatus::Active)
                ->values(),
            'closedCases' => $cases
                ->filter(fn (FieldCase $case) => ! $case->status->isActive())
                ->values(),
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'date' => $request->query('date', ''),
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function showViewData(FieldCase $fieldCase): array
    {
        $fieldCase->load([
            'animal',
            'opener',
            'healthReport',
            'procedures.nutritionRecommendation',
            'procedures.recorder',
        ]);

        $animal = $fieldCase->animal;
        $lastProcedureAt = $fieldCase->procedures->max('recorded_at');
        $lastUpdate = $lastProcedureAt instanceof Carbon
            ? $lastProcedureAt
            : ($fieldCase->closed_at ?? $fieldCase->opened_at);

        $followUps = $fieldCase->procedures
            ->sortByDesc('recorded_at')
            ->values()
            ->map(function ($procedure) {
                $nutrition = $procedure->nutritionRecommendation;

                return [
                    'date' => $procedure->recorded_at?->format('Y-m-d — H:i') ?? '—',
                    'vet' => $procedure->recorder?->name ?? '—',
                    'diagnosis' => $procedure->diagnosis,
                    'treatment' => $procedure->treatment,
                    'note' => $procedure->note ?? '',
                    'nutrition' => $nutrition ? [
                        'text' => $nutrition->recommendation_text,
                        'start' => $nutrition->start_date?->format('Y-m-d'),
                        'end' => $nutrition->end_date?->format('Y-m-d'),
                    ] : null,
                ];
            })
            ->all();

        return [
            'id' => $fieldCase->case_number,
            'fieldCase' => $fieldCase,
            'caseData' => [
                'status' => $fieldCase->status->value,
                'statusClass' => $fieldCase->status->headerStatusClass(),
                'statusText' => $fieldCase->status->label(),
                'openDate' => $fieldCase->opened_at?->format('Y-m-d') ?? '—',
                'lastUpdate' => $lastUpdate?->format('Y-m-d') ?? '—',
                'closeDate' => $fieldCase->closed_at?->format('Y-m-d') ?? '',
                'vet' => $fieldCase->opener?->name ?? '—',
                'reason' => $fieldCase->open_reason,
                'initialNote' => $fieldCase->initial_note ?? '',
                'animalId' => $animal?->code ? '#'.$animal->code : '—',
                'animalType' => $animal?->species ?? '—',
                'animalName' => $animal?->name ?? '',
                'mark' => $animal?->distinguishing_marks ?? '',
                'animalEmoji' => '🐾',
                'animalPhotoUrl' => $animal?->displayPhotoUrl(),
                'group' => $fieldCase->group,
                'followUps' => $followUps,
            ],
        ];
    }

    public function lastUpdatedAt(FieldCase $case): ?Carbon
    {
        $case->loadMissing('procedures');

        $lastProcedureAt = $case->procedures->max('recorded_at');

        if ($lastProcedureAt instanceof Carbon) {
            return $lastProcedureAt;
        }

        return $case->closed_at ?? $case->opened_at;
    }
}
