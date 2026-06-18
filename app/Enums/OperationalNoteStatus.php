<?php

namespace App\Enums;

enum OperationalNoteStatus: string
{
    case New = 'new';
    case Reviewed = 'reviewed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'جديدة',
            self::Reviewed => 'تمت المراجعة',
        };
    }
}
