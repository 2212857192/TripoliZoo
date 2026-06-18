<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\QuarantineStatus;
use App\Enums\ReceivingTaskSource;
use App\Enums\ReceivingTaskStatus;
use App\Enums\ReceivingTaskType;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\Quarantine;
use App\Models\QuarantineVaccine;
use App\Models\ReceivingTask;
use App\Models\User;
use App\Services\ReceivingTaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalDecisionDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_quarantine_release_stores_vaccines_as_decision_treatments(): void
    {
        User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'TST-QR-001',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => now(),
        ]);

        $quarantine = Quarantine::create([
            'case_number' => 'QR-2026-101',
            'animal_id' => $animal->id,
            'reason' => 'اشتباه مرض',
            'initial_health_status' => 'متوسط',
            'status' => QuarantineStatus::UnderFollowUp,
            'entry_date' => now(),
            'created_by' => $vetHead->id,
        ]);

        QuarantineVaccine::create([
            'quarantine_id' => $quarantine->id,
            'user_id' => $vetHead->id,
            'name' => 'لقاح الحمى القلاعية',
            'administered_at' => now()->toDateString(),
        ]);

        $task = app(ReceivingTaskService::class)->createFromQuarantineRelease($quarantine->fresh('animal'), $vetHead);

        $this->assertNotNull($task);
        $this->assertSame(['لقاح الحمى القلاعية'], $task->fresh()->decision_treatments);
    }

    public function test_existing_quarantine_task_shows_vaccines_and_decision_notes_on_show_page(): void
    {
        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'TST-QR-002',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::PendingReceipt->value,
            'registered_at' => now(),
        ]);

        $quarantine = Quarantine::create([
            'case_number' => 'QR-2026-102',
            'animal_id' => $animal->id,
            'reason' => 'اشتباه مرض',
            'initial_health_status' => 'متوسط',
            'status' => QuarantineStatus::HealthReleased,
            'entry_date' => now(),
            'released_at' => now()->toDateString(),
            'created_by' => $vetHead->id,
        ]);

        QuarantineVaccine::create([
            'quarantine_id' => $quarantine->id,
            'user_id' => $vetHead->id,
            'name' => 'مضاد طفيليات',
            'administered_at' => now()->toDateString(),
        ]);

        $task = ReceivingTask::create([
            'task_number' => 'RCV-2026-QR-01',
            'animal_id' => $animal->id,
            'quarantine_id' => $quarantine->id,
            'supervisor_id' => $supervisor->id,
            'status' => ReceivingTaskStatus::Pending,
            'task_type' => ReceivingTaskType::AfterHealthRelease,
            'source' => ReceivingTaskSource::Quarantine,
            'decision_date' => now()->toDateString(),
            'decision_issued_by' => $vetHead->id,
            'decision_notes' => 'اكتملت فترة الحجر الصحي وصدر قرار الإفراج.',
            'receipt_note' => 'ملاحظة مشرف لا يجب عرضها في التفاصيل الطبية',
        ]);

        $response = $this->actingAs($careHead)->get(route('care.decisions.show', $task));

        $response->assertOk();
        $response->assertSee('مضاد طفيليات', false);
        $response->assertSee('اكتملت فترة الحجر الصحي وصدر قرار الإفراج.', false);
        $response->assertDontSee('ملاحظة مشرف لا يجب عرضها في التفاصيل الطبية', false);
    }
}
