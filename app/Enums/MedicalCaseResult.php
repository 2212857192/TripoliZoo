<?php

namespace App\Enums;

enum MedicalCaseResult: string
{
    case ContinueTreatment = 'continue_treatment';
    case NoResponse = 'no_response';
    case ReadyForDischarge = 'ready_for_discharge';

    public function label(): string
    {
        return match ($this) {
            self::ContinueTreatment => 'استمرار العلاج',
            self::NoResponse => 'لا يستجيب للعلاج',
            self::ReadyForDischarge => 'جاهز للخروج',
        };
    }
}
