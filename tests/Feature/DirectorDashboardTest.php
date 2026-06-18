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

class DirectorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_director_dashboard_shows_dynamic_stats(): void
    {
        $director = User::factory()->create([
            'role' => UserRole::Director->value,
            'status' => 'active',
        ]);

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'DIR-AN-001',
            'species' => 'غزال',
            'name' => 'سند',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        HealthCase::create([
            'case_number' => 'HC-DIR-001',
            'animal_id' => Animal::withoutGlobalScopes()->first()->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'description' => 'حالة صحية للاختبار',
            'follow_up_kind' => HealthCaseFollowUpKind::NeedsReferral,
            'status' => HealthCaseStatus::New,
        ]);

        $response = $this->actingAs($director)->get('/director/dashboard');

        $response->assertOk();
        $response->assertSee('إجمالي الحيوانات داخل الحديقة', false);
        $response->assertSee('>1<', false);
        $response->assertSee('ملخص اليوم', false);
        $response->assertDontSee('ANM-109', false);
        $response->assertDontSee('248', false);
    }
}
