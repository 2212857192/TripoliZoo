<?php

namespace App\Http\Controllers\Care;

use App\Enums\HospitalCaseStatus;
use App\Enums\ReceivingTaskSource;
use App\Http\Controllers\Controller;
use App\Models\HospitalCase;
use App\Models\ReceivingTask;
use App\Services\MedicalDecisionListingService;
use App\Services\MedicalDecisionTreatmentCollector;
use Illuminate\View\View;

class MedicalDecisionController extends Controller
{
    public function index(bool $readOnly = false, ?string $layout = null, string $portal = 'care'): View
    {
        $portalBase = $this->portalBase($readOnly, $portal);
        $decisions = app(MedicalDecisionListingService::class)->list(
            $portalBase,
            $portal,
            $readOnly,
        );

        return view("{$portal}.decisions.index", [
            '__layout' => $layout ?? ($portal === 'vet' ? 'vet.layout' : 'care.layout'),
            'readOnly' => $readOnly,
            'decisions' => $decisions,
            'portalBase' => $portalBase,
            'decisionsShowRoute' => $portal === 'vet' ? 'vet.decisions.show' : 'care.decisions.show',
        ]);
    }

    public function show(
        ReceivingTask $receivingTask,
        bool $readOnly = false,
        ?string $layout = null,
        string $portal = 'care'
    ): View {
        $receivingTask->load(['animal', 'supervisor', 'decisionIssuer', 'quarantine.vaccines']);
        $treatmentCollector = app(MedicalDecisionTreatmentCollector::class);

        return view('care.decisions.show', [
            '__layout' => $layout ?? ($portal === 'vet' ? 'vet.layout' : 'care.layout'),
            'readOnly' => $readOnly,
            'decisionKind' => 'receiving',
            'task' => $receivingTask,
            'id' => $receivingTask->task_number,
            'portalBase' => $this->portalBase($readOnly, $portal),
            'resolvedTreatments' => $this->resolvedTreatments($receivingTask, $treatmentCollector),
        ]);
    }

    public function showSlaughter(
        HospitalCase $hospitalCase,
        bool $readOnly = false,
        ?string $layout = null,
        string $portal = 'care',
    ): View {
        abort_unless($hospitalCase->status === HospitalCaseStatus::Slaughtered, 404);

        $hospitalCase->load(['animal', 'admitter', 'procedures.recorder']);
        $treatmentCollector = app(MedicalDecisionTreatmentCollector::class);

        return view('care.decisions.show', [
            '__layout' => $layout ?? 'care.layout',
            'readOnly' => $readOnly,
            'decisionKind' => 'slaughter',
            'slaughterCase' => $hospitalCase,
            'id' => $hospitalCase->case_number,
            'portalBase' => $this->portalBase($readOnly, $portal),
            'resolvedTreatments' => $treatmentCollector->fromFollowUps($hospitalCase->procedures),
        ]);
    }

    /** @return list<string> */
    private function resolvedTreatments(ReceivingTask $task, MedicalDecisionTreatmentCollector $collector): array
    {
        $stored = array_values(array_filter($task->decision_treatments ?? []));

        if ($stored !== []) {
            return $stored;
        }

        if ($task->quarantine) {
            return $collector->fromQuarantine($task->quarantine);
        }

        if ($task->source === ReceivingTaskSource::Hospital && $task->animal_id) {
            $case = HospitalCase::query()
                ->with('procedures')
                ->where('animal_id', $task->animal_id)
                ->whereIn('status', [
                    HospitalCaseStatus::PendingHandover,
                    HospitalCaseStatus::HandoverDelayed,
                    HospitalCaseStatus::ReadyForDischarge,
                    HospitalCaseStatus::Slaughtered,
                ])
                ->orderByDesc('id')
                ->first();

            if ($case) {
                return $collector->fromFollowUps($case->procedures);
            }
        }

        return [];
    }

    private function portalBase(bool $readOnly, string $portal): string
    {
        if ($readOnly) {
            return $portal === 'vet' ? '/director/vet' : '/director/care';
        }

        return $portal === 'vet' ? '/vet' : '/care';
    }
}
