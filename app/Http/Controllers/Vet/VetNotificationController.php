<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\AutopsyReferral;
use App\Models\ReceivingTask;
use App\Models\TreatmentReferral;
use App\Models\VetNotification;
use App\Services\AutopsyReferralNotificationService;
use App\Services\TreatmentReferralNotificationService;
use App\Services\VetNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VetNotificationController extends Controller
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
            app(VetNotificationService::class)->markTaskAsReadForUser($task, $user);
        }

        return response()->json(['ok' => true]);
    }

    public function markReadByReferral(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        $data = $request->validate([
            'referral_number' => ['required', 'string', 'max:50'],
        ]);

        $referral = TreatmentReferral::query()
            ->where('referral_number', $data['referral_number'])
            ->first();

        if ($referral) {
            app(TreatmentReferralNotificationService::class)->markAsReadForUser($referral, $user);
        }

        return response()->json(['ok' => true]);
    }

    public function markReadByAutopsyReferral(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        $data = $request->validate([
            'referral_number' => ['required', 'string', 'max:50'],
        ]);

        $referral = AutopsyReferral::query()
            ->where('referral_number', $data['referral_number'])
            ->first();

        if ($referral) {
            app(AutopsyReferralNotificationService::class)->markAsReadForUser($referral, $user);
        }

        return response()->json(['ok' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        VetNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        app(TreatmentReferralNotificationService::class)->markAllAsReadForUser($user);
        app(AutopsyReferralNotificationService::class)->markAllAsReadForUser($user);

        return response()->json(['ok' => true]);
    }
}
