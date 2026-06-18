<?php

namespace App\Services;

use App\Enums\ReceivingTaskStatus;
use App\Models\ReceivingTask;
use Illuminate\Support\Collection;

class PortalDashboardService
{
    /** @return Collection<int, ReceivingTask> */
    public function recentReceivingDelays(int $limit = 5): Collection
    {
        return ReceivingTask::query()
            ->with(['animal', 'supervisor'])
            ->where('status', ReceivingTaskStatus::TemporarilyUnable)
            ->whereNotNull('delay_recorded_at')
            ->orderByDesc('delay_recorded_at')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, ReceivingTask> */
    public function recentMedicalDecisions(int $limit = 5): Collection
    {
        return ReceivingTask::query()
            ->with(['animal'])
            ->orderByDesc('decision_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
