<?php

namespace App\Enums;

enum HealthCaseFollowUpKind: string
{
    case NeedsReferral = 'needs_referral';
    case NoReferral = 'no_referral';

    public function label(): string
    {
        return match ($this) {
            self::NeedsReferral => 'تحتاج إحالة',
            self::NoReferral => 'لا تحتاج إحالة',
        };
    }
}
