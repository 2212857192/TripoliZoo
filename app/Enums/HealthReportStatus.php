<?php

namespace App\Enums;

enum HealthReportStatus: string
{
    case Sent = 'sent';
    case Received = 'received';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Sent => 'مُرسَل',
            self::Received => 'مُستلَم',
            self::Closed => 'مُغلَق',
        };
    }
}
