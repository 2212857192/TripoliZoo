<?php

namespace App\View\Composers;

use App\Enums\Portal;
use App\Models\CareNotification;
use App\Models\HealthCaseNotification;
use App\Models\OperationalNoteNotification;
use Illuminate\View\View;

class CareLayoutComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user || ! $user->hasPortal(Portal::Care)) {
            $view->with([
                'notificationCount' => 0,
                'notificationBody' => null,
                'careNotificationReadUrl' => null,
            ]);

            return;
        }

        $receivingNotifications = CareNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->with(['receivingTask.animal'])
            ->latest()
            ->limit(10)
            ->get();

        $healthCaseNotifications = HealthCaseNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->with(['healthCase.animal'])
            ->latest()
            ->limit(10)
            ->get();

        $operationalNoteNotifications = OperationalNoteNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->with(['operationalNote.supervisor'])
            ->latest()
            ->limit(10)
            ->get();

        $notificationCount = CareNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count()
            + HealthCaseNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count()
            + OperationalNoteNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();

        $notificationBody = ($receivingNotifications->isEmpty()
            && $healthCaseNotifications->isEmpty()
            && $operationalNoteNotifications->isEmpty())
            ? null
            : view('partials.care-notification-items', [
                'notifications' => $receivingNotifications,
                'healthCaseNotifications' => $healthCaseNotifications,
                'operationalNoteNotifications' => $operationalNoteNotifications,
            ])->render();

        $view->with(compact('notificationCount', 'notificationBody'));
        $view->with('careNotificationReadUrl', route('care.notification.read'));
    }
}
