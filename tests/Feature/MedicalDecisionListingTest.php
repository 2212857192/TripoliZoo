<?php

namespace Tests\Feature;

use App\Enums\HospitalCaseStatus;
use App\Enums\ReceivingTaskSource;
use App\Enums\ReceivingTaskStatus;
use App\Enums\ReceivingTaskType;
use App\Enums\TreatmentReferralStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\HealthCase;
use App\Models\HospitalCase;
use App\Models\ReceivingTask;
use App\Models\TreatmentReferral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalDecisionListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_director_vet_decisions_include_discharge_and_slaughter(): void
    {
        $director = User::factory()->create([
            'role' => UserRole::Director->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'MD-001',
            'name' => 'سامي',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => 'active',
            'registered_at' => now(),
        ]);

        ReceivingTask::create([
            'task_number' => 'RCV-2026-900',
            'animal_id' => $animal->id,
            'supervisor_id' => User::factory()->create([
                'role' => UserRole::GroupSupervisor->value,
                'assigned_group' => $animal->group,
            ])->id,
            'status' => ReceivingTaskStatus::Pending,
            'task_type' => ReceivingTaskType::AfterTreatment,
            'source' => ReceivingTaskSource::Hospital,
            'decision_date' => now()->toDateString(),
            'decision_issued_by' => User::factory()->create([
                'role' => UserRole::VetHead->value,
            ])->id,
            'decision_notes' => 'خروج بعد العلاج.',
        ]);

        $hospitalCase = $this->seedSlaughteredHospitalCase($animal);

        $response = $this->actingAs($director)->get('/director/vet/decisions');

        $response->assertOk()
            ->assertSee('خروج بعد العلاج')
            ->assertSee('ذبح اضطراري')
            ->assertSee('RCV-2026-900')
            ->assertSee($hospitalCase->case_number);
    }

    private function seedSlaughteredHospitalCase(Animal $animal): HospitalCase
    {
        $healthCase = HealthCase::create([
            'case_number' => 'HC-2026-900',
            'animal_id' => $animal->id,
            'supervisor_id' => User::factory()->create([
                'role' => UserRole::GroupSupervisor->value,
                'assigned_group' => $animal->group,
            ])->id,
            'group' => $animal->group,
            'description' => 'حالة طبية',
            'follow_up_kind' => 'needs_referral',
            'status' => 'referred',
        ]);

        $referral = TreatmentReferral::create([
            'referral_number' => 'TR-2026-900',
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'status' => TreatmentReferralStatus::Approved,
            'referred_by' => User::factory()->create(['role' => UserRole::CareHead->value])->id,
            'referred_at' => now(),
        ]);

        return HospitalCase::create([
            'case_number' => 'VH-2026-900',
            'treatment_referral_id' => $referral->id,
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'chief_complaint' => 'لا يستجيب للعلاج',
            'status' => HospitalCaseStatus::Slaughtered,
            'admitted_by' => User::factory()->create([
                'role' => UserRole::Veterinarian->value,
                'assigned_group' => $animal->group,
            ])->id,
            'admitted_at' => now()->subDays(2),
            'closed_at' => now(),
            'closing_outcome' => 'ذبح اضطراري',
        ]);
    }

    public function test_care_head_can_open_slaughter_decision_details(): void
    {
        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'MD-002',
            'name' => 'نور',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => 'active',
            'registered_at' => now(),
        ]);

        $hospitalCase = $this->seedSlaughteredHospitalCase($animal);

        $this->actingAs($careHead)
            ->get('/care/decisions/slaughter/'.$hospitalCase->case_number)
            ->assertOk()
            ->assertSee('ذبح اضطراري')
            ->assertSee($hospitalCase->case_number)
            ->assertSee('لا يتطلب استلام');
    }

    public function test_care_decisions_list_links_slaughter_to_care_portal(): void
    {
        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'MD-003',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => 'active',
            'registered_at' => now(),
        ]);

        $hospitalCase = $this->seedSlaughteredHospitalCase($animal);

        $this->actingAs($careHead)
            ->get('/care/decisions')
            ->assertOk()
            ->assertSee('/care/decisions/slaughter/'.$hospitalCase->case_number, false);
    }
}
