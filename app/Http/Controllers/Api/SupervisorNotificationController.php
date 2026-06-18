<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\HealthReportNotification;
use App\Models\SupervisorNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupervisorNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $receivingItems = SupervisorNotification::query()
            ->where('user_id', $supervisor->id)
            ->with(['receivingTask:id,task_number'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (SupervisorNotification $notification) => [
                'id' => $notification->id,
                'type' => 'receiving_task',
                'title' => $notification->title,
                'message' => $notification->message,
                'task_number' => $notification->receivingTask?->task_number,
                'report_number' => null,
                'is_read' => $notification->read_at !== null,
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at' => $notification->created_at?->toIso8601String(),
            ]);

        $healthItems = HealthReportNotification::query()
            ->where('user_id', $supervisor->id)
            ->with(['healthReport:id,report_number'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (HealthReportNotification $notification) => [
                'id' => $notification->id,
                'type' => 'health_report_update',
                'title' => $notification->title,
                'message' => $notification->message,
                'task_number' => null,
                'report_number' => $notification->healthReport?->report_number,
                'is_read' => $notification->read_at !== null,
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at' => $notification->created_at?->toIso8601String(),
            ]);

        $notifications = $receivingItems
            ->concat($healthItems)
            ->sortByDesc('created_at')
            ->values()
            ->take(50);

        $unreadCount = SupervisorNotification::query()
            ->where('user_id', $supervisor->id)
            ->whereNull('read_at')
            ->count()
            + HealthReportNotification::query()
                ->where('user_id', $supervisor->id)
                ->whereNull('read_at')
                ->count();

        return response()->json([
            'data' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markRead(Request $request, SupervisorNotification $notification): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        if ($notification->user_id !== $supervisor->id) {
            abort(403, 'لا يمكنك تعديل هذا الإشعار.');
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['ok' => true]);
    }

    public function markHealthReportRead(Request $request, HealthReportNotification $notification): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        if ($notification->user_id !== $supervisor->id) {
            abort(403, 'لا يمكنك تعديل هذا الإشعار.');
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        SupervisorNotification::query()
            ->where('user_id', $supervisor->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        HealthReportNotification::query()
            ->where('user_id', $supervisor->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    private function supervisorUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->role !== UserRole::GroupSupervisor->value || ! $user->assigned_group) {
            abort(403, 'هذا المسار مخصص لمشرفي المجموعات المسندين لمجموعة.');
        }

        return $user;
    }
}
