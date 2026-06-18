<?php

namespace App\Http\Controllers\Api;

use App\Enums\OperationalNoteKind;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\OperationalNoteResource;
use App\Models\OperationalNote;
use App\Models\User;
use App\Services\OperationalNoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupervisorOperationalNoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $notes = OperationalNote::query()
            ->where('supervisor_id', $supervisor->id)
            ->when($request->query('date'), function ($query, string $date) {
                $query->whereDate('noted_at', $date);
            })
            ->orderByDesc('noted_at')
            ->get();

        return response()->json([
            'data' => OperationalNoteResource::collection($notes),
        ]);
    }

    public function store(Request $request, OperationalNoteService $service): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $data = $request->validate([
            'note_kind' => ['required', Rule::in(array_map(fn ($c) => $c->value, OperationalNoteKind::cases()))],
            'summary' => ['required', 'string', 'max:2000'],
            'details' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('operational-notes', 'public')
            : null;

        $note = $service->createNote(
            $supervisor,
            OperationalNoteKind::from($data['note_kind']),
            $data['summary'],
            $data['details'] ?? null,
            $attachmentPath,
        );

        return response()->json([
            'message' => 'تم تسجيل الملاحظة التشغيلية وإرسالها لقسم الرعاية.',
            'data' => new OperationalNoteResource($note),
        ], 201);
    }

    private function supervisorUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->role !== UserRole::GroupSupervisor->value || ! $user->assigned_group) {
            abort(403, 'هذا المسار مخصص لمشرفي المجموعات.');
        }

        return $user;
    }
}
