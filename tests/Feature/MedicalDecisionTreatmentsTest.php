<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\ReceivingTaskSource;
use App\Enums\ReceivingTaskStatus;
use App\Enums\ReceivingTaskType;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\ReceivingTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalDecisionTreatmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_medical_decision_show_displays_treatments_as_list(): void
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
            'code' => 'TST-MD-001',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::PendingReceipt->value,
            'registered_at' => now(),
        ]);

        $task = ReceivingTask::create([
            'task_number' => 'RCV-2026-MD-01',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'status' => ReceivingTaskStatus::Pending,
            'task_type' => ReceivingTaskType::AfterTreatment,
            'source' => ReceivingTaskSource::Hospital,
            'decision_date' => now()->toDateString(),
            'decision_issued_by' => $vetHead->id,
            'decision_notes' => 'خروج بعد اكتمال العلاج.',
            'decision_treatments' => [
                'مضاد حيوي واسع الطيف',
                'ضمادات يومية',
            ],
        ]);

        $response = $this->actingAs($careHead)->get(route('care.decisions.show', $task));

        $response->assertOk();
        $response->assertSee('مضاد حيوي واسع الطيف', false);
        $response->assertSee('ضمادات يومية', false);
        $response->assertSee('خروج بعد اكتمال العلاج.', false);
        $response->assertSee('العلاجات', false);
    }
}
