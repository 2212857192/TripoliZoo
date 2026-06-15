<?php

namespace App\Enums;

enum Portal: string
{
    case Admin = 'admin';
    case Director = 'director';
    case Care = 'care';
    case Vet = 'vet';
    case Records = 'records';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'إدارة النظام',
            self::Director => 'مدير الحديقة',
            self::Care => 'الرعاية والتغذية',
            self::Vet => 'المستشفى البيطري',
            self::Records => 'السجلات والتوثيق',
        };
    }

    /** @return list<UserRole> */
    public function roles(): array
    {
        return match ($this) {
            self::Admin => [UserRole::SystemAdmin],
            self::Director => [UserRole::Director],
            self::Care => [UserRole::GroupSupervisor, UserRole::CareHead],
            self::Vet => [UserRole::Veterinarian, UserRole::VetHead],
            self::Records => [UserRole::RecordsOfficer],
        };
    }

    /** @return list<string> */
    public function roleValues(): array
    {
        return array_map(fn (UserRole $role) => $role->value, $this->roles());
    }

    public function dashboardPath(): string
    {
        return match ($this) {
            self::Admin => '/admin/dashboard',
            self::Director => '/director/dashboard',
            self::Care => '/care/dashboard',
            self::Vet => '/vet/dashboard',
            self::Records => '/records/dashboard',
        };
    }

    public static function tryFromRole(?UserRole $role): ?self
    {
        if (! $role) {
            return null;
        }

        foreach (self::cases() as $portal) {
            if (in_array($role, $portal->roles(), true)) {
                return $portal;
            }
        }

        return null;
    }
}
