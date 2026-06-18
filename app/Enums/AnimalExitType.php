<?php

namespace App\Enums;

enum AnimalExitType: string
{
    case Sale = 'sale';
    case Transfer = 'transfer';
    case Swap = 'swap';
    case Gift = 'gift';
    case Handover = 'handover';
    case Return = 'return';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'بيع',
            self::Transfer => 'نقل',
            self::Swap => 'مقايضة',
            self::Gift => 'إهداء',
            self::Handover => 'تسليم لجهة خارجية',
            self::Return => 'إرجاع',
            self::Other => 'أخرى',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
