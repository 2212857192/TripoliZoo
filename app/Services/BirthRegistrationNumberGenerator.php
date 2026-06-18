<?php

namespace App\Services;

use App\Models\BirthRegistration;

class BirthRegistrationNumberGenerator
{
    public function next(): string
    {
        $year = now()->year;
        $prefix = "BR-{$year}-";

        $last = BirthRegistration::query()
            ->where('registration_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('registration_number');

        $sequence = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
