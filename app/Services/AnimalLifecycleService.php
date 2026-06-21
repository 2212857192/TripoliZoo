<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Enums\FieldCaseStatus;
use App\Enums\HealthCaseStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\MortalityCaseStatus;
use App\Enums\QuarantineStatus;
use App\Enums\TreatmentReferralStatus;
use App\Models\Animal;
use App\Models\FieldCase;
use App\Models\HealthCase;
use App\Models\HospitalCase;
use App\Models\MortalityCase;
use App\Models\Quarantine;
use App\Models\TreatmentReferral;
use Illuminate\Validation\ValidationException;

class AnimalLifecycleService
{
    public const INACTIVE_MESSAGE = 'لا يمكن تنفيذ الإجراء، الحيوان غير نشط.';
    public const PENDING_MORTALITY_MESSAGE = 'لا يمكن تنفيذ الإجراء، الحيوان موقوف بانتظار اعتماد حالة النفوق.';
    public const OPEN_FIELD_CASE_MESSAGE = 'لا يمكن تنفيذ الإجراء، لدى الحيوان حالة ميدانية مفتوحة يجب إغلاقها أولاً.';
    public const OPEN_HEALTH_CASE_MESSAGE = 'لا يمكن تسجيل حالة صحية جديدة، يوجد حالة صحية قائمة لهذا الحيوان.';

    public const FIELD_CLOSED_MORTALITY = 'أُغلقت تلقائياً بسبب تسجيل حالة نفوق.';
    public const FIELD_CLOSED_SLAUGHTER = 'أُغلقت تلقائياً بسبب صدور قرار ذبح اضطراري.';
    public const FIELD_CLOSED_MANUAL = 'أُغلقت بعد اكتمال العلاج الميداني.';
    public const HOSPITAL_CLOSED_MORTALITY = 'أُغلقت تلقائياً بسبب تسجيل حالة نفوق.';
    public const HOSPITAL_CLOSED_SLAUGHTER = 'أُغلقت تلقائياً بسبب صدور قرار ذبح اضطراري.';

    public function assertAnimalCanReceiveActions(Animal $animal): void
    {
        if ($animal->status === AnimalStatus::Dead->value) {
            throw ValidationException::withMessages([
                'animal_code' => self::INACTIVE_MESSAGE,
            ]);
        }

        if ($animal->status === AnimalStatus::PendingMortalityApproval->value) {
            throw ValidationException::withMessages([
                'animal_code' => self::PENDING_MORTALITY_MESSAGE,
            ]);
        }

        if ($this->hasOpenMortalityCase($animal)) {
            throw ValidationException::withMessages([
                'animal_code' => self::PENDING_MORTALITY_MESSAGE,
            ]);
        }
    }

