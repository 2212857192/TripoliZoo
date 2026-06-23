<?php

namespace App\Http\Controllers\Api;

use App\Enums\HealthReportStatus;
use App\Enums\ReceivingTaskStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\HealthReport;
use App\Models\ReceivingTask;
use App\Models\SupervisorNotification;
use App\Models\User;
use App\Services\SupervisorNutritionRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupervisorDashboardController extends Controller
{
    public function show(Request $request, SupervisorNutritionRecommendationService $nutrition): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->role !== UserRole::GroupSupervisor->value) {
            abort(403, 'هذا المسار مخصص لمشرفي المجموعات.');
        }

        $dietRecommendations = $nutrition->dashboardItemsForGroup($user->assigned_group);

        return response()->json([
            'supervisor_name' => $user->name,
            'group_name' => $user->assigned_group,
            'pending_receiving_tasks' => ReceivingTask::query()
                ->where('supervisor_id', $user->id)
                ->where('status', ReceivingTaskStatus::Pending->value)
                ->count(),
            'unread_notifications' => SupervisorNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count(),
            'pending_health_reports_count' => HealthReport::query()
                ->where('group', $user->assigned_group)
                ->whereIn('status', [
                    HealthReportStatus::Sent->value,
                    HealthReportStatus::Received->value,
                ])
                ->count(),
            'active_diet_recommendations' => count($dietRecommendations),
            'diet_recommendations' => $dietRecommendations,
        ]);
    }
}
