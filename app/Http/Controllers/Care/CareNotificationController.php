<?php

namespace App\Http\Controllers\Care;

use App\Http\Controllers\Controller;
use App\Models\CareNotification;
use App\Models\ReceivingTask;
use App\Services\CareNotificationService;
use App\Services\OperationalNoteNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CareNotificationController extends Controller
{
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

        app(OperationalNoteNotificationService::class)->markAllAsReadForUser($user);

        return response()->json(['ok' => true]);
    }
}
