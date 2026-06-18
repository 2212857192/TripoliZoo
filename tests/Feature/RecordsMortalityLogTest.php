<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\AutopsyReferralStatus;
use App\Enums\MortalityCaseStatus;
use App\Enums\MortalityVictimKind;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\AutopsyReferral;
use App\Models\MortalityCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordsMortalityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_officer_sees_mortality_log_for_zoo_animals(): void
    {
        [$animal, $case] = $this->seedZooMortalityCase('شيخوخة', MortalityCaseStatus::Approved, reviewedAt: '2024-11-11');

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/mortality')
            ->assertOk()
            ->assertSee($animal->code, false)
            ->assertSee('شيخوخة — اعتماد مباشر', false)
            ->assertSee('2024-11-11', false)
            ->assertSee('سجل النفوق', false);
    }

    public function test_mortality_log_excludes_stillbirth_cases(): void
    {
        [$zooAnimal] = $this->seedZooMortalityCase('إصابة', MortalityCaseStatus::New);
        [$stillbornAnimal] = $this->seedStillbirthCase();

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/mortality')
            ->assertOk()
            ->assertSee($zooAnimal->code, false)
            ->assertDontSee($stillbornAnimal->code, false);
    }

    public function test_mortality_log_filters_by_autopsy_status(): void
    {
        [$approvedAnimal] = $this->seedZooMortalityCase('مرض', MortalityCaseStatus::Approved);
        [$autopsyAnimal, $autopsyCase] = $this->seedZooMortalityCase('غير ظاهر', MortalityCaseStatus::ReferredForAutopsy);

        AutopsyReferral::create([
            'referral_number' => 'AR-2026-001',
            'mortality_case_id' => $autopsyCase->id,
            'animal_id' => $autopsyAnimal->id,
            'group' => $autopsyAnimal->group,
            'status' => AutopsyReferralStatus::Pending,
            'referred_by' => User::factory()->create(['role' => UserRole::CareHead->value])->id,
            'referred_at' => now(),
            'transfer_reason' => 'سبب غير ظاهر',
        ]);

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/mortality?autopsy=yes')
            ->assertOk()
            ->assertSee($autopsyAnimal->code, false)
            ->assertDontSee($approvedAnimal->code, false);
    }

    public function test_mortality_log_shows_documented_autopsy_cause(): void
    {
        [$animal, $case] = $this->seedZooMortalityCase(null, MortalityCaseStatus::ReferredForAutopsy);

        AutopsyReferral::create([
            'referral_number' => 'AR-2026-002',
            'mortality_case_id' => $case->id,
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'status' => AutopsyReferralStatus::Documented,
            'referred_by' => User::factory()->create(['role' => UserRole::CareHead->value])->id,
            'referred_at' => now()->subDays(5),
            'transfer_reason' => 'سبب غير ظاهر',
            'documented_by' => User::factory()->create(['role' => UserRole::VetHead->value])->id,
            'documented_at' => '2025-12-22',
            'final_death_cause' => 'فشل تنفسي حاد',
        ]);

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/mortality')
            ->assertOk()
            ->assertSee('فشل تنفسي حاد — نتيجة تشريح', false)
            ->assertSee('2025-12-22', false);
    }

    /** @return array{0: Animal, 1: MortalityCase} */
    private function seedZooMortalityCase(
        ?string $deathCause,
        MortalityCaseStatus $status,
        ?string $reviewedAt = null,
    ): array {
        static $counter = 0;
        $counter++;

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'القططية',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'M'.$counter.'00',
            'name' => 'أسد',
            'species' => 'أسد أفريقي',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Dead->value,
            'registered_at' => now()->subYears(5),
        ]);

        $case = MortalityCase::create([
            'case_number' => 'MC-2026-'.$counter,
            'animal_id' => $animal->id,
            'subject_code' => $animal->code,
            'subject_type' => $animal->species,
            'supervisor_id' => $supervisor->id,
            'group' => $animal->group,
            'victim_kind' => MortalityVictimKind::ZooAnimal,
            'death_cause' => $deathCause,
            'death_date' => '2024-11-10',
            'status' => $status,
            'reviewed_at' => $reviewedAt,
        ]);

        return [$animal, $case];
    }

    /** @return array{0: Animal, 1: MortalityCase} */
    private function seedStillbirthCase(): array
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'القططية',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'NB100',
            'species' => 'أسد أفريقي',
            'group' => 'القططية',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Dead->value,
            'registered_at' => '2026-05-20',
        ]);

        $case = MortalityCase::create([
            'case_number' => 'MC-STILL-001',
            'animal_id' => $animal->id,
            'subject_code' => $animal->code,
            'subject_type' => $animal->species,
            'supervisor_id' => $supervisor->id,
            'group' => $animal->group,
            'victim_kind' => MortalityVictimKind::NewbornUnderFollowUp,
            'death_cause' => 'ضعف ولادة',
            'death_date' => '2026-06-10',
            'status' => MortalityCaseStatus::New,
        ]);

        return [$animal, $case];
    }
}
