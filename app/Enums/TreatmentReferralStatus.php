<?php

namespace App\Enums;

enum TreatmentReferralStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'قيد المراجعة',
            self::Approved => 'معتمدة',
            self::Rejected => 'مرفوضة',
        };
    }
}
