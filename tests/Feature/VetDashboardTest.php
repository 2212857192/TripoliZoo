<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\HealthCaseFollowUpKind;
use App\Enums\HealthCaseStatus;
use App\Enums\TreatmentReferralStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\HealthCase;
use App\Models\TreatmentReferral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VetDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_vet_dashboard_shows_dynamic_stats(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
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
            'code' => 'TST-VD-001',
            'species' => 'غزال',
            'name' => 'نور',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $healthCase = HealthCase::create([
            'case_number' => 'HC-2026-VD-001',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'description' => 'إصابة تحتاج إحالة علاج',
            'follow_up_kind' => HealthCaseFollowUpKind::NeedsReferral,
            'status' => HealthCaseStatus::Referred,
        ]);

        TreatmentReferral::create([
            'referral_number' => 'TR-2026-VD-001',
            'health_case_id' => $healthCase->id,
            'animal_id' => $animal->id,
            'group' => 'الغزلان',
            'status' => TreatmentReferralStatus::Pending,
            'referred_by' => $careHead->id,
            'referred_at' => now(),
        ]);

        $response = $this->actingAs($vetHead)->get('/vet/dashboard');

        $response->assertOk();
        $response->assertSee('إحالات علاج', false);
        $response->assertSee('>1<', false);
        $response->assertSee('نور (غزال)', false);
        $response->assertSee('TST-VD-001', false);
        $response->assertDontSee('الفهد البري', false);
        $response->assertDontSee('ANM-109', false);
    }
}
