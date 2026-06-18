<?php

namespace App\Enums;

enum UserRole: string
{
    case SystemAdmin = 'مدير النظام';
    case Director = 'مدير الحديقة';
    case GroupSupervisor = 'مشرف المجموعة';
    case Veterinarian = 'الطبيب البيطري';
    case CareHead = 'رئيس قسم الرعاية والتغذية';
    case VetHead = 'رئيس قسم المستشفى البيطري';
    case RecordsOfficer = 'مسؤول السجلات والتوثيق';
    case Visitor = 'زائر';

    public function portal(): ?Portal
    {
        return match ($this) {
            self::SystemAdmin => Portal::Admin,
            self::Director => Portal::Director,
            self::GroupSupervisor, self::CareHead => Portal::Care,
            self::Veterinarian, self::VetHead => Portal::Vet,
            self::RecordsOfficer => Portal::Records,
            self::Visitor => null,
        };
    }

    public function homePath(): string
    {
        return $this->portal()?->dashboardPath() ?? '/login';
    }

    public function appRole(): ?AppRole
    {
        return AppRole::fromUserRole($this);
    }

    public function canUseMobileApp(): bool
    {
        return $this->appRole() !== null;
    }

    public function canUseWebPortal(): bool
    {
        return match ($this) {
            self::Veterinarian, self::Visitor => false,
            default => $this->portal() !== null,
        };
    }

    public function label(): string
    {
        return $this->value;
    }

    public function requiresAssignedGroup(): bool
    {
        return in_array($this, [self::GroupSupervisor, self::Veterinarian], true);
    }

    /** @return list<string> */
    public static function employeeOptions(): array
    {
        return [
            self::Director->value,
            self::GroupSupervisor->value,
            self::Veterinarian->value,
            self::CareHead->value,
            self::VetHead->value,
            self::RecordsOfficer->value,
        ];
    }

    /** @return list<string> */
    public static function allValues(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }
}
