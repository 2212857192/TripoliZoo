<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\AutopsyReferral;
use App\Models\AutopsyReferralNotification;
use App\Models\User;

class AutopsyReferralNotificationService
{
    public function notifyNewReferral(AutopsyReferral $referral): void
    {
        $referral->loadMissing(['animal', 'mortalityCase.supervisor', 'referrer']);
        $animal = $referral->animal;
        $mortalityCase = $referral->mortalityCase;

        $label = $animal?->displayLabel() ?? $mortalityCase?->subject_code ?? 'حيوان';
        $code = $animal?->code ?? $mortalityCase?->subject_code ?? '';
        $careHeadName = $referral->referrer?->name ?? 'رئيس قسم الرعاية';

        $title = "إحالة تشريح جديدة — {$referral->group}";
        $message = "أحال {$careHeadName} الحيوان {$label} ({$code}) للتشريح — حالة النفوق {$mortalityCase?->case_number}.";

        $recipients = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::VetHead->value)
            ->get();

        foreach ($recipients as $user) {
            $this->storeNotification($user, $referral, $title, $message);
        }
    }

    public function markAsReadForUser(AutopsyReferral $referral, User $user): void
    {
        AutopsyReferralNotification::query()
            ->where('user_id', $user->id)
            ->where('autopsy_referral_id', $referral->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markAllAsReadForUser(User $user): void
    {
        AutopsyReferralNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function storeNotification(User $user, AutopsyReferral $referral, string $title, string $message): void
    {
        AutopsyReferralNotification::updateOrCreate(
            [
                'user_id' => $user->id,
                'autopsy_referral_id' => $referral->id,
            ],
            [
                'title' => $title,
                'message' => $message,
                'read_at' => null,
            ]
        );
    }
}
