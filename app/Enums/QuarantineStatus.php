<?php

namespace App\Enums;

enum QuarantineStatus: string
{
    case UnderFollowUp = 'under_followup';
    case HealthReleased = 'health_released';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::UnderFollowUp => 'قيد المتابعة',
            self::HealthReleased => 'تم الإفراج الصحي',
            self::Failed => 'لم تجتز الحجر',
        };
    }

    public function tabId(): string
    {
        return match ($this) {
            self::UnderFollowUp => 'tab-followup',
            self::HealthReleased => 'tab-cleared',
            self::Failed => 'tab-failed',
        };
    }
}
