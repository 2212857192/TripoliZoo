<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\OperationalNote;
use App\Models\OperationalNoteNotification;
use App\Models\User;

class OperationalNoteNotificationService
{
    public function __construct(private FcmPushService $fcm) {}

    public function notifyNewNote(OperationalNote $note): void
    {
        $note->loadMissing('supervisor');
        $supervisorName = $note->supervisor?->name ?? 'مشرف المجموعة';
        $kindLabel = $note->note_kind->label();

        $title = "ملاحظة تشغيلية جديدة — {$note->group}";
        $message = "سجّل {$supervisorName} ملاحظة ({$kindLabel}): ".str($note->summary)->limit(120);

        $careStaff = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::CareHead->value)
            ->get();

        foreach ($careStaff as $user) {
            $this->storeNotification($user, $note, $title, $message);
        }

        if ($careStaff->isNotEmpty()) {
            $this->fcm->sendToUsers(
                $careStaff,
                'حديقة حيوان طرابلس',
                $title,
                [
                    'type' => 'operational_note_new',
                    'note_number' => $note->note_number,
                    'route' => '/care/notes',
                    'group' => $note->group,
                ]
            );
        }
    }

    public function markAsReadForUser(OperationalNote $note, User $user): void
    {
        OperationalNoteNotification::query()
            ->where('user_id', $user->id)
            ->where('operational_note_id', $note->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markAllAsReadForUser(User $user): void
    {
        OperationalNoteNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function storeNotification(User $user, OperationalNote $note, string $title, string $message): void
    {
        OperationalNoteNotification::updateOrCreate(
            [
                'user_id' => $user->id,
                'operational_note_id' => $note->id,
            ],
            [
                'title' => $title,
                'message' => $message,
                'read_at' => null,
            ]
        );
    }
}
