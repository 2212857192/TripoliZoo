<?php

namespace App\View\Composers;

use App\Enums\Portal;
use App\Services\PortalNotificationFeedService;
use Illuminate\View\View;

class VetLayoutComposer
{
    public function __construct(private PortalNotificationFeedService $feedService) {}

    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user || ! $user->hasPortal(Portal::Vet)) {
            $view->with([
                'notificationCount' => 0,
                'notificationBody' => null,
                'vetNotificationFeed' => collect(),
                'quarantineNotificationReadUrl' => null,
                'vetReceivingNotificationReadUrl' => null,
            ]);

            return;
        }

        $feed = $this->feedService->buildVetFeed($user);
        $notificationCount = $this->feedService->vetUnreadCount($user);

        $notificationBody = $feed->isEmpty()
            ? null
            : view('partials.vet-notification-feed', [
                'vetNotificationFeed' => $feed,
            ])->render();

        $view->with(compact('notificationCount', 'notificationBody'));
        $view->with('feed', $feed);
        $view->with('vetNotificationFeed', $feed);
        $view->with('quarantineNotificationReadUrl', route('quarantine.notification.read-case'));
        $view->with('vetReceivingNotificationReadUrl', route('vet.notification.read'));
        $view->with('vetTreatmentReferralNotificationReadUrl', route('vet.referrals.treatment.notification.read'));
        $view->with('vetTreatmentReferralsUrl', '/vet/referrals/treatment');
        $view->with('vetAutopsyReferralNotificationReadUrl', route('vet.referrals.autopsy.notification.read'));
        $view->with('vetAutopsyReferralsUrl', '/vet/referrals/autopsy');
        $view->with('vetHospitalCasesUrl', '/vet/cases/hospital');
        $view->with('vetHospitalNotificationReadUrlTemplate', '/vet/notifications/hospital/__CASE__/read');
    }
}
