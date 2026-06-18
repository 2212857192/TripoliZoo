<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\MortalityCase;
use App\Models\MortalityCaseNotification;
use App\Models\User;

class MortalityCaseNotificationService
{
    public function __construct(private FcmPushService $fcm) {}

    public function notifyNewCase(MortalityCase $mortalityCase): void
    {
        $mortalityCase->loadMissing(['animal', 'supervisor']);
        $animal = $mortalityCase->animal;
        $label = $animal?->displayLabel() ?? $mortalityCase->subject_code;
        $supervisorName = $mortalityCase->supervisor?->name ?? 'مشرف المجموعة';
        $cause = $mortalityCase->displayCause();

        $title = "حالة نفوق جديدة — {$mortalityCase->group}";
        $message = "سجّل {$supervisorName} حالة نفوق للحيوان {$label} — السبب: {$cause}";

        $careStaff = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::CareHead->value)
            ->get();

        foreach ($careStaff as $user) {
            $this->storeNotification($user, $mortalityCase, $title, $message);
        }

        if ($careStaff->isNotEmpty()) {
            $this->fcm->sendToUsers(
                $careStaff,
                'حديقة حيوان طرابلس',
                $title,
                [
                    'type' => 'mortality_case_new',
                    'case_number' => $mortalityCase->case_number,
                    'route' => '/care/mortality',
                    'group' => $mortalityCase->group,
                ]
            );
        }
    }

    public function markAsReadForUser(MortalityCase $mortalityCase, User $user): void
    {
        MortalityCaseNotification::query()
            ->where('user_id', $user->id)
            ->where('mortality_case_id', $mortalityCase->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function storeNotification(User $user, MortalityCase $mortalityCase, string $title, string $message): void
    {
        MortalityCaseNotification::updateOrCreate(
            [
                'user_id' => $user->id,
                'mortality_case_id' => $mortalityCase->id,
            ],
            [
                'title' => $title,
                'message' => $message,
                'read_at' => null,
            ]
        );
    }
}
