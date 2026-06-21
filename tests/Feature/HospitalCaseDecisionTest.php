<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\FieldCaseStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\ReceivingTaskSource;
use App\Enums\ReceivingTaskStatus;
use App\Enums\TreatmentReferralStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\FieldCase;
use App\Models\HealthCase;
use App\Models\HospitalCase;
use App\Models\ReceivingTask;
use App\Models\TreatmentReferral;
use App\Models\User;
use App\Services\ReceivingTaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HospitalCaseDecisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_vet_head_can_choose_slaughter_when_doctor_requested_discharge(): void
    {
        [$hospitalCase, $vetHead] = $this->seedPendingDischargeCase();

        $response = $this->actingAs($vetHead)->post(
            route('vet.cases.hospital.issue-decision', $hospitalCase),
            ['decision' => 'slaughter', 'note' => 'قرار رئيس القسم']
        );

        $response->assertRedirect(route('vet.cases.hospital.show', $hospitalCase));

        $this->assertDatabaseHas('hospital_cases', [
            'id' => $hospitalCase->id,
            'status' => HospitalCaseStatus::Slaughtered->value,
        ]);
    }

    public function test_vet_head_can_choose_discharge_when_doctor_requested_slaughter(): void
    {
        [$hospitalCase, $vetHead] = $this->seedPendingSlaughterCase();

        $response = $this->actingAs($vetHead)->post(
            route('vet.cases.hospital.issue-decision', $hospitalCase),
            ['decision' => 'discharge']
        );

        $response->assertRedirect(route('vet.cases.hospital.show', $hospitalCase));

        $this->assertDatabaseHas('hospital_cases', [
            'id' => $hospitalCase->id,
            'status' => HospitalCaseStatus::PendingHandover->value,
        ]);
    }

    public function test_supervisor_receipt_closes_hospital_case_as_discharged(): void
    {
        [$hospitalCase, $vetHead] = $this->seedPendingDischargeCase();

        $this->actingAs($vetHead)->post(
            route('vet.cases.hospital.issue-decision', $hospitalCase),
            ['decision' => 'discharge']
        )->assertRedirect();

        $supervisor = User::query()
            ->where('role', UserRole::GroupSupervisor->value)
            ->where('assigned_group', $hospitalCase->group)
            ->firstOrFail();

        $task = ReceivingTask::query()
            ->where('animal_id', $hospitalCase->animal_id)
            ->where('source', ReceivingTaskSource::Hospital)
            ->firstOrFail();

        app(ReceivingTaskService::class)->confirmReceipt($task, $supervisor, 'تم الاستلام في المجموعة');

        $this->assertDatabaseHas('hospital_cases', [
            'id' => $hospitalCase->id,
            'status' => HospitalCaseStatus::Discharged->value,
            'closing_outcome' => 'تم الاستلام في المجموعة',
        ]);

        $this->assertDatabaseHas('receiving_tasks', [
            'animal_id' => $hospitalCase->animal_id,
            'hospital_case_id' => $hospitalCase->id,
            'status' => ReceivingTaskStatus::Received->value,
        ]);

        $this->assertDatabaseHas('animals', [
            'id' => $hospitalCase->animal_id,
            'status' => AnimalStatus::Active->value,
        ]);
    }

    public function test_discharge_decision_moves_case_to_pending_handover_tab(): void
    {
        [$hospitalCase, $vetHead] = $this->seedPendingDischargeCase();

        $this->actingAs($vetHead)->post(
            route('vet.cases.hospital.issue-decision', $hospitalCase),
            ['decision' => 'discharge']
        )->assertRedirect();

        $this->actingAs($vetHead)->get(route('vet.cases.hospital.index'))
            ->assertOk()
            ->assertSee($hospitalCase->case_number)
            ->assertSee('بانتظار الاستلام');
    }

    public function test_decided_hospital_cases_are_hidden_from_doctor_list(): void
    {
        [$hospitalCase, $vetHead] = $this->seedPendingDischargeCase();

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => $hospitalCase->group,
            'status' => 'active',
        ]);

        Sanctum::actingAs($vet);

        $this->getJson('/api/auth/doctor/cases')
            ->assertOk()
            ->assertJsonFragment(['id' => 'hospital-'.$hospitalCase->case_number]);

        $this->actingAs($vetHead)->post(
            route('vet.cases.hospital.issue-decision', $hospitalCase),
            ['decision' => 'discharge']
        )->assertRedirect();

        Sanctum::actingAs($vet);

        $this->getJson('/api/auth/doctor/cases')
            ->assertOk()
            ->assertJsonMissing(['id' => 'hospital-'.$hospitalCase->case_number]);

        $this->getJson('/api/auth/doctor/cases/hospital-'.$hospitalCase->case_number)
            ->assertNotFound();
    }

    public function test_slaughter_decision_hides_hospital_case_from_doctor_list(): void
    {
        [$hospitalCase, $vetHead] = $this->seedPendingSlaughterCase();

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => $hospitalCase->group,
            'status' => 'active',
        ]);

        $this->actingAs($vetHead)->post(
            route('vet.cases.hospital.issue-decision', $hospitalCase),
            ['decision' => 'slaughter']
        )->assertRedirect();

        Sanctum::actingAs($vet);

        $this->getJson('/api/auth/doctor/cases')
            ->assertOk()
            ->assertJsonMissing(['id' => 'hospital-'.$hospitalCase->case_number]);
    }

    public function test_slaughter_decision_closes_open_field_case_and_marks_animal_dead(): void
    {
        [$hospitalCase, $vetHead] = $this->seedPendingSlaughterCase();

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => $hospitalCase->group,
            'status' => 'active',
        ]);

        $fieldCase = FieldCase::create([
            'case_number' => 'FC-2026-201',
            'animal_id' => $hospitalCase->animal_id,
            'group' => $hospitalCase->group,
            'open_reason' => 'متابعة ميدانية',
            'status' => FieldCaseStatus::Active,
            'opened_by' => $vet->id,
            'opened_at' => now(),
        ]);

        $this->actingAs($vetHead)->post(
            route('vet.cases.hospital.issue-decision', $hospitalCase),
            ['decision' => 'slaughter']
        )->assertRedirect();

        $this->assertDatabaseHas('field_cases', [
            'id' => $fieldCase->id,
            'status' => FieldCaseStatus::Closed->value,
            'closing_note' => 'أُغلقت تلقائياً بسبب صدور قرار ذبح اضطراري.',
        ]);

        $this->assertDatabaseHas('hospital_cases', [
            'id' => $hospitalCase->id,
            'status' => HospitalCaseStatus::Slaughtered->value,
            'closing_outcome' => 'أُغلقت تلقائياً بسبب صدور قرار ذبح اضطراري.',
        ]);

        $this->assertDatabaseHas('animals', [
            'id' => $hospitalCase->animal_id,
            'status' => AnimalStatus::Dead->value,
        ]);
    }

    /** @return array{0: HospitalCase, 1: User} */
    private function seedPendingDischargeCase(): array
    {
        return $this->seedHospitalCase(HospitalCaseStatus::PendingDischargeApproval);
    }

    /** @return array{0: HospitalCase, 1: User} */
    private function seedPendingSlaughterCase(): array
    {
        return $this->seedHospitalCase(HospitalCaseStatus::PendingSlaughterApproval);
    }

    /** @return array{0: HospitalCase, 1: User} */
    private function seedHospitalCase(HospitalCaseStatus $status): array
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

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G050',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => 'active',
            'registered_at' => now(),
        ]);

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => $animal->group,
        ]);

        $healthCase = HealthCase::create([
            'case_number' => 'HC-2026-200',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => $animal->group,
            'description' => 'حالة تحتاج علاج',
            'follow_up_kind' => 'needs_referral',
            'status' => 'referred',
        ]);

        $referral = TreatmentReferral::create([
            'referral_number' => 'TR-2026-200',
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'status' => TreatmentReferralStatus::Approved,
            'referred_by' => User::factory()->create(['role' => UserRole::CareHead->value])->id,
            'referred_at' => now(),
        ]);

        $hospitalCase = HospitalCase::create([
            'case_number' => 'VH-2026-200',
            'treatment_referral_id' => $referral->id,
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'chief_complaint' => 'التهاب',
            'status' => $status,
            'admitted_by' => $vet->id,
            'admitted_at' => now(),
        ]);

        return [$hospitalCase, $vetHead];
    }
}
