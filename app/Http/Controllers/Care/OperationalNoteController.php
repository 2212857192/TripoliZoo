<?php

namespace App\Http\Controllers\Care;

use App\Enums\OperationalNoteKind;
use App\Enums\OperationalNoteStatus;
use App\Http\Controllers\Controller;
use App\Models\OperationalNote;
use App\Services\OperationalNoteNotificationService;
use App\Services\OperationalNoteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OperationalNoteController extends Controller
{
    public function index(Request $request): View
    {
        return view('care.notes.index', $this->viewData($request));
    }

    public function directorIndex(Request $request): View
    {
        return directorPage('care.notes.index', $this->viewData($request, readOnly: true));
    }

    public function review(OperationalNote $operationalNote, OperationalNoteService $service): RedirectResponse
    {
        $user = $service->careHeadUser();
        $service->markReviewed($operationalNote, $user);

        return redirect()
            ->route('care.notes.index', ['status' => 'reviewed'])
            ->with('success', "تم تحديد الملاحظة {$operationalNote->note_number} كمراجَعة.");
    }

    public function markNotificationRead(
        OperationalNote $operationalNote,
        OperationalNoteService $service,
        OperationalNoteNotificationService $notifier,
    ): RedirectResponse {
        $user = $service->careHeadUser();
        $notifier->markAsReadForUser($operationalNote, $user);

        return redirect()->route('care.notes.index', ['note' => $operationalNote->note_number]);
    }

    public function attachment(OperationalNote $operationalNote, OperationalNoteService $service): Response
    {
        $service->careHeadUser();

        if (! $operationalNote->attachment_path) {
            abort(404, 'لا يوجد مرفق لهذه الملاحظة.');
        }

        if (! Storage::disk('public')->exists($operationalNote->attachment_path)) {
            abort(404, 'ملف المرفق غير موجود على الخادم.');
        }

        return Storage::disk('public')->response($operationalNote->attachment_path);
    }

    /** @return array<string, mixed> */
    private function viewData(Request $request, bool $readOnly = false): array
    {
        $status = $request->query('status', 'new');
        if (! in_array($status, ['new', 'reviewed'], true)) {
            $status = 'new';
        }

        $query = OperationalNote::query()
            ->with(['supervisor', 'reviewer'])
            ->where('status', $status === 'new' ? OperationalNoteStatus::New : OperationalNoteStatus::Reviewed)
            ->orderByDesc('noted_at');

        if ($kind = $request->query('kind')) {
            if (OperationalNoteKind::tryFrom($kind) !== null) {
                $query->where('note_kind', $kind);
            }
        }

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('note_number', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%");
            });
        }

        $notes = $query->paginate(12)->withQueryString();
        $portalBase = $readOnly ? '/director/care' : '/care';
        $notesForJs = $this->notesForJs($notes, $portalBase, $readOnly);

        if ($highlightNote = $request->query('note')) {
            if (! isset($notesForJs[$highlightNote])) {
                $highlight = OperationalNote::query()
                    ->with(['supervisor', 'reviewer'])
                    ->where('note_number', $highlightNote)
                    ->first();

                if ($highlight) {
                    $notesForJs = array_merge(
                        $notesForJs,
                        $this->notesForJs(collect([$highlight]), $portalBase, $readOnly),
                    );
                }
            }
        }

        return [
            'notes' => $notes,
            'readOnly' => $readOnly,
            'activeStatus' => $status,
            'highlightNote' => $request->query('note'),
            'notesForJs' => $notesForJs,
            'portalBase' => $portalBase,
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'kind' => $request->query('kind', ''),
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    private function notesForJs($notes, string $portalBase, bool $readOnly): array
    {
        $collection = method_exists($notes, 'getCollection')
            ? $notes->getCollection()
            : collect($notes);

        return $collection->mapWithKeys(function (OperationalNote $note) use ($portalBase, $readOnly) {
            return [$note->note_number => [
                'note_number' => $note->note_number,
                'status' => $note->status->value,
                'status_label' => $note->status->label(),
                'note_kind' => $note->note_kind->value,
                'note_kind_label' => $note->note_kind->label(),
                'group' => $note->group,
                'supervisor' => $note->supervisor?->name,
                'noted_at' => $note->noted_at?->format('Y-m-d'),
                'noted_at_display' => $note->noted_at?->format('Y-m-d / h:i A'),
                'summary' => $note->summary,
                'details' => $note->details,
                'has_attachment' => $note->has_attachment,
                'attachment_url' => $note->has_attachment
                    ? $portalBase.'/notes/'.$note->note_number.'/attachment'
                    : null,
                'can_review' => ! $readOnly && $note->canBeReviewed(),
                'review_url' => ! $readOnly && $note->canBeReviewed()
                    ? route('care.notes.review', $note->note_number)
                    : null,
            ]];
        })->all();
    }
}
