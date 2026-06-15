<?php

namespace App\Enums;

enum AppRole: string
{
    case Visitor = 'visitor';
    case Supervisor = 'supervisor';
    case Doctor = 'doctor';

    public static function fromUserRole(?UserRole $role): ?self
    {
        return match ($role) {
            UserRole::Visitor => self::Visitor,
            UserRole::GroupSupervisor => self::Supervisor,
            UserRole::Veterinarian => self::Doctor,
            default => null,
        };
    }
}
