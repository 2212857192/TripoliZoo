<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\MortalityCaseStatus;
use App\Enums\MortalityVictimKind;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\MortalityCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecordsStillbirthLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_officer_sees_stillbirth_log_from_mortality_cases(): void
    {
        [$supervisor, $newborn, $mother] = $this->seedStillbornCase();

        Sanctum::actingAs($supervisor);
        $this->postJson('/api/auth/supervisor/mortality-cases', [
            'animal_code' => $newborn->code,
            'victim_kind' => MortalityVictimKind::NewbornUnderFollowUp->value,
            'death_cause' => 'ضعف ولادة',
            'death_date' => '2026-06-10',
        ])->assertCreated();

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/stillbirths')
            ->assertOk()
            ->assertSee($newborn->code, false)
            ->assertSee($mother->code, false)
            ->assertSee('ضعف ولادة', false)
            ->assertSee('سجل الولادات النافقة', false);
    }

    public function test_stillbirth_log_filters_by_autopsy_status(): void
    {
        [$supervisor, $newborn] = $this->seedStillbornCase();

        Sanctum::actingAs($supervisor);
        $this->postJson('/api/auth/supervisor/mortality-cases', [
            'animal_code' => $newborn->code,
            'victim_kind' => MortalityVictimKind::NewbornUnderFollowUp->value,
            'death_cause' => 'تشوهات',
        ])->assertCreated();

        $case = MortalityCase::query()->firstOrFail();
        $case->update(['status' => MortalityCaseStatus::ReferredForAutopsy]);

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/stillbirths?autopsy=yes')
            ->assertOk()
            ->assertSee($newborn->code, false);

        $this->actingAs($officer)
            ->get('/records/logs/stillbirths?autopsy=no')
            ->assertOk()
            ->assertDontSee($newborn->code, false);
    }

    /** @return array{0: User, 1: Animal, 2: Animal} */
    private function seedStillbornCase(): array
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'القططية',
            'status' => 'active',
        ]);

        $mother = Animal::withoutGlobalScopes()->create([
            'code' => 'L100',
            'species' => 'أسد أفريقي',
            'group' => 'القططية',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now()->subYear(),
        ]);

        $newborn = Animal::withoutGlobalScopes()->create([
            'code' => 'NB001',
            'species' => 'أسد أفريقي',
            'group' => 'القططية',
            'gender' => 'أنثى',
            'mother_id' => $mother->id,
            'birth_date' => '2026-05-20',
            'status' => AnimalStatus::UnderBirthFollowUp->value,
            'registered_at' => '2026-05-20',
        ]);

        return [$supervisor, $newborn, $mother];
    }
}
