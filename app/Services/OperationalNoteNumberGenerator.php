<?php

namespace App\Services;

use App\Models\OperationalNote;

class OperationalNoteNumberGenerator
{
    public function next(): string
    {
        $year = now()->year;
        $prefix = "ON-{$year}-";

        $last = OperationalNote::query()
            ->where('note_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('note_number');

        $sequence = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
