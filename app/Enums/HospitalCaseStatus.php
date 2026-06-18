<?php

namespace App\Enums;

enum HospitalCaseStatus: string
{
    case UnderTreatment = 'under_treatment';
    case ReadyForDischarge = 'ready_for_discharge';
    case NoResponse = 'no_response';
    case PendingDischargeApproval = 'pending_discharge_approval';
    case PendingSlaughterApproval = 'pending_slaughter_approval';
    case PendingHandover = 'pending_handover';
    case HandoverDelayed = 'handover_delayed';
    case Discharged = 'discharged';
    case Slaughtered = 'slaughtered';

    public function label(): string
    {
        return match ($this) {
            self::UnderTreatment => 'قيد العلاج',
            self::ReadyForDischarge => 'جاهز للخروج',
            self::NoResponse => 'لا يستجيب للعلاج',
            self::PendingDischargeApproval => 'بانتظار اعتماد الخروج',
            self::PendingSlaughterApproval => 'بانتظار اعتماد الذبح',
            self::PendingHandover => 'بانتظار الاستلام',
            self::HandoverDelayed => 'تعذر الاستلام مؤقتاً',
            self::Discharged => 'خروج بعد العلاج',
            self::Slaughtered => 'ذبح اضطراري',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ReadyForDischarge => 'badge-ready',
            self::UnderTreatment => 'badge-watch',
            self::NoResponse => 'badge-no-response',
            self::PendingDischargeApproval => 'badge-handover',
            self::PendingSlaughterApproval => 'badge-unavailable',
            self::PendingHandover => 'badge-handover',
            self::HandoverDelayed => 'badge-unavailable',
            self::Discharged, self::Slaughtered => 'badge-received',
        };
    }

    public function headerStatusClass(): string
    {
        return match ($this) {
            self::ReadyForDischarge => 'status-ready',
            self::UnderTreatment => 'status-watch',
            self::NoResponse => 'status-no-response',
            default => 'status-watch',
        };
    }

    /** @return list<self> */
    public static function awaitingVetHeadDecision(): array
    {
        return [
            self::PendingDischargeApproval,
            self::PendingSlaughterApproval,
        ];
    }

    /** @return list<self> */
    public static function active(): array
    {
        return [
            self::UnderTreatment,
            self::ReadyForDischarge,
            self::NoResponse,
            self::PendingDischargeApproval,
            self::PendingSlaughterApproval,
        ];
    }

    /** @return list<self> */
    public static function pendingHandover(): array
    {
        return [
            self::PendingHandover,
            self::HandoverDelayed,
        ];
    }

    /** @return list<self> */
    public static function completed(): array
    {
        return [
            self::Discharged,
            self::Slaughtered,
        ];
    }

    /** @return list<self> */
    public static function visibleToDoctor(): array
    {
        return [
            self::UnderTreatment,
            self::NoResponse,
            self::PendingDischargeApproval,
            self::PendingSlaughterApproval,
        ];
    }

    /** @return list<string> */
    public static function visibleToDoctorValues(): array
    {
        return array_map(
            fn (self $status) => $status->value,
            self::visibleToDoctor(),
        );
    }
}
