<?php

namespace App\Http\Controllers\Api;

use App\Enums\ReceivingTaskStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReceivingTaskResource;
use App\Models\ReceivingTask;
use App\Models\User;
use App\Services\ReceivingTaskService;
use App\Services\SupervisorNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceivingTaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $status = $request->query('status');
        $allowed = [
            ReceivingTaskStatus::Pending->value,
            ReceivingTaskStatus::TemporarilyUnable->value,
            ReceivingTaskStatus::Received->value,
        ];

        $query = ReceivingTask::query()
            ->with(['animal', 'decisionIssuer'])
            ->where('supervisor_id', $supervisor->id)
            ->orderByDesc('created_at');

        if (is_string($status) && in_array($status, $allowed, true)) {
            $query->where('status', $status);
        }

        return response()->json([
            'data' => ReceivingTaskResource::collection($query->get()),
            'pending_count' => ReceivingTask::query()
                ->where('supervisor_id', $supervisor->id)
                ->where('status', ReceivingTaskStatus::Pending->value)
                ->count(),
        ]);
    }

    public function show(Request $request, ReceivingTask $receivingTask): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);
        $this->authorizeTask($receivingTask, $supervisor);

        $receivingTask->load(['animal', 'decisionIssuer']);
        app(SupervisorNotificationService::class)->markTaskAsReadForUser($receivingTask, $supervisor);

        return response()->json([
            'data' => new ReceivingTaskResource($receivingTask),
        ]);
    }

    public function confirm(Request $request, ReceivingTask $receivingTask, ReceivingTaskService $service): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);
        $this->authorizeTask($receivingTask, $supervisor);

        if (! $receivingTask->canConfirmReceipt()) {
            return response()->json(['message' => 'لا يمكن تأكيد الاستلام لهذه المهمة حالياً.'], 422);
        }

        $data = $request->validate([
            'receipt_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $service->confirmReceipt($receivingTask, $supervisor, $data['receipt_note'] ?? null);

        $receivingTask->refresh()->load(['animal', 'decisionIssuer']);

        return response()->json([
            'message' => 'تم تأكيد استلام الحيوان.',
            'data' => new ReceivingTaskResource($receivingTask),
        ]);
    }

    public function delay(Request $request, ReceivingTask $receivingTask, ReceivingTaskService $service): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);
        $this->authorizeTask($receivingTask, $supervisor);

        if (! $receivingTask->canRecordDelay()) {
            return response()->json(['message' => 'لا يمكن تسجيل تعذر الاستلام إلا للمهام بانتظار الاستلام.'], 422);
        }

        $data = $request->validate([
            'delay_reason' => ['required', 'string', 'max:500'],
            'delay_extra_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $service->recordTemporaryDelay(
            $receivingTask,
            $data['delay_reason'],
            $data['delay_extra_note'] ?? null,
        );

        $receivingTask->refresh()->load(['animal', 'decisionIssuer']);

        return response()->json([
            'message' => 'تم تسجيل تعذر الاستلام مؤقتًا.',
            'data' => new ReceivingTaskResource($receivingTask),
        ]);
    }

    private function supervisorUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->role !== UserRole::GroupSupervisor->value) {
            abort(403, 'هذا المسار مخصص لمشرفي المجموعات.');
        }

        return $user;
    }

    private function authorizeTask(ReceivingTask $task, User $supervisor): void
    {
        if ($task->supervisor_id !== $supervisor->id) {
            abort(403, 'ليس لديك صلاحية على مهمة الاستلام هذه.');
        }
    }
}
