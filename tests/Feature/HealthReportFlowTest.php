<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\HealthReportStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\HealthReport;
use App\Models\HealthReportNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HealthReportFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_create_health_report_and_notify_group_vet_only(): void
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'GAZ-HR-01',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        Sanctum::actingAs($supervisor);

        $response = $this->postJson('/api/auth/supervisor/health-reports', [
            'animal_code' => 'GAZ-HR-01',
            'description' => 'خمول واضح منذ الصباح',
            'is_urgent' => true,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('health_reports', [
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'status' => HealthReportStatus::Sent->value,
        ]);

        $report = HealthReport::query()->first();

        $this->assertDatabaseMissing('health_report_notifications', [
            'user_id' => $careHead->id,
            'health_report_id' => $report->id,
        ]);

        $this->assertDatabaseHas('health_report_notifications', [
            'user_id' => $vet->id,
            'health_report_id' => $report->id,
        ]);

        Sanctum::actingAs($vet);

        $this->getJson('/api/auth/doctor/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonFragment([
                'type' => 'health_report',
                'report_number' => $report->report_number,
            ]);

        $this->getJson('/api/auth/doctor/dashboard')
            ->assertOk()
            ->assertJsonPath('unread_notifications', 1);
    }

    public function test_supervisor_can_upload_attachment_with_health_report(): void
    {
        Storage::fake('public');

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'GAZ-HR-03',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        Sanctum::actingAs($supervisor);

        $file = UploadedFile::fake()->create('injury.jpg', 100, 'image/jpeg');

        $this->post('/api/auth/supervisor/health-reports', [
            'animal_code' => 'GAZ-HR-03',
            'description' => 'جروح في الرجل',
            'is_urgent' => '1',
            'attachment' => $file,
        ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.has_attachment', true);

        $report = HealthReport::query()->first();

        $this->assertTrue($report->has_attachment);
        $this->assertNotNull($report->attachment_path);
        Storage::disk('public')->assertExists($report->attachment_path);
    }

    public function test_doctor_can_receive_and_close_health_report(): void
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'GAZ-HR-02',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $report = HealthReport::create([
            'report_number' => 'RP-2026-0001',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'description' => 'جروح سطحية',
            'status' => HealthReportStatus::Sent,
        ]);

        Sanctum::actingAs($vet);

        $this->getJson("/api/auth/doctor/health-reports/{$report->report_number}")
            ->assertOk()
            ->assertJsonPath('data.status', HealthReportStatus::Received->value);

        $this->assertDatabaseHas('health_report_notifications', [
            'user_id' => $supervisor->id,
            'health_report_id' => $report->id,
        ]);

        $this->postJson("/api/auth/doctor/health-reports/{$report->report_number}/close", [
            'doctor_note' => 'تمت المعاينة ولا حاجة لإحالة',
        ])->assertOk();

        $report->refresh();
        $this->assertSame(HealthReportStatus::Closed, $report->status);
        $this->assertSame('تمت المعاينة ولا حاجة لإحالة', $report->doctor_note);
    }

    public function test_supervisor_and_doctor_can_download_health_report_attachment(): void
    {
        Storage::fake('public');

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'GAZ-HR-04',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $path = 'health-reports/test-attachment.jpg';
        Storage::disk('public')->put($path, 'fake-image');

        $report = HealthReport::create([
            'report_number' => 'RP-2026-0099',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'description' => 'بلاغ بمرفق',
            'status' => HealthReportStatus::Sent,
            'has_attachment' => true,
            'attachment_path' => $path,
        ]);

        Sanctum::actingAs($supervisor);
        $this->get("/api/auth/health-reports/{$report->report_number}/attachment")
            ->assertOk();

        Sanctum::actingAs($vet);
        $this->get("/api/auth/health-reports/{$report->report_number}/attachment")
            ->assertOk();
    }
}
