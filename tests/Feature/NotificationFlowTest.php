<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\QuarantineStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\CareNotification;
use App\Models\Quarantine;
use App\Models\QuarantineNotification;
use App\Models\ReceivingTask;
use App\Models\SupervisorNotification;
use App\Models\User;
use App\Services\QuarantineNotificationService;
use App\Services\ReceivingTaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_quarantine_notify_only_group_veterinarian_not_vet_head(): void
    {
        $vetHead = User::factory()->create([
            'name' => 'رئيس المستشفى',
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $vet = User::factory()->create([
            'name' => 'طبيب الغزلان',
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $supervisor = User::factory()->create([
            'name' => 'مشرف الغزلان',
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'TST-0001',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => now(),
        ]);

        $quarantine = Quarantine::create([
            'case_number' => 'QR-2026-999',
            'animal_id' => $animal->id,
            'reason' => '',
            'initial_health_status' => 'جيد',
            'status' => QuarantineStatus::UnderFollowUp,
            'entry_date' => now(),
            'responsible_vet_id' => $vet->id,
            'created_by' => $vetHead->id,
        ]);

        app(QuarantineNotificationService::class)->notifyGroupVets($quarantine);

        $this->assertDatabaseHas('quarantine_notifications', [
            'user_id' => $vet->id,
            'quarantine_id' => $quarantine->id,
        ]);

        $this->assertDatabaseMissing('quarantine_notifications', [
            'user_id' => $vetHead->id,
            'quarantine_id' => $quarantine->id,
        ]);

        $this->assertDatabaseMissing('quarantine_notifications', [
            'user_id' => $supervisor->id,
            'quarantine_id' => $quarantine->id,
        ]);
    }

    public function test_quarantine_release_notifies_supervisor_only(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'TST-0002',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => now(),
        ]);

        $quarantine = Quarantine::create([
            'case_number' => 'QR-2026-998',
            'animal_id' => $animal->id,
            'reason' => '',
            'initial_health_status' => 'جيد',
            'status' => QuarantineStatus::UnderFollowUp,
            'entry_date' => now(),
            'responsible_vet_id' => $vet->id,
            'created_by' => $vetHead->id,
        ]);

        $quarantine->update([
            'status' => QuarantineStatus::HealthReleased,
            'released_at' => now()->toDateString(),
        ]);

        $task = app(ReceivingTaskService::class)->createFromQuarantineRelease($quarantine->fresh('animal'), $vetHead);

        $this->assertNotNull($task);
        $this->assertDatabaseHas('receiving_tasks', [
            'id' => $task->id,
            'supervisor_id' => $supervisor->id,
        ]);

        $this->assertDatabaseHas('supervisor_notifications', [
            'user_id' => $supervisor->id,
            'receiving_task_id' => $task->id,
        ]);

        $this->assertDatabaseMissing('supervisor_notifications', [
            'user_id' => $vet->id,
            'receiving_task_id' => $task->id,
        ]);

        $this->assertDatabaseHas('care_notifications', [
            'user_id' => $careHead->id,
            'receiving_task_id' => $task->id,
        ]);

        $this->assertDatabaseHas('vet_notifications', [
            'user_id' => $vetHead->id,
            'receiving_task_id' => $task->id,
        ]);

        $this->assertDatabaseMissing('care_notifications', [
            'user_id' => $supervisor->id,
            'receiving_task_id' => $task->id,
        ]);
    }

    public function test_receiving_delay_notifies_care_and_vet_head(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
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

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'TST-0003',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::PendingReceipt->value,
            'registered_at' => now(),
        ]);

        $task = ReceivingTask::create([
            'task_number' => 'RCV-2026-099',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'status' => \App\Enums\ReceivingTaskStatus::Pending,
            'task_type' => \App\Enums\ReceivingTaskType::AfterHealthRelease,
            'source' => \App\Enums\ReceivingTaskSource::Quarantine,
            'decision_date' => now()->toDateString(),
            'decision_issued_by' => $vetHead->id,
        ]);

        app(ReceivingTaskService::class)->recordTemporaryDelay($task, 'القفص غير جاهز');

        $this->assertDatabaseHas('care_notifications', [
            'user_id' => $careHead->id,
            'receiving_task_id' => $task->id,
        ]);

        $this->assertDatabaseHas('vet_notifications', [
            'user_id' => $vetHead->id,
            'receiving_task_id' => $task->id,
        ]);

        $this->assertDatabaseMissing('vet_notifications', [
            'user_id' => $vet->id,
            'receiving_task_id' => $task->id,
        ]);
    }

    public function test_receiving_completed_notifies_care_and_vet_head(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
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

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'TST-0004',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::PendingReceipt->value,
            'registered_at' => now(),
        ]);

        $task = ReceivingTask::create([
            'task_number' => 'RCV-2026-100',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'status' => \App\Enums\ReceivingTaskStatus::Pending,
            'task_type' => \App\Enums\ReceivingTaskType::AfterHealthRelease,
            'source' => \App\Enums\ReceivingTaskSource::Quarantine,
            'decision_date' => now()->toDateString(),
            'decision_issued_by' => $vetHead->id,
        ]);

        app(ReceivingTaskService::class)->confirmReceipt($task, $supervisor, 'تم التسليم بنجاح');

        $this->assertDatabaseHas('care_notifications', [
            'user_id' => $careHead->id,
            'receiving_task_id' => $task->id,
        ]);

        $this->assertDatabaseHas('vet_notifications', [
            'user_id' => $vetHead->id,
            'receiving_task_id' => $task->id,
        ]);

        $this->assertDatabaseMissing('vet_notifications', [
            'user_id' => $vet->id,
            'receiving_task_id' => $task->id,
        ]);

        $careNotification = CareNotification::query()
            ->where('user_id', $careHead->id)
            ->where('receiving_task_id', $task->id)
            ->first();

        $this->assertNotNull($careNotification);
        $this->assertStringContainsString('تم استلام', $careNotification->title);
    }
}
