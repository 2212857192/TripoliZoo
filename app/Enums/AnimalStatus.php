<?php

namespace App\Enums;

enum AnimalStatus: string
{
    case Active = 'active';
    case PendingReceipt = 'pending_receipt';
    case Quarantine = 'quarantine';
    case UnderBirthFollowUp = 'under_birth_follow_up';
    case Dead = 'dead';
    case Exited = 'exited';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'نشط',
            self::PendingReceipt => 'بانتظار الاستلام',
            self::Quarantine => 'تحت الحجر الصحي',
            self::UnderBirthFollowUp => 'مولود قيد المتابعة',
            self::Dead => 'نافق',
            self::Exited => 'خارج من الحديقة',
        };
    }

    /** حالات الحيوان المتاحة لمشرف المجموعة والطبيب في القوائم العامة. */
    public static function groupOperationalValues(): array
    {
        return [
            self::Active->value,
            self::UnderBirthFollowUp->value,
        ];
    }

    /** حالات الحيوان الرسمي داخل الحديقة في بوابة السجلات. */
    public static function recordsListValues(): array
    {
        return [
            self::Active->value,
            self::UnderBirthFollowUp->value,
        ];
    }
}
