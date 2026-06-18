<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ReceivingTask;
use App\Models\SupervisorNotification;
use App\Models\User;

class SupervisorNotificationService
{
    public function __construct(private FcmPushService $fcm) {}

    public function notifyReceivingTask(ReceivingTask $task): void
    {
        $task->loadMissing(['animal', 'supervisor', 'decisionIssuer']);
        $animal = $task->animal;
        $supervisor = $task->supervisor;

        if (! $supervisor || ! $animal) {
            return;
        }

        $label = $animal->displayLabel();
        $issuerName = $task->decisionIssuer?->name ?? 'رئيس قسم المستشفى البيطري';

        SupervisorNotification::updateOrCreate(
            [
                'user_id' => $supervisor->id,
                'receiving_task_id' => $task->id,
            ],
            [
                'title' => "مهمة استلام جديدة — {$animal->group}",
                'message' => "صدر قرار إفراج صحي للحيوان {$label} ({$animal->code}). المطلوب: تأكيد الاستلام في المجموعة.",
                'read_at' => null,
            ]
        );

        $this->fcm->sendToUsers(
            collect([$supervisor]),
            'حديقة حيوان طرابلس',
            "مهمة استلام جديدة — {$animal->group}: {$label}",
            [
                'type' => 'receiving_task_new',
                'task_number' => $task->task_number,
                'route' => '/supervisor/receiving-tasks?filter=pending',
                'group' => $animal->group,
            ]
        );
    }

    public function markTaskAsReadForUser(ReceivingTask $task, User $user): void
    {
        SupervisorNotification::query()
            ->where('user_id', $user->id)
            ->where('receiving_task_id', $task->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /** @return list<User> */
    public function supervisorsForGroup(string $group): array
    {
        return User::query()
            ->where('status', 'active')
            ->where('role', UserRole::GroupSupervisor->value)
            ->where('assigned_group', $group)
            ->get()
            ->all();
    }
}
