<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\BirthRegistration;
use App\Models\BirthRegistrationNotification;
use App\Models\User;

class BirthRegistrationNotificationService
{
    public function __construct(private FcmPushService $fcm) {}

    public function notifyNewRegistration(BirthRegistration $registration): void
    {
        $registration->loadMissing(['mother', 'supervisor', 'newborns']);
        $supervisorName = $registration->supervisor?->name ?? 'مشرف المجموعة';
        $motherCode = $registration->mother?->code ?? '—';

        $title = "ولادة جديدة — {$registration->group}";
        $message = "سجّل {$supervisorName} ولادة لـ {$motherCode} بعدد {$registration->birth_count} مولود.";

        $careStaff = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::CareHead->value)
            ->get();

        foreach ($careStaff as $user) {
            $this->storeNotification($user, $registration, $title, $message);
        }

        if ($careStaff->isNotEmpty()) {
            $this->fcm->sendToUsers(
                $careStaff,
                'حديقة حيوان طرابلس',
                $title,
                [
                    'type' => 'birth_registration_new',
                    'registration_number' => $registration->registration_number,
                    'route' => '/care/births',
                    'group' => $registration->group,
                ]
            );
        }
    }

    public function markAsReadForUser(BirthRegistration $registration, User $user): void
    {
        BirthRegistrationNotification::query()
            ->where('user_id', $user->id)
            ->where('birth_registration_id', $registration->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function storeNotification(User $user, BirthRegistration $registration, string $title, string $message): void
    {
        BirthRegistrationNotification::updateOrCreate(
            [
                'user_id' => $user->id,
                'birth_registration_id' => $registration->id,
            ],
            [
                'title' => $title,
                'message' => $message,
                'read_at' => null,
            ]
        );
    }
}
