<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\FieldCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SupervisorNutritionRecommendationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_dashboard_lists_active_diet_recommendations_for_group(): void
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G-200',
            'name' => 'غزال صغير',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $fieldCase = FieldCase::create([
            'case_number' => 'FC-2026-001',
            'animal_id' => $animal->id,
            'group' => 'الغزلان',
            'open_reason' => 'عرج',
            'status' => 'active',
            'opened_by' => $vet->id,
            'opened_at' => now(),
        ]);

        Sanctum::actingAs($vet);

        $this->postJson('/api/auth/doctor/cases/field-'.$fieldCase->case_number.'/procedures', [
            'diagnosis' => 'التواء',
            'treatment' => 'راحة',
            'case_result' => 'continue_treatment',
            'nutrition' => [
                'recommendation_text' => 'علف لين لمدة 5 أيام',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(5)->toDateString(),
                'note' => 'تجنب الحبوب الكاملة',
            ],
        ])->assertCreated();

        Sanctum::actingAs($supervisor);

        $response = $this->getJson('/api/auth/supervisor/dashboard');

        $response->assertOk();
        $response->assertJsonPath('active_diet_recommendations', 1);
        $response->assertJsonPath('diet_recommendations.0.animal_id', 'G-200');
        $response->assertJsonPath('diet_recommendations.0.instruction', 'علف لين لمدة 5 أيام');
        $response->assertJsonPath('diet_recommendations.0.doctor_note', 'تجنب الحبوب الكاملة');
    }
}
