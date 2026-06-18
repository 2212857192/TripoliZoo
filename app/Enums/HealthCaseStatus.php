<?php

namespace App\Enums;

enum HealthCaseStatus: string
{
    case New = 'new';
    case Reviewed = 'reviewed';
    case Referred = 'referred';

    public function label(): string
    {
        return match ($this) {
            self::New => 'جديدة',
            self::Reviewed => 'تمت المراجعة',
            self::Referred => 'محالة للعلاج',
        };
    }
}
