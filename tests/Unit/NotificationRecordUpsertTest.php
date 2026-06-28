<?php

namespace Tests\Unit;

use App\Enums\AnimalStatus;
use App\Enums\HealthReportStatus;
use App\Enums\ReceivingTaskSource;
use App\Enums\ReceivingTaskStatus;
use App\Enums\ReceivingTaskType;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\CareNotification;
use App\Models\HealthReport;
use App\Models\HealthReportNotification;
use App\Models\ReceivingTask;
use App\Models\User;
use App\Services\NotificationRecordUpsert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationRecordUpsertTest extends TestCase
{
    use RefreshDatabase;

    public function test_upsert_does_not_reset_read_at_on_update(): void
    {
        $user = User::factory()->create();
        $task = $this->createReceivingTask();

        NotificationRecordUpsert::save(
            CareNotification::class,
            [
                'user_id' => $user->id,
                'receiving_task_id' => $task->id,
            ],
            [
                'title' => 'عنوان أول',
                'message' => 'رسالة أولى',
            ],
        );

        $notification = CareNotification::query()->firstOrFail();
        $notification->update(['read_at' => now()->subMinute()]);
        $readAt = $notification->fresh()->read_at;

        NotificationRecordUpsert::save(
            CareNotification::class,
            [
                'user_id' => $user->id,
                'receiving_task_id' => $task->id,
            ],
            [
                'title' => 'عنوان أول',
                'message' => 'رسالة أولى',
            ],
        );

        $this->assertNotNull($notification->fresh()->read_at);
        $this->assertSame(
            $readAt?->toDateTimeString(),
            $notification->fresh()->read_at?->toDateTimeString(),
        );
    }

    public function test_upsert_reopens_notification_when_content_changes_and_requested(): void
    {
        $user = User::factory()->create();
        $report = $this->createHealthReport();

        NotificationRecordUpsert::save(
            HealthReportNotification::class,
            [
                'user_id' => $user->id,
                'health_report_id' => $report->id,
            ],
            [
                'title' => 'بلاغ قديم',
                'message' => 'تفاصيل قديمة',
            ],
        );

        $notification = HealthReportNotification::query()->firstOrFail();
        $notification->update(['read_at' => now()->subHour()]);

        NotificationRecordUpsert::save(
            HealthReportNotification::class,
            [
                'user_id' => $user->id,
                'health_report_id' => $report->id,
            ],
            [
                'title' => 'بلاغ محدّث',
                'message' => 'تفاصيل جديدة',
            ],
            reopenOnContentChange: true,
        );

        $this->assertNull($notification->fresh()->read_at);
    }

    private function createReceivingTask(): ReceivingTask
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'NTF-TASK-01',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::PendingReceipt->value,
            'registered_at' => now(),
        ]);

        return ReceivingTask::create([
            'task_number' => 'RCV-NTF-001',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'status' => ReceivingTaskStatus::Pending,
            'task_type' => ReceivingTaskType::AfterHealthRelease,
            'source' => ReceivingTaskSource::Quarantine,
            'decision_date' => now()->toDateString(),
        ]);
    }

    private function createHealthReport(): HealthReport
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'NTF-HR-01',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        return HealthReport::create([
            'report_number' => 'RP-NTF-001',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'description' => 'بلاغ اختباري',
            'status' => HealthReportStatus::Sent,
        ]);
    }
}
