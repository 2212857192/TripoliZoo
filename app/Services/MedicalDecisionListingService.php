<?php

namespace App\Services;

use App\Enums\HospitalCaseStatus;
use App\Enums\ReceivingTaskSource;
use App\Models\HospitalCase;
use App\Models\ReceivingTask;
use App\Support\MedicalDecisionListItem;
use Illuminate\Support\Collection;

class MedicalDecisionListingService
{
    /** @return Collection<int, MedicalDecisionListItem> */
    public function list(string $portalBase, string $portal = 'care', bool $readOnly = false): Collection
    {
        $items = ReceivingTask::query()
            ->with(['animal', 'supervisor', 'decisionIssuer'])
            ->orderByDesc('decision_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (ReceivingTask $task) => MedicalDecisionListItem::fromReceivingTask(
                $task,
                $this->receivingTaskShowUrl($task, $portalBase),
            ));

        $slaughters = HospitalCase::query()
            ->with(['animal', 'admitter'])
            ->where('status', HospitalCaseStatus::Slaughtered)
            ->orderByDesc('closed_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (HospitalCase $case) => MedicalDecisionListItem::fromSlaughterCase(
                $case,
                $this->slaughterShowUrl($case, $portalBase, $portal, $readOnly),
            ));

        return $items
            ->concat($slaughters)
            ->sortByDesc(fn (MedicalDecisionListItem $item) => $item->sortTimestamp())
            ->values();
    }

    private function receivingTaskShowUrl(ReceivingTask $task, string $portalBase): string
    {
        return $portalBase.'/decisions/'.$task->task_number;
    }

    private function slaughterShowUrl(
        HospitalCase $case,
        string $portalBase,
        string $portal,
        bool $readOnly,
    ): string {
        if ($readOnly) {
            return $portal === 'vet'
                ? '/director/vet/cases/hospital/'.$case->case_number
                : '/director/care/decisions/slaughter/'.$case->case_number;
        }

        if ($portal === 'vet') {
            return $portalBase.'/cases/hospital/'.$case->case_number;
        }

        return $portalBase.'/decisions/slaughter/'.$case->case_number;
    }
}
