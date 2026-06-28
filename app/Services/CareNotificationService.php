<?php

namespace App\Services;

use App\Enums\ReceivingTaskType;
use App\Enums\UserRole;
use App\Models\CareNotification;
use App\Models\ReceivingTask;
use App\Models\User;

class CareNotificationService
{
    public function notifyMedicalDecision(ReceivingTask $task): void
    {
        $task->loadMissing(['animal', 'decisionIssuer', 'supervisor']);
        $animal = $task->animal;

        if (! $animal) {
            return;
        }

        $label = $animal->displayLabel();
        $issuerName = $task->decisionIssuer?->name ?? 'رئيس قسم المستشفى البيطري';
        $decisionLabel = $task->task_type === ReceivingTaskType::AfterHealthRelease
            ? 'إفراج صحي'
            : 'خروج بعد العلاج';

        $title = "قرار طبي جديد — {$decisionLabel}";
        $message = "صدر قرار {$decisionLabel} للحيوان {$label} ({$animal->code}) في مجموعة «{$animal->group}» بواسطة {$issuerName}.";

        $this->notifyCareHeads($task, $title, $message);
    }

    public function markTaskAsReadForUser(ReceivingTask $task, User $user): void
    {
        CareNotification::query()
            ->where('user_id', $user->id)
            ->where('receiving_task_id', $task->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

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

        $this->notifyCareHeads($task, $title, $message);
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

        $this->notifyCareHeads($task, $title, $message);
    }

    private function notifyCareHeads(ReceivingTask $task, string $title, string $message): void
    {
        $careStaff = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::CareHead->value)
            ->get();

        foreach ($careStaff as $user) {
            NotificationRecordUpsert::save(
                CareNotification::class,
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
