<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\MortalityCaseStatus;
use App\Enums\MortalityVictimKind;
use App\Enums\QuarantineStatus;
use App\Enums\TreatmentReferralStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\AnimalExit;
use App\Models\BirthRegistration;
use App\Models\HealthCase;
use App\Models\HospitalCase;
use App\Models\MortalityCase;
use App\Models\Quarantine;
use App\Models\TreatmentReferral;
use App\Models\User;
use App\Services\RecordsDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_officer_sees_dynamic_dashboard_stats(): void
    {
        $this->seedDashboardData();

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/dashboard')
            ->assertOk()
            ->assertSee('إجمالي الحيوانات', false)
            ->assertSee('سجل الولادات', false)
            ->assertSee('REG001', false)
            ->assertSee('إضافة حيوان', false)
            ->assertSee('نفوق', false);
    }

    public function test_dashboard_service_returns_expected_counts(): void
    {
        $this->seedDashboardData();

        $stats = app(RecordsDashboardService::class)->stats();

        $this->assertSame(3, $stats['active_animals']);
        $this->assertSame(6, $stats['total_profiles']);
        $this->assertSame(1, $stats['births']);
        $this->assertSame(1, $stats['entries']);
        $this->assertSame(1, $stats['mortality']);
        $this->assertSame(1, $stats['slaughter']);
        $this->assertSame(1, $stats['exits']);
    }

    private function seedDashboardData(): void
    {
        Animal::withoutGlobalScopes()->create([
            'code' => 'REG001',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'source' => 'records',
            'status' => AnimalStatus::Active->value,
            'registered_at' => '2026-06-07',
        ]);

        $exitedAnimal = Animal::withoutGlobalScopes()->create([
            'code' => 'REG002',
            'species' => 'أسد',
            'group' => 'القططية',
            'gender' => 'أنثى',
            'source' => 'records',
            'status' => AnimalStatus::Exited->value,
            'registered_at' => '2025-01-01',
        ]);

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'القرود',
            'status' => 'active',
        ]);

        $registration = BirthRegistration::create([
            'registration_number' => 'BR-001',
            'mother_id' => Animal::withoutGlobalScopes()->create([
                'code' => 'M100',
                'species' => 'قرد مكاك',
                'group' => 'القرود',
                'gender' => 'أنثى',
                'status' => AnimalStatus::Active->value,
                'registered_at' => now()->subYear(),
            ])->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'القرود',
            'birth_date' => '2026-06-05',
            'birth_count' => 1,
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'NB001',
            'species' => 'قرد مكاك',
            'group' => 'القرود',
            'gender' => 'ذكر',
            'status' => AnimalStatus::UnderBirthFollowUp->value,
            'birth_date' => '2026-06-05',
            'registered_at' => '2026-06-05',
            'birth_registration_id' => $registration->id,
        ]);

        $deadAnimal = Animal::withoutGlobalScopes()->create([
            'code' => 'DEAD01',
            'species' => 'نعامة',
            'group' => 'الطيور',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Dead->value,
            'registered_at' => '2024-01-01',
        ]);

        MortalityCase::create([
            'case_number' => 'MC-001',
            'animal_id' => $deadAnimal->id,
            'subject_code' => $deadAnimal->code,
            'subject_type' => $deadAnimal->species,
            'supervisor_id' => User::factory()->create([
                'role' => UserRole::GroupSupervisor->value,
                'assigned_group' => 'الطيور',
            ])->id,
            'group' => 'الطيور',
            'victim_kind' => MortalityVictimKind::ZooAnimal,
            'death_cause' => 'مرض',
            'death_date' => '2026-06-06',
            'status' => MortalityCaseStatus::Approved,
        ]);

        Quarantine::create([
            'case_number' => 'QR-001',
            'animal_id' => Animal::withoutGlobalScopes()->create([
                'code' => 'Q001',
                'species' => 'زرافة',
                'group' => 'الثدييات الكبيرة',
                'gender' => 'أنثى',
                'source' => 'quarantine',
                'status' => AnimalStatus::Quarantine->value,
                'registered_at' => '2026-01-01',
            ])->id,
            'reason' => '',
            'initial_health_status' => 'جيد',
            'status' => QuarantineStatus::HealthReleased->value,
            'entry_date' => '2026-01-01',
            'released_at' => '2026-02-01',
            'created_by' => User::factory()->create(['role' => UserRole::VetHead->value])->id,
        ]);

        AnimalExit::create([
            'animal_id' => $exitedAnimal->id,
            'recorded_by' => User::factory()->create(['role' => UserRole::RecordsOfficer->value])->id,
            'exit_date' => '2026-02-01',
            'exit_type' => 'transfer',
            'recipient' => 'حديقة أخرى',
            'reason' => 'نقل',
        ]);

        $healthCase = HealthCase::create([
            'case_number' => 'HC-001',
            'animal_id' => $deadAnimal->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'الطيور',
            'description' => 'حالة',
            'follow_up_kind' => 'needs_referral',
            'status' => 'referred',
        ]);

        $referral = TreatmentReferral::create([
            'referral_number' => 'TR-001',
            'health_case_id' => $healthCase->id,
            'animal_id' => $deadAnimal->id,
            'group' => 'الطيور',
            'status' => TreatmentReferralStatus::Approved,
            'referred_by' => User::factory()->create(['role' => UserRole::CareHead->value])->id,
            'referred_at' => now(),
        ]);

        HospitalCase::create([
            'case_number' => 'VH-SL-1',
            'treatment_referral_id' => $referral->id,
            'health_case_id' => $healthCase->id,
            'animal_id' => $deadAnimal->id,
            'group' => 'الطيور',
            'chief_complaint' => 'إصابة',
            'status' => HospitalCaseStatus::Slaughtered,
            'admitted_by' => User::factory()->create([
                'role' => UserRole::Veterinarian->value,
                'assigned_group' => 'الطيور',
            ])->id,
            'admitted_at' => now(),
            'closed_at' => '2026-03-01',
            'closing_outcome' => 'ذبح اضطراري',
        ]);
    }
}
