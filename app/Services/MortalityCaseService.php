<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Enums\MortalityCaseStatus;
use App\Enums\MortalityVictimKind;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\MortalityCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MortalityCaseService
{
    public function __construct(
        private MortalityCaseNumberGenerator $numbers,
        private MortalityCaseNotificationService $notifier,
        private AutopsyReferralService $autopsyReferrals,
    ) {}

    public function createCase(
        User $supervisor,
        MortalityVictimKind $victimKind,
        string $subjectCode,
        ?string $subjectType,
        ?string $deathCause,
        ?string $notes,
        ?Animal $animal,
        ?string $attachmentPath = null,
    ): MortalityCase {
        $mortalityCase = null;

        DB::transaction(function () use (
            $supervisor,
            $victimKind,
            $subjectCode,
            $subjectType,
            $deathCause,
            $notes,
            $animal,
            $attachmentPath,
            &$mortalityCase,
        ) {
            $mortalityCase = MortalityCase::create([
                'case_number' => $this->numbers->next(),
                'animal_id' => $animal?->id,
                'subject_code' => $subjectCode,
                'subject_type' => $subjectType ?? $animal?->species,
                'supervisor_id' => $supervisor->id,
                'group' => $animal?->group ?? $supervisor->assigned_group,
                'victim_kind' => $victimKind,
                'death_cause' => $this->normalizeCause($deathCause),
                'notes' => $notes,
                'death_date' => now()->toDateString(),
                'has_attachment' => $attachmentPath !== null,
                'attachment_path' => $attachmentPath,
                'status' => MortalityCaseStatus::New,
            ]);
        });

        $fresh = $mortalityCase->fresh(['animal', 'supervisor']);
        $this->notifier->notifyNewCase($fresh);

        return $fresh;
    }

    public function approve(MortalityCase $mortalityCase, User $careHead): MortalityCase
    {
        if (! $mortalityCase->canBeActedOn()) {
            return $mortalityCase;
        }

        if (! $mortalityCase->isCauseApparent()) {
            throw ValidationException::withMessages([
                'death_cause' => 'لا يمكن اعتماد حالة نفوق بدون سبب ظاهر.',
            ]);
        }

        DB::transaction(function () use ($mortalityCase, $careHead) {
            $mortalityCase->update([
                'status' => MortalityCaseStatus::Approved,
                'reviewed_by' => $careHead->id,
                'reviewed_at' => now(),
            ]);

            if ($mortalityCase->animal_id) {
                Animal::withoutGlobalScopes()
                    ->whereKey($mortalityCase->animal_id)
                    ->update(['status' => AnimalStatus::Dead->value]);
            }
        });

        $this->notifier->markAsReadForUser($mortalityCase, $careHead);

        return $mortalityCase->fresh(['animal', 'supervisor', 'reviewer']);
    }

    public function referForAutopsy(MortalityCase $mortalityCase, User $careHead, ?string $reason = null): MortalityCase
    {
        if (! $mortalityCase->canBeActedOn()) {
            return $mortalityCase;
        }

        DB::transaction(function () use ($mortalityCase, $careHead, $reason) {
            $mortalityCase->update([
                'status' => MortalityCaseStatus::ReferredForAutopsy,
                'reviewed_by' => $careHead->id,
                'reviewed_at' => now(),
                'autopsy_reason' => $reason,
            ]);

            $this->autopsyReferrals->createFromMortalityCase($mortalityCase, $careHead, $reason);
        });

        $this->notifier->markAsReadForUser($mortalityCase, $careHead);

        return $mortalityCase->fresh(['animal', 'supervisor', 'reviewer', 'autopsyReferral']);
    }

    public function careHeadUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== UserRole::CareHead->value) {
            abort(403, 'هذا الإجراء مخصص لرئيس قسم الرعاية والتغذية.');
        }

        return $user;
    }

    private function normalizeCause(?string $deathCause): ?string
    {
        $cause = trim((string) $deathCause);

        return $cause !== '' ? $cause : null;
    }
}
