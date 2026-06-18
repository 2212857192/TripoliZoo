<?php

namespace App\Enums;

enum AutopsyReferralStatus: string
{
    case Pending = 'pending';
    case Documented = 'documented';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'بانتظار التوثيق',
            self::Documented => 'موثقة',
        };
    }
}
