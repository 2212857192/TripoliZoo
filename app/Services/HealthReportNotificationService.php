<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\HealthReport;
use App\Models\HealthReportNotification;
use App\Models\User;

class HealthReportNotificationService
{
    public function __construct(private FcmPushService $fcm) {}

    public function notifyNewReport(HealthReport $report): void
    {
        $report->loadMissing(['animal', 'supervisor']);
        $animal = $report->animal;

        if (! $animal) {
            return;
        }

        $label = $animal->displayLabel();
        $urgentPrefix = $report->is_urgent ? 'عاجل — ' : '';
        $title = "{$urgentPrefix}بلاغ صحي جديد — {$report->group}";
        $message = "أرسل {$report->supervisor?->name} بلاغاً عن {$label} ({$animal->code}): {$report->description}";

        $vets = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::Veterinarian->value)
            ->where('assigned_group', $report->group)
            ->get();

        foreach ($vets as $user) {
            $this->storeNotification($user, $report, $title, $message);
        }

        if ($vets->isNotEmpty()) {
            $this->fcm->sendToUsers(
                $vets,
                'حديقة حيوان طرابلس',
                $title,
                [
                    'type' => 'health_report_new',
                    'report_number' => $report->report_number,
                    'route' => '/doctor/reports',
                    'group' => $report->group,
                ]
            );
        }
    }

    public function notifySupervisorOfUpdate(HealthReport $report, string $title, string $message): void
    {
        $report->loadMissing('supervisor');
        $supervisor = $report->supervisor;

        if (! $supervisor) {
            return;
        }

        $this->storeNotification($supervisor, $report, $title, $message);

        $this->fcm->sendToUsers(
            collect([$supervisor]),
            'حديقة حيوان طرابلس',
            $title,
            [
                'type' => 'health_report_update',
                'report_number' => $report->report_number,
                'route' => '/supervisor/health-reports',
                'group' => $report->group,
            ]
        );
    }

    public function markAsReadForUser(HealthReport $report, User $user): void
    {
        HealthReportNotification::query()
            ->where('user_id', $user->id)
            ->where('health_report_id', $report->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function storeNotification(User $user, HealthReport $report, string $title, string $message): void
    {
        NotificationRecordUpsert::save(
            HealthReportNotification::class,
            [
                'user_id' => $user->id,
                'health_report_id' => $report->id,
            ],
            [
                'title' => $title,
                'message' => $message,
            ],
            reopenOnContentChange: true,
        );
    }
}
