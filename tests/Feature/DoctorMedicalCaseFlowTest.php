<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\HealthReportStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\TreatmentReferralStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\FieldCase;
use App\Models\HealthCase;
use App\Models\HealthReport;
use App\Models\HospitalCase;
use App\Models\TreatmentReferral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DoctorMedicalCaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_register_procedure_on_field_case(): void
    {
        [$vet, $animal] = $this->seedVetWithAnimal();

        Sanctum::actingAs($vet);

        $this->postJson('/api/auth/doctor/field-cases', [
            'animal_code' => $animal->code,
            'open_reason' => 'عرج خفيف',
        ])->assertCreated();

        $fieldCase = FieldCase::query()->firstOrFail();
        $caseKey = 'field-'.$fieldCase->case_number;

        $this->postJson("/api/auth/doctor/cases/{$caseKey}/procedures", [
            'diagnosis' => 'التواء بسيط',
            'treatment' => 'راحة ومضاد التهاب',
            'case_result' => 'continue_treatment',
            'nutrition' => [
                'recommendation_text' => 'علف خاص',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(5)->toDateString(),
            ],
        ])->assertCreated()
            ->assertJsonPath('data.procedures.0.diagnosis', 'التواء بسيط');

        $this->assertDatabaseHas('medical_case_procedures', [
            'caseable_type' => FieldCase::class,
            'caseable_id' => $fieldCase->id,
            'diagnosis' => 'التواء بسيط',
        ]);
    }

    public function test_doctor_can_close_field_case(): void
    {
        [$vet, $animal] = $this->seedVetWithAnimal();
        Sanctum::actingAs($vet);

        $this->postJson('/api/auth/doctor/field-cases', [
            'animal_code' => $animal->code,
            'open_reason' => 'عرج خفيف',
        ]);

        $fieldCase = FieldCase::query()->firstOrFail();
        $caseKey = 'field-'.$fieldCase->case_number;

        $this->postJson("/api/auth/doctor/cases/{$caseKey}/close")
            ->assertOk()
            ->assertJsonPath('data.status', 'closed');

        $this->assertDatabaseHas('field_cases', [
            'id' => $fieldCase->id,
            'status' => 'closed',
        ]);
    }

    public function test_hospital_procedure_ready_for_discharge_notifies_vet_head(): void
    {
        [$vet, $animal] = $this->seedVetWithAnimal();
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $hospitalCase = HospitalCase::create([
            'case_number' => 'VH-2026-002',
            'treatment_referral_id' => $this->seedTreatmentReferral($animal)->id,
            'health_case_id' => HealthCase::query()->first()->id,
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'chief_complaint' => 'التهاب',
            'status' => HospitalCaseStatus::UnderTreatment,
            'admitted_by' => $vetHead->id,
            'admitted_at' => now(),
        ]);

        Sanctum::actingAs($vet);

        $this->postJson('/api/auth/doctor/cases/hospital-'.$hospitalCase->case_number.'/procedures', [
            'diagnosis' => 'تحسن',
            'treatment' => 'إيقاف المضاد',
            'case_result' => 'ready_for_discharge',
        ])->assertCreated();

        $this->assertDatabaseHas('hospital_cases', [
            'id' => $hospitalCase->id,
            'status' => HospitalCaseStatus::PendingDischargeApproval->value,
        ]);

        $this->assertDatabaseHas('hospital_case_notifications', [
            'user_id' => $vetHead->id,
            'hospital_case_id' => $hospitalCase->id,
        ]);

        $this->getJson('/api/auth/doctor/cases/hospital-'.$hospitalCase->case_number)
            ->assertOk()
            ->assertJsonPath('data.can_register_procedure', true);

        $this->postJson('/api/auth/doctor/cases/hospital-'.$hospitalCase->case_number.'/procedures', [
            'diagnosis' => 'متابعة يومية',
            'treatment' => 'مراقبة الشهية',
            'case_result' => 'continue_treatment',
        ])->assertCreated();
    }

    public function test_doctor_can_list_group_animals(): void
    {
        [$vet, $animal] = $this->seedVetWithAnimal();

        Animal::withoutGlobalScopes()->create([
            'code' => 'G011',
            'species' => 'غزال',
            'group' => 'القططية',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        Sanctum::actingAs($vet);

        $this->getJson('/api/auth/doctor/animals')
            ->assertOk()
            ->assertJsonFragment(['id' => $animal->code])
            ->assertJsonMissing(['id' => 'G011']);
    }

    public function test_doctor_animals_list_excludes_quarantine_and_pending_receipt(): void
    {
        [$vet, $animal] = $this->seedVetWithAnimal();

        Animal::withoutGlobalScopes()->create([
            'code' => 'G011',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => now(),
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'G012',
            'species' => 'جمل',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::PendingReceipt->value,
            'registered_at' => now(),
        ]);

        Sanctum::actingAs($vet);

        $this->getJson('/api/auth/doctor/animals')
            ->assertOk()
            ->assertJsonFragment(['id' => $animal->code])
            ->assertJsonMissing(['id' => 'G011'])
            ->assertJsonMissing(['id' => 'G012']);
    }

    public function test_doctor_animals_list_excludes_animals_in_hospital(): void
    {
        [$vet, $animal] = $this->seedVetWithAnimal();

        $hospitalAnimal = Animal::withoutGlobalScopes()->create([
            'code' => 'G013',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        HospitalCase::create([
            'case_number' => 'VH-2026-099',
            'treatment_referral_id' => $this->seedTreatmentReferral($hospitalAnimal)->id,
            'health_case_id' => HealthCase::query()->where('animal_id', $hospitalAnimal->id)->value('id'),
            'animal_id' => $hospitalAnimal->id,
            'group' => $hospitalAnimal->group,
            'chief_complaint' => 'التهاب حاد',
            'status' => HospitalCaseStatus::UnderTreatment,
            'admitted_by' => $vet->id,
            'admitted_at' => now(),
        ]);

        Sanctum::actingAs($vet);

        $this->getJson('/api/auth/doctor/animals')
            ->assertOk()
            ->assertJsonFragment(['id' => $animal->code])
            ->assertJsonMissing(['id' => $hospitalAnimal->code]);

        $this->postJson('/api/auth/doctor/field-cases', [
            'animal_code' => $hospitalAnimal->code,
            'open_reason' => 'محاولة فتح حالة ميدانية',
        ])->assertStatus(422);
    }

    public function test_doctor_can_open_field_case_and_list_it(): void
    {
        [$vet, $animal] = $this->seedVetWithAnimal();

        Sanctum::actingAs($vet);

        $response = $this->postJson('/api/auth/doctor/field-cases', [
            'animal_code' => $animal->code,
            'open_reason' => 'عرج خفيف في الساق',
            'initial_note' => 'يحتاج متابعة يومية',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.case_type', 'field')
            ->assertJsonPath('data.status', 'active');

        $fieldCase = FieldCase::query()->first();
        $this->assertNotNull($fieldCase);

        $this->getJson('/api/auth/doctor/cases')
            ->assertOk()
            ->assertJsonPath('data.0.id', 'field-'.$fieldCase->case_number);

        $this->getJson('/api/auth/doctor/cases/field-'.$fieldCase->case_number)
            ->assertOk()
            ->assertJsonPath('data.open_reason', 'عرج خفيف في الساق');
    }

    public function test_doctor_sees_hospital_cases_for_their_group(): void
    {
        [$vet, $animal] = $this->seedVetWithAnimal();

        $hospitalCase = HospitalCase::create([
            'case_number' => 'VH-2026-001',
            'treatment_referral_id' => $this->seedTreatmentReferral($animal)->id,
            'health_case_id' => HealthCase::query()->first()->id,
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'chief_complaint' => 'التهاب رئوي',
            'status' => HospitalCaseStatus::UnderTreatment,
            'admitted_by' => User::factory()->create(['role' => UserRole::VetHead->value])->id,
            'admitted_at' => now(),
        ]);

        Sanctum::actingAs($vet);

        $this->getJson('/api/auth/doctor/cases')
            ->assertOk()
            ->assertJsonPath('data.0.id', 'hospital-'.$hospitalCase->case_number)
            ->assertJsonPath('data.0.case_type', 'hospital');
    }

    public function test_closing_health_report_with_field_case_opened_creates_field_case(): void
    {
        [$vet, $animal] = $this->seedVetWithAnimal();

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => $animal->group,
            'status' => 'active',
        ]);

        $report = HealthReport::create([
            'report_number' => 'HR-2026-001',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => $animal->group,
            'description' => 'خمول واضح',
            'status' => HealthReportStatus::Sent,
        ]);

        Sanctum::actingAs($vet);

        $this->postJson("/api/auth/doctor/health-reports/{$report->report_number}/close", [
            'doctor_note' => 'تم الفحص وبدء المتابعة الميدانية',
            'field_case_opened' => true,
        ])->assertOk();

        $this->assertDatabaseHas('field_cases', [
            'animal_id' => $animal->id,
            'health_report_id' => $report->id,
            'open_reason' => 'خمول واضح',
        ]);
    }

    /** @return array{0: User, 1: Animal} */
    private function seedVetWithAnimal(): array
    {
        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G010',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        return [$vet, $animal];
    }

    private function seedTreatmentReferral(Animal $animal): TreatmentReferral
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => $animal->group,
        ]);

        $healthCase = HealthCase::create([
            'case_number' => 'HC-2026-099',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => $animal->group,
            'description' => 'حالة تحتاج علاج',
            'follow_up_kind' => 'needs_referral',
            'status' => 'referred',
        ]);

        return TreatmentReferral::create([
            'referral_number' => 'TR-2026-099',
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'status' => TreatmentReferralStatus::Approved,
            'referred_by' => User::factory()->create(['role' => UserRole::CareHead->value])->id,
            'referred_at' => now(),
        ]);
    }
}
