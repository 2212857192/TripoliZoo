<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Enums\HealthCaseFollowUpKind;
use App\Enums\HealthCaseStatus;
use App\Enums\TreatmentReferralStatus;
use App\Models\Animal;
use App\Models\HealthCase;
use App\Models\TreatmentReferral;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HealthCaseService
{
    public function __construct(
        private HealthCaseNumberGenerator $numbers,
        private TreatmentReferralNumberGenerator $referralNumbers,
        private HealthCaseNotificationService $notifier,
        private TreatmentReferralNotificationService $referralNotifier,
        private AnimalLifecycleService $animalLifecycle,
    ) {}

    public function createCase(
        User $supervisor,
        Animal $animal,
        string $description,
        HealthCaseFollowUpKind $followUpKind,
        ?string $attachmentPath = null,
        ?string $animalNotes = null,
    ): HealthCase {
        $this->animalLifecycle->assertAnimalCanReceiveActions($animal);
        $this->animalLifecycle->assertNoOpenHealthCase($animal);

        $healthCase = null;

        DB::transaction(function () use ($supervisor, $animal, $description, $followUpKind, $attachmentPath, $animalNotes, &$healthCase) {
            $healthCase = HealthCase::create([
                'case_number' => $this->numbers->next(),
                'animal_id' => $animal->id,
                'supervisor_id' => $supervisor->id,
                'group' => $animal->group,
                'description' => $description,
                'animal_notes' => $followUpKind === HealthCaseFollowUpKind::NeedsReferral
                    ? ($animalNotes !== null && trim($animalNotes) !== '' ? trim($animalNotes) : null)
                    : null,
                'follow_up_kind' => $followUpKind,
                'has_attachment' => $attachmentPath !== null,
                'attachment_path' => $attachmentPath,
                'status' => HealthCaseStatus::New,
            ]);
        });

        /** @var HealthCase $healthCase */
        $fresh = $healthCase->fresh(['animal', 'supervisor']);
        $this->notifier->notifyNewCase($fresh);

        return $fresh;
    }

    public function markReviewed(HealthCase $healthCase, User $careHead): HealthCase
    {
        if (! $healthCase->canBeActedOn()) {
            throw ValidationException::withMessages([
                'case_number' => 'لا يمكن مراجعة هذه الحالة، فقد تمت معالجتها مسبقاً.',
            ]);
        }

        $healthCase->update([
            'status' => HealthCaseStatus::Reviewed,
            'reviewed_by' => $careHead->id,
            'reviewed_at' => now(),
        ]);

        $this->notifier->markAsReadForUser($healthCase, $careHead);

        return $healthCase->fresh(['animal', 'supervisor', 'reviewer']);
    }

    public function referForTreatment(HealthCase $healthCase, User $careHead): HealthCase
    {
        $healthCase->loadMissing('animal');
        if ($healthCase->animal) {
            $this->animalLifecycle->assertAnimalCanReceiveActions($healthCase->animal);
        }

        if ($healthCase->follow_up_kind !== HealthCaseFollowUpKind::NeedsReferral) {
            throw ValidationException::withMessages([
                'case_number' => 'هذه الحالة مسجّلة بأنها لا تحتاج إحالة.',
            ]);
        }

        if (! $healthCase->canBeActedOn()) {
            throw ValidationException::withMessages([
                'case_number' => 'لا يمكن إحالة هذه الحالة، فقد تمت معالجتها مسبقاً.',
            ]);
        }

        $referral = null;

        DB::transaction(function () use ($healthCase, $careHead, &$referral) {
            $healthCase->update([
                'status' => HealthCaseStatus::Referred,
                'referred_by' => $careHead->id,
                'referred_at' => now(),
            ]);

            $referral = TreatmentReferral::create([
                'referral_number' => $this->referralNumbers->next(),
                'health_case_id' => $healthCase->id,
                'animal_id' => $healthCase->animal_id,
                'group' => $healthCase->group,
                'status' => TreatmentReferralStatus::Pending,
                'referred_by' => $careHead->id,
                'referred_at' => now(),
            ]);
        });

        $this->notifier->markAsReadForUser($healthCase, $careHead);

        if ($referral) {
            $this->referralNotifier->notifyNewReferral($referral->fresh(['animal', 'healthCase.supervisor', 'referrer']));
        }

        return $healthCase->fresh(['animal', 'supervisor', 'referrer', 'treatmentReferral']);
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Animal> */
    public function animalsForSupervisor(User $supervisor)
    {
        $group = $supervisor->assigned_group;

        if (! $group) {
            return collect();
        }

        return Animal::withQuarantine()
            ->where('group', $group)
            ->whereIn('status', [
                AnimalStatus::Active->value,
                AnimalStatus::PendingReceipt->value,
                AnimalStatus::UnderBirthFollowUp->value,
            ])
            ->whereNotIn('status', [
                AnimalStatus::Dead->value,
                AnimalStatus::PendingMortalityApproval->value,
                AnimalStatus::Exited->value,
            ])
            ->orderBy('code')
            ->get();
    }
}
