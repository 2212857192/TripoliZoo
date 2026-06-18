<?php

namespace App\Services;

use App\Models\TreatmentReferral;

class TreatmentReferralNumberGenerator
{
    public function next(): string
    {
        $year = now()->year;
        $prefix = "TR-{$year}-";

        $last = TreatmentReferral::query()
            ->where('referral_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('referral_number');

        $sequence = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
