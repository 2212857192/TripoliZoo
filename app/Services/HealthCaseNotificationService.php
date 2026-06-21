<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\HealthCase;
use App\Models\HealthCaseNotification;
use App\Models\User;
use App\Services\FcmPushService;

class HealthCaseNotificationService
{
    public function __construct(private FcmPushService $fcm) {}

    public function notifyNewCase(HealthCase $healthCase): void
    {
        $healthCase->loadMissing(['animal', 'supervisor']);
        $animal = $healthCase->animal;

        if (! $animal) {
            return;
        }

        $label = $animal->displayLabel();
        $supervisorName = $healthCase->supervisor?->name ?? 'مشرف المجموعة';
        $followUpLabel = $healthCase->follow_up_kind->label();

        $title = "حالة صحية جديدة — {$healthCase->group}";
        $message = "سجّل {$supervisorName} حالة صحية للحيوان {$label} ({$animal->code}) — {$followUpLabel}: {$healthCase->description}";

        $careStaff = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::CareHead->value)
            ->get();

        foreach ($careStaff as $user) {
            $this->storeNotification($user, $healthCase, $title, $message);
        }

        if ($careStaff->isNotEmpty()) {
            $this->fcm->sendToUsers(
                $careStaff,
                'حديقة حيوان طرابلس',
                $title,
                [
                    'type' => 'health_case_new',
                    'case_number' => $healthCase->case_number,
                    'route' => '/care/health',
                    'group' => $healthCase->group,
                ]
            );
        }
    }

    public function markAsReadForUser(HealthCase $healthCase, User $user): void
    {
        HealthCaseNotification::query()
            ->where('user_id', $user->id)
            ->where('health_case_id', $healthCase->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markAllAsReadForUser(User $user): void
    {
        HealthCaseNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function storeNotification(User $user, HealthCase $healthCase, string $title, string $message): void
    {
        HealthCaseNotification::updateOrCreate(
            [
                'user_id' => $user->id,
                'health_case_id' => $healthCase->id,
            ],
            [
                'title' => $title,
                'message' => $message,
                'read_at' => null,
            ]
        );
    }
}
