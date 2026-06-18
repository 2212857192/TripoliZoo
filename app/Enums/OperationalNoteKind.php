<?php

namespace App\Enums;

enum OperationalNoteKind: string
{
    case Feeding = 'feeding';
    case General = 'general';

    public function label(): string
    {
        return match ($this) {
            self::Feeding => 'تغذية',
            self::General => 'ملاحظة عامة',
        };
    }
}
