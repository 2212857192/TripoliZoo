<?php

namespace App\Http\Controllers\Api;

use App\Enums\QuarantineStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuarantineApiResource;
use App\Models\Quarantine;
use App\Models\QuarantineNote;
use App\Models\QuarantineNotification;
use App\Models\QuarantineVaccine;
use App\Models\User;
use App\Services\QuarantineNotificationService;
use App\Services\ReceivingTaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorQuarantineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vet = $this->veterinarianUser($request);
        $group = $vet->assigned_group;

        $unreadIds = QuarantineNotification::query()
            ->where('user_id', $vet->id)
            ->whereNull('read_at')
            ->pluck('quarantine_id')
            ->flip();

        $quarantines = Quarantine::query()
            ->with(['animal', 'responsibleVet', 'notes.author', 'vaccines.author'])
            ->where('status', QuarantineStatus::UnderFollowUp)
            ->whereHas('animal', fn ($q) => $q->where('group', $group))
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->get()
            ->each(function (Quarantine $quarantine) use ($unreadIds) {
                $quarantine->setAttribute('is_unread', $unreadIds->has($quarantine->id));
            });

        return QuarantineApiResource::collection($quarantines)
            ->additional(['active_count' => $quarantines->count()])
            ->response();
    }

    public function show(Request $request, Quarantine $quarantine): JsonResponse
    {
        $vet = $this->veterinarianUser($request);
        $this->authorizeQuarantine($quarantine, $vet);

        $quarantine->load(['animal', 'responsibleVet', 'notes.author', 'vaccines.author']);
        app(QuarantineNotificationService::class)->markQuarantineAsReadForUser($quarantine, $vet);

        return (new QuarantineApiResource($quarantine))->response();
    }

    public function storeNote(
        Request $request,
        Quarantine $quarantine,
        QuarantineNotificationService $notifier
    ): JsonResponse {
        $vet = $this->veterinarianUser($request);
        $this->authorizeQuarantine($quarantine, $vet);

        if (! $quarantine->isUnderFollowUp()) {
            return response()->json([
                'message' => 'لا يمكن إضافة ملاحظات إلا للحالات قيد المتابعة.',
            ], 422);
        }

        $data = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ], [
            'note.required' => 'نص الملاحظة مطلوب.',
        ]);

        QuarantineNote::create([
            'quarantine_id' => $quarantine->id,
            'user_id' => $vet->id,
            'note' => $data['note'],
            'noted_at' => now(),
        ]);

        if (! $quarantine->responsibleVet?->isVeterinarian()) {
            $quarantine->update(['responsible_vet_id' => $vet->id]);
        }

        $quarantine->loadMissing('animal');
        $animal = $quarantine->animal;
        $label = $animal->name ?: $animal->species;

        $notifier->notifyVetHead(
            $quarantine,
            'ملاحظة صحية جديدة',
            "سجّل د. {$vet->name} ملاحظة صحية على {$label} ({$quarantine->case_number}).",
            'quarantine_note_added'
        );

        $quarantine->load(['animal', 'responsibleVet', 'notes.author', 'vaccines.author']);

        return response()->json([
            'message' => 'تم تسجيل الملاحظة الصحية.',
            'data' => new QuarantineApiResource($quarantine),
        ]);
    }

    public function storeVaccine(
        Request $request,
        Quarantine $quarantine,
        QuarantineNotificationService $notifier
    ): JsonResponse {
        $vet = $this->veterinarianUser($request);
        $this->authorizeQuarantine($quarantine, $vet);

        if (! $quarantine->isUnderFollowUp()) {
            return response()->json([
                'message' => 'لا يمكن إضافة جرعات إلا للحالات قيد المتابعة.',
            ], 422);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'administered_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ], [
            'name.required' => 'اسم الجرعة مطلوب.',
            'administered_at.required' => 'تاريخ الجرعة مطلوب.',
        ]);

        QuarantineVaccine::create([
            'quarantine_id' => $quarantine->id,
            'user_id' => $vet->id,
            'name' => $data['name'],
            'administered_at' => $data['administered_at'],
            'note' => $data['note'] ?? null,
        ]);

        if (! $quarantine->responsibleVet?->isVeterinarian()) {
            $quarantine->update(['responsible_vet_id' => $vet->id]);
        }

        $quarantine->loadMissing('animal');
        $animal = $quarantine->animal;
        $label = $animal->name ?: $animal->species;

        $notifier->notifyVetHead(
            $quarantine,
            'جرعة وقائية جديدة',
            "سجّل د. {$vet->name} جرعة «{$data['name']}» للحيوان {$label} ({$quarantine->case_number}).",
            'quarantine_vaccine_added'
        );

        $quarantine->load(['animal', 'responsibleVet', 'notes.author', 'vaccines.author']);

        return response()->json([
            'message' => 'تم تسجيل الجرعة الوقائية.',
            'data' => new QuarantineApiResource($quarantine),
        ]);
    }

    public function release(
        Request $request,
        Quarantine $quarantine,
        ReceivingTaskService $receivingTasks
    ): JsonResponse {
        $vet = $this->veterinarianUser($request);
        $this->authorizeQuarantine($quarantine, $vet);

        if ($quarantine->status !== QuarantineStatus::UnderFollowUp) {
            return response()->json([
                'message' => 'لا يمكن إصدار إفراج صحي إلا للحالات قيد المتابعة.',
            ], 422);
        }

        DB::transaction(function () use ($quarantine) {
            $quarantine->update([
                'status' => QuarantineStatus::HealthReleased,
                'released_at' => now()->toDateString(),
            ]);
        });

        $task = $receivingTasks->createFromQuarantineRelease($quarantine->fresh('animal'), $vet);
        $animal = $quarantine->fresh('animal')->animal;

        $message = $task
            ? "تم إصدار قرار الإفراج الصحي وإرسال مهمة استلام لمشرف مجموعة «{$animal->group}»."
            : "تم إصدار قرار الإفراج الصحي. لم يُعثر على مشرف مجموعة لـ «{$animal->group}».";

        return response()->json(['message' => $message]);
    }

    public function close(Request $request, Quarantine $quarantine): JsonResponse
    {
        $vet = $this->veterinarianUser($request);
        $this->authorizeQuarantine($quarantine, $vet);

        if ($quarantine->status !== QuarantineStatus::UnderFollowUp) {
            return response()->json([
                'message' => 'لا يمكن إنهاء هذه الحالة حالياً.',
            ], 422);
        }

        $data = $request->validate([
            'close_reason' => ['required', 'string', 'max:255'],
            'close_notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'close_reason.required' => 'سبب الإنهاء مطلوب.',
        ]);

        $quarantine->update([
            'status' => QuarantineStatus::Failed,
            'closed_at' => now()->toDateString(),
            'close_reason' => trim($data['close_reason'].($data['close_notes'] ? ' — '.$data['close_notes'] : '')),
        ]);

        return response()->json([
            'message' => 'تم إنهاء حالة الحجر وتسجيلها ضمن الحالات التي لم تجتز الحجر.',
        ]);
    }

    private function veterinarianUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->role !== UserRole::Veterinarian->value || ! $user->assigned_group) {
            abort(403, 'هذا المسار مخصص للأطباء البيطريين المسندين لمجموعة.');
        }

        return $user;
    }

    private function authorizeQuarantine(Quarantine $quarantine, User $vet): void
    {
        $quarantine->loadMissing('animal');

        if ($quarantine->animal?->group !== $vet->assigned_group) {
            abort(403, 'ليس لديك صلاحية على حيوانات هذه المجموعة.');
        }
    }
}
