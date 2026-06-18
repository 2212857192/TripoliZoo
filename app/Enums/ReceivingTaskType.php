<?php

namespace App\Enums;

enum ReceivingTaskType: string
{
    case AfterHealthRelease = 'after_health_release';
    case AfterTreatment = 'after_treatment';

    public function label(): string
    {
        return match ($this) {
            self::AfterHealthRelease => 'استلام بعد إفراج صحي',
            self::AfterTreatment => 'استلام بعد علاج',
        };
    }

    public function careDecisionLabel(): string
    {
        return match ($this) {
            self::AfterHealthRelease => 'إفراج صحي',
            self::AfterTreatment => 'خروج بعد العلاج',
        };
    }

    public function careDecisionTypeKey(): string
    {
        return match ($this) {
            self::AfterHealthRelease => 'release',
            self::AfterTreatment => 'discharge',
        };
    }
}
