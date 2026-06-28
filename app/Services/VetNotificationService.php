<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ReceivingTask;
use App\Models\User;
use App\Models\VetNotification;

class VetNotificationService
{
    public function notifyReceivingDelay(ReceivingTask $task): void
    {
        $task->loadMissing(['animal', 'supervisor']);
        $animal = $task->animal;

        if (! $animal) {
            return;
        }

        $label = $animal->displayLabel();
        $supervisorName = $task->supervisor?->name ?? 'مشرف المجموعة';
        $reason = $task->delay_reason ?? '—';

        $title = 'تعذر استلام مؤقت — '.$animal->group;
        $message = "سجّل {$supervisorName} تعذر استلام {$label} ({$animal->code}): {$reason}";

        $this->notifyVetHeads($task, $title, $message);
    }

    public function notifyReceivingCompleted(ReceivingTask $task): void
    {
        $task->loadMissing(['animal', 'supervisor']);
        $animal = $task->animal;

        if (! $animal) {
            return;
        }

        $label = $animal->displayLabel();
        $supervisorName = $task->supervisor?->name ?? 'مشرف المجموعة';

        $title = 'تم استلام الحيوان — '.$animal->group;
        $message = "أكّد {$supervisorName} استلام {$label} ({$animal->code}) في المجموعة.";

        $this->notifyVetHeads($task, $title, $message);
    }

    public function notifyMedicalDecision(ReceivingTask $task): void
    {
        $task->loadMissing(['animal', 'decisionIssuer']);
        $animal = $task->animal;

        if (! $animal) {
            return;
        }

        $label = $animal->displayLabel();
        $decisionLabel = $task->task_type->careDecisionLabel();

        $title = "قرار طبي جديد — {$decisionLabel}";
        $message = "صدر قرار {$decisionLabel} للحيوان {$label} ({$animal->code}) في مجموعة «{$animal->group}».";

        $this->notifyVetHeads($task, $title, $message);
    }

    public function markAsReadForUser(VetNotification $notification, User $user): void
    {
        if ($notification->user_id !== $user->id) {
            abort(403, 'لا يمكنك تعديل هذا الإشعار.');
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }
    }

    public function markTaskAsReadForUser(ReceivingTask $task, User $user): void
    {
        VetNotification::query()
            ->where('user_id', $user->id)
            ->where('receiving_task_id', $task->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function notifyVetHeads(ReceivingTask $task, string $title, string $message): void
    {
        $recipients = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::VetHead->value)
            ->get();

        foreach ($recipients as $user) {
            NotificationRecordUpsert::save(
                VetNotification::class,
                [
                    'user_id' => $user->id,
                    'receiving_task_id' => $task->id,
                ],
                [
                    'title' => $title,
                    'message' => $message,
                ],
            );
        }
    }
}
