<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\HealthCaseFollowUpKind;
use App\Enums\HealthCaseStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\OperationalNoteKind;
use App\Enums\OperationalNoteStatus;
use App\Enums\ReceivingTaskSource;
use App\Enums\TreatmentReferralStatus;
use App\Enums\ReceivingTaskStatus;
use App\Enums\ReceivingTaskType;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\CareNotification;
use App\Models\HealthCase;
use App\Models\HealthCaseNotification;
use App\Models\OperationalNote;
use App\Models\OperationalNoteNotification;
use App\Models\HospitalCase;
use App\Models\HospitalCaseNotification;
use App\Models\ReceivingTask;
use App\Models\TreatmentReferral;
use App\Models\User;
use App\Models\VetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_vet_portal_feed_includes_read_and_unread_notifications(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $unreadTask = $this->makeReceivingTask($vetHead, 'RCV-2026-601');
        $readTask = $this->makeReceivingTask($vetHead, 'RCV-2026-602');

        VetNotification::create([
            'user_id' => $vetHead->id,
            'receiving_task_id' => $unreadTask->id,
            'title' => 'إشعار غير مقروء',
            'message' => 'رسالة جديدة',
            'read_at' => null,
        ]);

        VetNotification::create([
            'user_id' => $vetHead->id,
            'receiving_task_id' => $readTask->id,
            'title' => 'إشعار مقروء',
            'message' => 'رسالة قديمة',
            'read_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($vetHead)->get('/vet/dashboard');

        $response->assertOk()
            ->assertSee('إشعار غير مقروء')
            ->assertSee('إشعار مقروء')
            ->assertSee('is-unread')
            ->assertSee('is-read')
            ->assertSee('جديد');
    }

    public function test_care_portal_feed_includes_read_and_unread_notifications(): void
    {
        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $unreadTask = $this->makeReceivingTask($careHead, 'RCV-2026-603');
        $readTask = $this->makeReceivingTask($careHead, 'RCV-2026-604');

        CareNotification::create([
            'user_id' => $careHead->id,
            'receiving_task_id' => $unreadTask->id,
            'title' => 'قرار جديد',
            'message' => 'بانتظار المراجعة',
            'read_at' => null,
        ]);

        CareNotification::create([
            'user_id' => $careHead->id,
            'receiving_task_id' => $readTask->id,
            'title' => 'قرار سابق',
            'message' => 'تمت المراجعة',
            'read_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($careHead)->get('/care/dashboard');

        $response->assertOk()
            ->assertSee('قرار جديد')
            ->assertSee('قرار سابق')
            ->assertSee('notification-filters')
            ->assertSee('handleCareNotificationClick', false)
            ->assertSee('data-notification-kind="receiving"', false);
    }

    public function test_marking_care_receiving_notification_read_keeps_record(): void
    {
        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $task = $this->makeReceivingTask($careHead, 'RCV-2026-501');

        $notification = CareNotification::create([
            'user_id' => $careHead->id,
            'receiving_task_id' => $task->id,
            'title' => 'قرار استلام',
            'message' => 'تفاصيل القرار',
            'read_at' => null,
        ]);

        $this->actingAs($careHead)
            ->postJson(route('care.notification.read'), [
                'task_number' => $task->task_number,
            ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('care_notifications', [
            'id' => $notification->id,
            'user_id' => $careHead->id,
        ]);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_health_case_notification_read_endpoint_returns_json(): void
    {
        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'TST-HC-010',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $healthCase = HealthCase::create([
            'case_number' => 'HC-2026-010',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'description' => 'حالة تحتاج مراجعة',
            'follow_up_kind' => HealthCaseFollowUpKind::NeedsReferral,
            'status' => HealthCaseStatus::New,
        ]);

        HealthCaseNotification::create([
            'user_id' => $careHead->id,
            'health_case_id' => $healthCase->id,
            'title' => 'حالة صحية',
            'message' => 'تحتاج مراجعة',
            'read_at' => null,
        ]);

        $this->actingAs($careHead)
            ->postJson(route('care.health.notification.read', $healthCase))
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('url', route('care.health.index', ['case' => $healthCase->case_number]));

        $this->assertDatabaseHas('health_case_notifications', [
            'health_case_id' => $healthCase->id,
            'user_id' => $careHead->id,
        ]);

        $this->assertNotNull(
            HealthCaseNotification::query()
                ->where('health_case_id', $healthCase->id)
                ->where('user_id', $careHead->id)
                ->value('read_at')
        );
    }

    public function test_operational_note_notification_read_endpoint_returns_json(): void
    {
        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $note = OperationalNote::create([
            'note_number' => 'ON-2026-010',
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'note_kind' => OperationalNoteKind::General,
            'summary' => 'ملاحظة عامة',
            'details' => 'تفاصيل الملاحظة',
            'status' => OperationalNoteStatus::New,
            'noted_at' => now(),
        ]);

        OperationalNoteNotification::create([
            'user_id' => $careHead->id,
            'operational_note_id' => $note->id,
            'title' => 'ملاحظة تشغيلية',
            'message' => 'تحتاج مراجعة',
            'read_at' => null,
        ]);

        $this->actingAs($careHead)
            ->postJson(route('care.notes.notification.read', $note))
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('url', route('care.notes.index', ['note' => $note->note_number]));

        $this->assertDatabaseHas('operational_note_notifications', [
            'operational_note_id' => $note->id,
            'user_id' => $careHead->id,
        ]);

        $this->assertNotNull(
            OperationalNoteNotification::query()
                ->where('operational_note_id', $note->id)
                ->where('user_id', $careHead->id)
                ->value('read_at')
        );
    }

    public function test_vet_dashboard_does_not_show_recent_alerts_section(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $this->actingAs($vetHead)
            ->get('/vet/dashboard')
            ->assertOk()
            ->assertDontSee('آخر التنبيهات المهمة');
    }

    public function test_vet_dashboard_urgent_cases_table_has_no_current_status_column(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $this->actingAs($vetHead)
            ->get('/vet/dashboard')
            ->assertOk()
            ->assertSee('الوضع الإجرائي')
            ->assertDontSee('الوضع الحالي');
    }

    public function test_vet_portal_feed_includes_hospital_case_notifications(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $hospitalCase = $this->makeHospitalCase('VH-2026-099');

        HospitalCaseNotification::create([
            'user_id' => $vetHead->id,
            'hospital_case_id' => $hospitalCase->id,
            'title' => 'طلب اعتماد خروج',
            'message' => 'الحيوان جاهز للخروج',
            'read_at' => null,
        ]);

        $this->actingAs($vetHead)
            ->get('/vet/dashboard')
            ->assertOk()
            ->assertSee('طلب اعتماد خروج')
            ->assertSee('hospital_case', false);
    }

    public function test_hospital_case_notification_read_endpoint_returns_json(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $hospitalCase = $this->makeHospitalCase('VH-2026-100');

        HospitalCaseNotification::create([
            'user_id' => $vetHead->id,
            'hospital_case_id' => $hospitalCase->id,
            'title' => 'طلب اعتماد ذبح',
            'message' => 'يحتاج مراجعة',
            'read_at' => null,
        ]);

        $this->actingAs($vetHead)
            ->postJson(route('vet.hospital.notification.read', $hospitalCase))
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertNotNull(
            HospitalCaseNotification::query()
                ->where('hospital_case_id', $hospitalCase->id)
                ->where('user_id', $vetHead->id)
                ->value('read_at')
        );
    }

    public function test_vet_notification_feed_endpoint_returns_json(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $task = $this->makeReceivingTask($vetHead, 'RCV-2026-701');

        VetNotification::create([
            'user_id' => $vetHead->id,
            'receiving_task_id' => $task->id,
            'title' => 'إشعار مباشر',
            'message' => 'تحديث فوري',
            'read_at' => null,
        ]);

        $response = $this->actingAs($vetHead)
            ->getJson(route('vet.notifications.feed'));

        $response->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonStructure(['unread_count', 'html', 'version']);

        $this->assertStringContainsString('إشعار مباشر', (string) $response->json('html'));
    }

    public function test_care_notification_feed_endpoint_returns_json(): void
    {
        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $task = $this->makeReceivingTask($careHead, 'RCV-2026-702');

        CareNotification::create([
            'user_id' => $careHead->id,
            'receiving_task_id' => $task->id,
            'title' => 'قرار فوري',
            'message' => 'تحديث مباشر',
            'read_at' => null,
        ]);

        $response = $this->actingAs($careHead)
            ->getJson(route('care.notifications.feed'));

        $response->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonStructure(['unread_count', 'html', 'version']);

        $this->assertStringContainsString('قرار فوري', (string) $response->json('html'));
    }

    public function test_vet_portal_layout_exposes_realtime_feed_url(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $this->actingAs($vetHead)
            ->get('/vet/dashboard')
            ->assertOk()
            ->assertSee('window.portalNotificationsFeedUrl', false);
    }

    public function test_care_portal_layout_exposes_realtime_feed_url(): void
    {
        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $this->actingAs($careHead)
            ->get('/care/dashboard')
            ->assertOk()
            ->assertSee('window.portalNotificationsFeedUrl', false);
    }

    private function makeHospitalCase(string $caseNumber): HospitalCase
    {
        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'TST-'.$caseNumber,
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $healthCase = HealthCase::create([
            'case_number' => 'HC-'.$caseNumber,
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'description' => 'حالة مستشفى',
            'follow_up_kind' => HealthCaseFollowUpKind::NeedsReferral,
            'status' => HealthCaseStatus::Referred,
        ]);

        $referral = TreatmentReferral::create([
            'referral_number' => 'TR-'.$caseNumber,
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => 'الغزلان',
            'status' => TreatmentReferralStatus::Approved,
            'referred_by' => $careHead->id,
            'referred_at' => now(),
        ]);

        return HospitalCase::create([
            'case_number' => $caseNumber,
            'treatment_referral_id' => $referral->id,
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => 'الغزلان',
            'chief_complaint' => 'التهاب',
            'status' => HospitalCaseStatus::PendingDischargeApproval,
            'admitted_by' => $vet->id,
            'admitted_at' => now(),
        ]);
    }

    private function makeReceivingTask(User $issuer, string $taskNumber = 'RCV-2026-500'): ReceivingTask
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'TST-'.$taskNumber,
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::PendingReceipt->value,
            'registered_at' => now(),
        ]);

        return ReceivingTask::create([
            'task_number' => $taskNumber,
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'status' => ReceivingTaskStatus::Pending,
            'task_type' => ReceivingTaskType::AfterHealthRelease,
            'source' => ReceivingTaskSource::Quarantine,
            'decision_date' => now()->toDateString(),
            'decision_issued_by' => $issuer->id,
        ]);
    }
}
