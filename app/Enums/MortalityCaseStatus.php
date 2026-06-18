<?php

namespace App\Enums;

enum MortalityCaseStatus: string
{
    case New = 'new';
    case Approved = 'approved';
    case ReferredForAutopsy = 'referred_for_autopsy';

    public function label(): string
    {
        return match ($this) {
            self::New => 'جديدة',
            self::Approved => 'معتمدة',
            self::ReferredForAutopsy => 'محالة للتشريح',
        };
    }
}
