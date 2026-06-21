<?php

namespace App\View\Composers;

use App\Enums\Portal;
use App\Services\PortalNotificationFeedService;
use Illuminate\View\View;

class CareLayoutComposer
{
    public function __construct(private PortalNotificationFeedService $feedService) {}

    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user || ! $user->hasPortal(Portal::Care)) {
            $view->with([
                'notificationCount' => 0,
                'notificationBody' => null,
                'careNotificationFeed' => collect(),
                'careNotificationReadUrl' => null,
            ]);

            return;
        }

        $feed = $this->feedService->buildCareFeed($user);
        $notificationCount = $this->feedService->careUnreadCount($user);

        $notificationBody = $feed->isEmpty()
            ? null
            : view('partials.care-notification-feed', [
                'careNotificationFeed' => $feed,
            ])->render();

        $view->with(compact('notificationCount', 'notificationBody'));
        $view->with('careNotificationFeed', $feed);
        $view->with('careNotificationReadUrl', route('care.notification.read'));
    }
}
