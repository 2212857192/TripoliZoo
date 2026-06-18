<?php

namespace App\Services;

use App\Models\ReceivingTask;

class ReceivingTaskNumberGenerator
{
    public function next(): string
    {
        $year = now()->year;
        $prefix = "RCV-{$year}-";

        $lastTask = ReceivingTask::query()
            ->where('task_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('task_number');

        $sequence = 1;
        if ($lastTask && preg_match('/-(\d+)$/', $lastTask, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
