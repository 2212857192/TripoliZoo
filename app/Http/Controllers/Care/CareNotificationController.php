<?php

namespace App\Http\Controllers\Care;

use App\Http\Controllers\Controller;
use App\Models\CareNotification;
use App\Models\ReceivingTask;
use App\Services\CareNotificationService;
use App\Services\HealthCaseNotificationService;
use App\Services\OperationalNoteNotificationService;
use App\Services\PortalNotificationFeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CareNotificationController extends Controller
{
    public function __construct(private PortalNotificationFeedService $feedService) {}

    public function feed(): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        return response()->json($this->feedService->forCare($user));
    }

    public function markReadByTask(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        $data = $request->validate([
            'task_number' => ['required', 'string', 'max:50'],
        ]);

        $task = ReceivingTask::query()
            ->where('task_number', $data['task_number'])
            ->first();

        if ($task) {
            app(CareNotificationService::class)->markTaskAsReadForUser($task, $user);
        }

        return response()->json(['ok' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        CareNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        app(HealthCaseNotificationService::class)->markAllAsReadForUser($user);
        app(OperationalNoteNotificationService::class)->markAllAsReadForUser($user);

        return response()->json(['ok' => true]);
    }
}
