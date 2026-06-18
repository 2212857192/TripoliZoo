<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\MedicalCaseResult;
use App\Enums\TreatmentReferralStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\HealthCase;
use App\Models\HospitalCase;
use App\Models\MedicalCaseProcedure;
use App\Models\TreatmentReferral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordsSlaughterLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_officer_sees_slaughter_log(): void
    {
        [$animal, $case, $vet, $headVet] = $this->seedSlaughteredCase(
            closingOutcome: 'كسر مفتوح غير قابل للعلاج',
            closedAt: '2025-08-14',
        );

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/slaughter')
            ->assertOk()
            ->assertSee($animal->code, false)
            ->assertSee('كسر مفتوح غير قابل للعلاج', false)
            ->assertSee('2025-08-14', false)
            ->assertSee($vet->name, false)
            ->assertSee($headVet->name, false)
            ->assertSee('سجل الذبح الاضطراري', false);
    }

    public function test_slaughter_log_filters_by_group(): void
    {
        [$animalA] = $this->seedSlaughteredCase(group: 'الثدييات الكبيرة');
        [$animalB] = $this->seedSlaughteredCase(group: 'الغزلان');

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/slaughter?group='.urlencode('الثدييات الكبيرة'))
            ->assertOk()
            ->assertSee($animalA->code, false)
            ->assertDontSee($animalB->code, false);
    }

    public function test_slaughter_log_excludes_non_slaughtered_hospital_cases(): void
    {
        [$slaughterAnimal] = $this->seedSlaughteredCase();
        [$activeAnimal] = $this->seedHospitalCaseWithStatus(HospitalCaseStatus::UnderTreatment);

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/slaughter')
            ->assertOk()
            ->assertSee($slaughterAnimal->code, false)
            ->assertDontSee($activeAnimal->code, false);
    }

    /**
     * @return array{0: Animal, 1: HospitalCase, 2: User, 3: User}
     */
    private function seedSlaughteredCase(
        string $group = 'الثدييات الكبيرة',
        string $closingOutcome = 'ذبح اضطراري',
        string $closedAt = '2025-08-14',
    ): array {
        static $counter = 0;
        $counter++;

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => $group,
            'status' => 'active',
            'name' => 'د. أحمد الفيتوري '.$counter,
        ]);

        $headVet = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
            'name' => 'د. سالم الزاوي '.$counter,
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'S'.$counter.'00',
            'name' => 'فحل',
            'species' => 'حصان عربي',
            'group' => $group,
            'gender' => 'ذكر',
            'status' => AnimalStatus::Dead->value,
            'registered_at' => now()->subYears(3),
        ]);

        [$healthCase, $referral] = $this->seedReferralChain($animal);

        $case = HospitalCase::create([
            'case_number' => 'VH-SL-'.$counter,
            'treatment_referral_id' => $referral->id,
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => $group,
            'chief_complaint' => 'إصابة خطيرة',
            'status' => HospitalCaseStatus::Slaughtered,
            'admitted_by' => $vet->id,
            'admitted_at' => '2025-08-01',
            'closed_at' => $closedAt,
            'closing_outcome' => $closingOutcome,
        ]);

        MedicalCaseProcedure::create([
            'caseable_type' => HospitalCase::class,
            'caseable_id' => $case->id,
            'recorded_by' => $headVet->id,
            'diagnosis' => 'لا يستجيب للعلاج',
            'treatment' => '—',
            'case_result' => MedicalCaseResult::NoResponse,
            'recorded_at' => '2025-08-10',
        ]);

        return [$animal, $case, $vet, $headVet];
    }

    /** @return array{0: Animal, 1: HospitalCase} */
    private function seedHospitalCaseWithStatus(HospitalCaseStatus $status): array
    {
        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'ACTIVE01',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        [$healthCase, $referral] = $this->seedReferralChain($animal);

        $case = HospitalCase::create([
            'case_number' => 'VH-ACTIVE-01',
            'treatment_referral_id' => $referral->id,
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'chief_complaint' => 'التهاب',
            'status' => $status,
            'admitted_by' => $vet->id,
            'admitted_at' => now(),
        ]);

        return [$animal, $case];
    }

    /** @return array{0: HealthCase, 1: TreatmentReferral} */
    private function seedReferralChain(Animal $animal): array
    {
        static $referralCounter = 0;
        $referralCounter++;

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => $animal->group,
            'status' => 'active',
        ]);

        $healthCase = HealthCase::create([
            'case_number' => 'HC-SL-'.$referralCounter,
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => $animal->group,
            'description' => 'حالة تحتاج علاج',
            'follow_up_kind' => 'needs_referral',
            'status' => 'referred',
        ]);

        $referral = TreatmentReferral::create([
            'referral_number' => 'TR-SL-'.$referralCounter,
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'status' => TreatmentReferralStatus::Approved,
            'referred_by' => User::factory()->create(['role' => UserRole::CareHead->value])->id,
            'referred_at' => now(),
        ]);

        return [$healthCase, $referral];
    }
}
