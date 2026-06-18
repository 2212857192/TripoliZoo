<?php

namespace App\Enums;

enum ReceivingTaskSource: string
{
    case Quarantine = 'quarantine';
    case Hospital = 'hospital';

    public function label(): string
    {
        return match ($this) {
            self::Quarantine => 'الحجر الصحي',
            self::Hospital => 'المستشفى',
        };
    }

    public function fromLabel(): string
    {
        return match ($this) {
            self::Quarantine => 'من الحجر الصحي',
            self::Hospital => 'من المستشفى',
        };
    }
}
