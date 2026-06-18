<?php

namespace App\Enums;

enum ReceivingTaskStatus: string
{
    case Pending = 'pending';
    case TemporarilyUnable = 'temporarily_unable';
    case Received = 'received';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'بانتظار الاستلام',
            self::TemporarilyUnable => 'تعذر الاستلام مؤقتًا',
            self::Received => 'تم الاستلام',
        };
    }

    public function careStatusKey(): string
    {
        return match ($this) {
            self::Pending => 'pending',
            self::Received => 'received',
            self::TemporarilyUnable => 'failed',
        };
    }

    public function careStatusBadgeClass(): string
    {
        return match ($this) {
            self::Pending => 'status-pending',
            self::Received => 'status-received',
            self::TemporarilyUnable => 'status-failed',
        };
    }
}
