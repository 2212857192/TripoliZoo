<?php

namespace App\Enums;

enum FieldCaseStatus: string
{
    case Active = 'active';
    case Closed = 'closed';
    case CompletedTreatment = 'completed_treatment';
    case ReferredToHospital = 'referred_to_hospital';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'قيد المتابعة',
            self::Closed => 'مغلقة',
            self::CompletedTreatment => 'منتهية – أُغلقت بعد اكتمال العلاج الميداني.',
            self::ReferredToHospital => 'محالة إلى مستشفى',
        };
    }

    public function listBadgeClass(): string
    {
        return match ($this) {
            self::Active => 'badge-followup',
            self::Closed => 'badge-no-followup',
            self::CompletedTreatment => 'badge-no-followup',
            self::ReferredToHospital => 'badge-referred',
        };
    }

    public function headerStatusClass(): string
    {
        return match ($this) {
            self::Active => 'status-open',
            self::Closed => 'status-closed',
            self::CompletedTreatment => 'status-closed',
            self::ReferredToHospital => 'status-referred',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
