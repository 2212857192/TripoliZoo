<?php

namespace App\Services;

use App\Models\HospitalCase;

class HospitalCaseNumberGenerator
{
    public function next(): string
    {
        $year = now()->year;
        $prefix = "VH-{$year}-";

        $last = HospitalCase::query()
            ->where('case_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('case_number');

        $sequence = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
