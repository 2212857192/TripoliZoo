<?php

namespace App\Services;

use App\Enums\OperationalNoteKind;
use App\Enums\OperationalNoteStatus;
use App\Enums\UserRole;
use App\Models\OperationalNote;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OperationalNoteService
{
    public function __construct(
        private OperationalNoteNumberGenerator $numbers,
        private OperationalNoteNotificationService $notifier,
    ) {}

    public function createNote(
        User $supervisor,
        OperationalNoteKind $noteKind,
        string $summary,
        ?string $details = null,
        ?string $attachmentPath = null,
    ): OperationalNote {
        $note = OperationalNote::create([
            'note_number' => $this->numbers->next(),
            'supervisor_id' => $supervisor->id,
            'group' => $supervisor->assigned_group,
            'note_kind' => $noteKind,
            'summary' => $summary,
            'details' => $details,
            'has_attachment' => $attachmentPath !== null,
            'attachment_path' => $attachmentPath,
            'status' => OperationalNoteStatus::New,
            'noted_at' => now(),
        ]);

        $fresh = $note->fresh(['supervisor']);
        $this->notifier->notifyNewNote($fresh);

        return $fresh;
    }

    public function markReviewed(OperationalNote $note, User $careHead): OperationalNote
    {
        if (! $note->canBeReviewed()) {
            return $note;
        }

        DB::transaction(function () use ($note, $careHead) {
            $note->update([
                'status' => OperationalNoteStatus::Reviewed,
                'reviewed_by' => $careHead->id,
                'reviewed_at' => now(),
            ]);
        });

        $this->notifier->markAsReadForUser($note, $careHead);

        return $note->fresh(['supervisor', 'reviewer']);
    }

    public function careHeadUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== UserRole::CareHead->value) {
            abort(403, 'هذا الإجراء مخصص لرئيس قسم الرعاية والتغذية.');
        }

        return $user;
    }
}
