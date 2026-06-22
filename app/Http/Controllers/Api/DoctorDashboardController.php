<?php

namespace App\Http\Controllers\Api;

use App\Enums\QuarantineStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\HealthReportNotification;
use App\Models\Quarantine;
use App\Models\QuarantineNotification;
use App\Models\User;
use App\Models\VetNotification;
use App\Services\DoctorMedicalCaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorDashboardController extends Controller
{
    public function show(Request $request, DoctorMedicalCaseService $cases): JsonResponse
    {
        $vet = $this->veterinarianUser($request);
        $group = $vet->assigned_group;

        $activeCount = Quarantine::query()
            ->where('status', QuarantineStatus::UnderFollowUp)
            ->whereHas('animal', fn ($q) => $q->where('group', $group))
            ->count();

        $unreadCount = QuarantineNotification::query()
            ->where('user_id', $vet->id)
            ->whereNull('read_at')
            ->count()
            + VetNotification::query()
                ->where('user_id', $vet->id)
                ->whereNull('read_at')
                ->count()
            + HealthReportNotification::query()
                ->where('user_id', $vet->id)
                ->whereNull('read_at')
                ->count();

        $quarantineAlerts = QuarantineNotification::query()
            ->where('user_id', $vet->id)
            ->whereNull('read_at')
            ->with(['quarantine.animal'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (QuarantineNotification $notification) => [
                'title' => $notification->title,
                'subtitle' => $notification->message,
                'case_number' => $notification->quarantine?->case_number,
                'report_number' => null,
                'urgent' => true,
                'sort_at' => $notification->created_at,
            ]);

        $healthReportAlerts = HealthReportNotification::query()
            ->where('user_id', $vet->id)
            ->whereNull('read_at')
            ->with(['healthReport'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (HealthReportNotification $notification) => [
                'title' => $notification->title,
                'subtitle' => $notification->message,
                'case_number' => null,
                'report_number' => $notification->healthReport?->report_number,
                'urgent' => str_contains($notification->title, 'عاجل'),
                'sort_at' => $notification->created_at,
            ]);

        $alerts = $quarantineAlerts
            ->concat($healthReportAlerts)
            ->sortByDesc('sort_at')
            ->take(5)
            ->map(fn (array $alert) => [
                'title' => $alert['title'],
                'subtitle' => $alert['subtitle'],
                'case_number' => $alert['case_number'],
                'report_number' => $alert['report_number'],
                'urgent' => $alert['urgent'],
            ])
            ->values()
            ->all();

        return response()->json([
            'doctor_name' => $vet->name,
            'group_name' => $group,
            'quarantine_active_count' => $activeCount,
            'active_field_cases_count' => $cases->activeFieldCount($group),
            'active_hospital_cases_count' => $cases->activeHospitalCount($group),
            'unread_notifications' => $unreadCount,
            'alerts' => $alerts,
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
}
