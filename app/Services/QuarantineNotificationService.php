<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Quarantine;
use App\Models\QuarantineNotification;
use App\Models\User;

class QuarantineNotificationService
{
    public function __construct(private FcmPushService $fcm) {}

    public function notifyGroupVets(Quarantine $quarantine): void
    {
        $quarantine->loadMissing('animal');
        $animal = $quarantine->animal;
        $group = $animal->group;

        $recipients = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::Veterinarian->value)
            ->where('assigned_group', $group)
            ->get();

        $label = $animal->name
            ? "{$animal->name} ({$animal->species})"
            : $animal->species;

        foreach ($recipients as $vet) {
            QuarantineNotification::updateOrCreate(
                [
                    'user_id' => $vet->id,
                    'quarantine_id' => $quarantine->id,
                ],
                [
                    'title' => "حيوان جديد في الحجر — {$group}",
                    'message' => "تمت إضافة {$label} برقم {$quarantine->case_number} إلى الحجر الصحي. المجموعة: {$group}.",
                    'read_at' => null,
                ]
            );
        }

        $this->fcm->sendToUsers(
            $recipients,
            'حديقة حيوان طرابلس',
            "حيوان جديد في الحجر الصحي — {$group}: {$label}",
            [
                'type' => 'quarantine_new',
                'case_number' => $quarantine->case_number,
                'route' => '/doctor/quarantine/'.$quarantine->case_number,
                'group' => $group,
            ]
        );
    }

    public function markQuarantineAsReadForUser(Quarantine $quarantine, User $user): void
    {
        QuarantineNotification::query()
            ->where('user_id', $user->id)
            ->where('quarantine_id', $quarantine->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function notifyVetHead(Quarantine $quarantine, string $title, string $message, string $type): void
    {
        $quarantine->loadMissing('animal');
        $animal = $quarantine->animal;

        $recipients = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::VetHead->value)
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        $label = $animal->name
            ? "{$animal->name} ({$animal->species})"
            : $animal->species;

        foreach ($recipients as $head) {
            QuarantineNotification::updateOrCreate(
                [
                    'user_id' => $head->id,
                    'quarantine_id' => $quarantine->id,
                ],
                [
                    'title' => $title,
                    'message' => $message,
                    'read_at' => null,
                ]
            );
        }

        $this->fcm->sendToUsers(
            $recipients,
            'حديقة حيوان طرابلس',
            "{$title} — {$label}",
            [
                'type' => $type,
                'case_number' => $quarantine->case_number,
                'route' => '/vet/quarantine?open='.$quarantine->case_number,
                'group' => $animal->group,
            ]
        );
    }
}
