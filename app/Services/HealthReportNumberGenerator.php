<?php

namespace App\Services;

use App\Models\HealthReport;

class HealthReportNumberGenerator
{
    public function next(): string
    {
        $year = now()->year;
        $prefix = "RP-{$year}-";

        $last = HealthReport::query()
            ->where('report_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('report_number');

        $sequence = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
