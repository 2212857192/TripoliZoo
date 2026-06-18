<?php

namespace App\Enums;

enum FieldCaseStatus: string
{
    case Active = 'active';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'قيد المتابعة',
            self::Closed => 'مغلقة',
        };
    }

    public function listBadgeClass(): string
    {
        return match ($this) {
            self::Active => 'badge-followup',
            self::Closed => 'badge-no-followup',
        };
    }

    public function headerStatusClass(): string
    {
        return match ($this) {
            self::Active => 'status-open',
            self::Closed => 'status-closed',
        };
    }
}
