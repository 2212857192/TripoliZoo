<?php

namespace App\Services;

use App\Models\Quarantine;

class QuarantineCaseNumberGenerator
{
    public function next(): string
    {
        $year = now()->year;
        $prefix = "QR-{$year}-";

        $lastCase = Quarantine::query()
            ->where('case_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('case_number');

        $sequence = 1;
        if ($lastCase && preg_match('/-(\d+)$/', $lastCase, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