    public function assertNoOpenFieldCase(Animal $animal, ?int $exceptFieldCaseId = null): void
    {
        $query = FieldCase::query()
            ->where('animal_id', $animal->id)
            ->where('status', FieldCaseStatus::Active->value);

        if ($exceptFieldCaseId !== null) {
            $query->whereKeyNot($exceptFieldCaseId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'animal_code' => self::OPEN_FIELD_CASE_MESSAGE,
            ]);
        }
    }

    public function assertNoOpenHealthCase(Animal $animal): void
    {
        $exists = HealthCase::query()
            ->where('animal_id', $animal->id)
            ->whereIn('status', [
                HealthCaseStatus::New->value,
                HealthCaseStatus::Referred->value,
            ])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'animal_code' => self::OPEN_HEALTH_CASE_MESSAGE,
            ]);
        }
    }

    public function referActiveFieldCasesToHospital(Animal $animal, HospitalCase $hospitalCase): void
    {
        FieldCase::query()
            ->where('animal_id', $animal->id)
            ->where('status', FieldCaseStatus::Active->value)
            ->update([
                'status' => FieldCaseStatus::ReferredToHospital->value,
                'hospital_case_id' => $hospitalCase->id,
                'closed_at' => now(),
                'closing_note' => "أُحيلت تلقائياً إلى المستشفى — {$hospitalCase->case_number}.",
            ]);
    }

    public function suspendForPendingMortality(Animal $animal, MortalityCase $mortalityCase): void
    {
        Animal::withoutGlobalScopes()
            ->whereKey($animal->id)
            ->update(['status' => AnimalStatus::PendingMortalityApproval->value]);

        $this->closeActiveFieldCases($animal, FieldCaseStatus::Closed, self::FIELD_CLOSED_MORTALITY);

        HealthCase::query()
            ->where('animal_id', $animal->id)
            ->where('status', HealthCaseStatus::New->value)
            ->update([
                'status' => HealthCaseStatus::Reviewed->value,
                'reviewed_at' => now(),
            ]);

        TreatmentReferral::query()
            ->where('animal_id', $animal->id)
            ->where('status', TreatmentReferralStatus::Pending->value)
            ->update([
                'status' => TreatmentReferralStatus::Rejected->value,
                'reviewed_at' => now(),
                'rejection_reason' => self::PENDING_MORTALITY_MESSAGE,
            ]);
    }

    public function finalizeAsDead(Animal $animal): void
    {
        Animal::withoutGlobalScopes()
            ->whereKey($animal->id)
            ->update(['status' => AnimalStatus::Dead->value]);

        $this->stopRelatedOperations($animal, self::INACTIVE_MESSAGE);

        $this->closeActiveFieldCases($animal, FieldCaseStatus::Closed, self::FIELD_CLOSED_MORTALITY);
        $this->closeOpenHospitalCases($animal, self::HOSPITAL_CLOSED_MORTALITY);

        Quarantine::query()
            ->where('animal_id', $animal->id)
            ->where('status', QuarantineStatus::UnderFollowUp->value)
            ->update([
                'status' => QuarantineStatus::Failed->value,
                'closed_at' => now(),
                'close_reason' => 'نفوق داخل الحجر',
                'close_notes' => self::HOSPITAL_CLOSED_MORTALITY,
            ]);
    }

    public function finalizeAfterEmergencySlaughter(Animal $animal, HospitalCase $slaughteredCase): void
    {
        Animal::withoutGlobalScopes()
            ->whereKey($animal->id)
            ->update(['status' => AnimalStatus::Dead->value]);

        $this->stopRelatedOperations($animal, self::INACTIVE_MESSAGE);

        $this->closeActiveFieldCases($animal, FieldCaseStatus::Closed, self::FIELD_CLOSED_SLAUGHTER);
        $this->closeOpenHospitalCases(
            $animal,
            self::HOSPITAL_CLOSED_SLAUGHTER,
            $slaughteredCase->id,
        );

        Quarantine::query()
            ->where('animal_id', $animal->id)
            ->where('status', QuarantineStatus::UnderFollowUp->value)
            ->update([
                'status' => QuarantineStatus::Failed->value,
                'closed_at' => now(),
                'close_reason' => 'ذبح اضطراري داخل الحجر',
                'close_notes' => self::HOSPITAL_CLOSED_SLAUGHTER,
            ]);
    }

    private function closeActiveFieldCases(
        Animal $animal,
        FieldCaseStatus $status,
        string $closingNote,
    ): void {
        FieldCase::query()
            ->where('animal_id', $animal->id)
            ->where('status', FieldCaseStatus::Active->value)
            ->update([
                'status' => $status->value,
                'closed_at' => now(),
                'closing_note' => $closingNote,
            ]);
    }

    /** @param  list<string>  $statuses */
    private function closeOpenHospitalCases(
        Animal $animal,
        string $closingOutcome,
        ?int $exceptHospitalCaseId = null,
        array $statuses = [],
    ): void {
        if ($statuses === []) {
            $statuses = $this->openHospitalStatuses();
        }

        $query = HospitalCase::query()
            ->where('animal_id', $animal->id)
            ->whereIn('status', $statuses);

        if ($exceptHospitalCaseId !== null) {
            $query->whereKeyNot($exceptHospitalCaseId);
        }

        $query->update([
            'status' => HospitalCaseStatus::Slaughtered->value,
            'closed_at' => now(),
            'closing_outcome' => $closingOutcome,
        ]);
    }

    private function stopRelatedOperations(Animal $animal, string $referralRejectionReason): void
    {
        HealthCase::query()
            ->where('animal_id', $animal->id)
            ->where('status', HealthCaseStatus::New->value)
            ->update([
                'status' => HealthCaseStatus::Reviewed->value,
                'reviewed_at' => now(),
            ]);

        TreatmentReferral::query()
            ->where('animal_id', $animal->id)
            ->where('status', TreatmentReferralStatus::Pending->value)
            ->update([
                'status' => TreatmentReferralStatus::Rejected->value,
                'reviewed_at' => now(),
                'rejection_reason' => $referralRejectionReason,
            ]);
    }

    /** @return list<string> */
    private function openHospitalStatuses(): array
    {
        return array_map(
            fn (HospitalCaseStatus $status) => $status->value,
            array_merge(HospitalCaseStatus::active(), HospitalCaseStatus::pendingHandover()),
        );
    }

    private function hasOpenMortalityCase(Animal $animal): bool
    {
        return MortalityCase::query()
            ->where('animal_id', $animal->id)
            ->where('status', MortalityCaseStatus::New->value)
            ->exists();
    }
}
