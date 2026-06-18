<?php

namespace App\Enums;

enum MortalityVictimKind: string
{
    case ZooAnimal = 'zoo_animal';
    case NewbornUnderFollowUp = 'newborn_under_follow_up';

    public function label(): string
    {
        return match ($this) {
            self::ZooAnimal => 'حيوان داخل الحديقة',
            self::NewbornUnderFollowUp => 'مولود قيد المتابعة',
        };
    }
}
