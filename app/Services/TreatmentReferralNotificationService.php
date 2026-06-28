<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\TreatmentReferral;
use App\Models\TreatmentReferralNotification;
use App\Models\User;

class TreatmentReferralNotificationService
{
    public function notifyNewReferral(TreatmentReferral $referral): void
    {
        $referral->loadMissing(['animal', 'healthCase.supervisor', 'referrer']);
        $animal = $referral->animal;

        if (! $animal) {
            return;
        }

        $label = $animal->displayLabel();
        $careHeadName = $referral->referrer?->name ?? 'رئيس قسم الرعاية';
        $supervisorName = $referral->healthCase?->supervisor?->name ?? 'مشرف المجموعة';

        $title = "إحالة علاج جديدة — {$referral->group}";
        $message = "أحال {$careHeadName} الحيوان {$label} ({$animal->code}) للعلاج — الحالة {$referral->healthCase?->case_number} من {$supervisorName}.";

        $recipients = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::VetHead->value)
            ->get();

        foreach ($recipients as $user) {
            $this->storeNotification($user, $referral, $title, $message);
        }
    }

    public function markAsReadForUser(TreatmentReferral $referral, User $user): void
    {
        TreatmentReferralNotification::query()
            ->where('user_id', $user->id)
            ->where('treatment_referral_id', $referral->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markAllAsReadForUser(User $user): void
    {
        TreatmentReferralNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function storeNotification(User $user, TreatmentReferral $referral, string $title, string $message): void
    {
        NotificationRecordUpsert::save(
            TreatmentReferralNotification::class,
            [
                'user_id' => $user->id,
                'treatment_referral_id' => $referral->id,
            ],
            [
                'title' => $title,
                'message' => $message,
            ],
        );
    }
}
