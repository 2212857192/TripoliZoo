<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\HealthCaseFollowUpKind;
use App\Enums\HealthCaseStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\HealthCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_care_dashboard_shows_dynamic_stats(): void
    {
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
            'code' => 'TST-HC-001',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        HealthCase::create([
            'case_number' => 'HC-2026-001',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'description' => 'حالة تحتاج إحالة',
            'follow_up_kind' => HealthCaseFollowUpKind::NeedsReferral,
            'status' => HealthCaseStatus::New,
        ]);

        $response = $this->actingAs($careHead)->get('/care/dashboard');

        $response->assertOk();
        $response->assertSee('حالات صحية', false);
        $response->assertSee('>1<', false);
        $response->assertSee('حالة تحتاج إحالة', false);
        $response->assertDontSee('أسد إفريقي', false);
    }
}
