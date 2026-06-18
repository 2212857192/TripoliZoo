<?php

namespace App\View\Composers;

use App\Enums\Portal;
use App\Models\AutopsyReferralNotification;
use App\Models\QuarantineNotification;
use App\Models\TreatmentReferralNotification;
use App\Models\VetNotification;
use Illuminate\View\View;

class VetLayoutComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user || ! $user->hasPortal(Portal::Vet)) {
            $view->with([
                'notificationCount' => 0,
                'notificationBody' => null,
                'quarantineNotificationReadUrl' => null,
                'vetReceivingNotificationReadUrl' => null,
            ]);

            return;
        }

        $quarantineNotifications = QuarantineNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->with(['quarantine.animal'])
            ->latest()
            ->limit(10)
            ->get();

        $receivingNotifications = VetNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->with(['receivingTask.animal'])
            ->latest()
            ->limit(10)
            ->get();

        $treatmentReferralNotifications = TreatmentReferralNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->with(['treatmentReferral.animal'])
            ->latest()
            ->limit(10)
            ->get();

        $autopsyReferralNotifications = AutopsyReferralNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->with(['autopsyReferral.animal'])
            ->latest()
            ->limit(10)
            ->get();

        $notificationCount = QuarantineNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count()
            + VetNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count()
            + TreatmentReferralNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count()
            + AutopsyReferralNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();

        $parts = [];
        if ($quarantineNotifications->isNotEmpty()) {
            $parts[] = view('partials.vet-notification-items', [
                'notifications' => $quarantineNotifications,
            ])->render();
        }
        if ($receivingNotifications->isNotEmpty()) {
            $parts[] = view('partials.vet-receiving-notification-items', [
                'notifications' => $receivingNotifications,
            ])->render();
        }
        if ($treatmentReferralNotifications->isNotEmpty()) {
            $parts[] = view('partials.vet-treatment-referral-notification-items', [
                'notifications' => $treatmentReferralNotifications,
            ])->render();
        }
        if ($autopsyReferralNotifications->isNotEmpty()) {
            $parts[] = view('partials.vet-autopsy-referral-notification-items', [
                'notifications' => $autopsyReferralNotifications,
            ])->render();
        }

        $notificationBody = $parts === [] ? null : implode('', $parts);

        $view->with(compact('notificationCount', 'notificationBody'));
        $view->with('quarantineNotificationReadUrl', route('quarantine.notification.read-case'));
        $view->with('vetReceivingNotificationReadUrl', route('vet.notification.read'));
        $view->with('vetTreatmentReferralNotificationReadUrl', route('vet.referrals.treatment.notification.read'));
        $view->with('vetTreatmentReferralsUrl', '/vet/referrals/treatment');
        $view->with('vetAutopsyReferralNotificationReadUrl', route('vet.referrals.autopsy.notification.read'));
        $view->with('vetAutopsyReferralsUrl', '/vet/referrals/autopsy');
    }
}
