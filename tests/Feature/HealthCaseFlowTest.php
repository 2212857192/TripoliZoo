<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\HealthCaseFollowUpKind;
use App\Enums\HealthCaseStatus;
use App\Enums\TreatmentReferralStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\HealthCase;
use App\Models\HealthCaseNotification;
use App\Models\TreatmentReferral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HealthCaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_create_health_case_and_notify_care_head(): void
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G001',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        Sanctum::actingAs($supervisor);

        $response = $this->postJson('/api/auth/supervisor/health-cases', [
            'animal_code' => 'G001',
            'description' => 'عرج واضح في الساق الأمامية',
            'follow_up_kind' => HealthCaseFollowUpKind::NeedsReferral->value,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('health_cases', [
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'status' => HealthCaseStatus::New->value,
            'follow_up_kind' => HealthCaseFollowUpKind::NeedsReferral->value,
        ]);

        $healthCase = HealthCase::query()->first();

        $this->assertDatabaseHas('health_case_notifications', [
            'user_id' => $careHead->id,
            'health_case_id' => $healthCase->id,
        ]);

        $this->assertDatabaseMissing('health_case_notifications', [
            'user_id' => $vet->id,
            'health_case_id' => $healthCase->id,
        ]);
    }

    public function test_care_head_can_review_health_case(): void
    {
        [$careHead, $healthCase] = $this->seedHealthCase();

        $response = $this->actingAs($careHead)->post(
            route('care.health.review', $healthCase->case_number),
        );

        $response->assertRedirect(route('care.health.index'));
        $this->assertDatabaseHas('health_cases', [
            'id' => $healthCase->id,
            'status' => HealthCaseStatus::Reviewed->value,
            'reviewed_by' => $careHead->id,
        ]);
    }

    public function test_care_head_can_refer_health_case_and_create_treatment_referral(): void
    {
        [$careHead, $healthCase] = $this->seedHealthCase();

        $response = $this->actingAs($careHead)->post(
            route('care.health.refer', $healthCase->case_number),
        );

        $response->assertRedirect(route('care.health.index'));
        $this->assertDatabaseHas('health_cases', [
            'id' => $healthCase->id,
            'status' => HealthCaseStatus::Referred->value,
            'referred_by' => $careHead->id,
        ]);

        $this->assertDatabaseHas('treatment_referrals', [
            'health_case_id' => $healthCase->id,
            'status' => TreatmentReferralStatus::Pending->value,
            'referred_by' => $careHead->id,
        ]);
    }

    public function test_care_head_cannot_refer_health_case_marked_no_referral(): void
    {
        [$careHead, $healthCase] = $this->seedHealthCase(
            followUpKind: HealthCaseFollowUpKind::NoReferral,
        );

        $this->actingAs($careHead)
            ->post(route('care.health.refer', $healthCase->case_number))
            ->assertStatus(422);

        $this->assertDatabaseHas('health_cases', [
            'id' => $healthCase->id,
            'status' => HealthCaseStatus::New->value,
        ]);
    }

    public function test_care_health_page_includes_case_data_for_details_modal(): void
    {
        [$careHead, $healthCase] = $this->seedHealthCase();

        $this->actingAs($careHead)
            ->get(route('care.health.index'))
            ->assertOk()
            ->assertSee('data-case-number="'.$healthCase->case_number.'"', false)
            ->assertSee('"'.$healthCase->case_number.'":', false);
    }

    public function test_supervisor_can_upload_attachment_with_health_case(): void
    {
        Storage::fake('public');

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'G002',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        Sanctum::actingAs($supervisor);

        $this->post('/api/auth/supervisor/health-cases', [
            'animal_code' => 'G002',
            'description' => 'جروح سطحية',
            'follow_up_kind' => HealthCaseFollowUpKind::NoReferral->value,
            'attachment' => UploadedFile::fake()->create('case.jpg', 100, 'image/jpeg'),
        ])->assertCreated();

        $healthCase = HealthCase::query()->first();
        $this->assertTrue($healthCase->has_attachment);
        $this->assertNotNull($healthCase->attachment_path);
        Storage::disk('public')->assertExists($healthCase->attachment_path);
    }

    public function test_referring_health_case_notifies_vet_head_and_shows_on_referrals_page(): void
    {
        [$careHead, $healthCase] = $this->seedHealthCase();

        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $this->actingAs($careHead)->post(
            route('care.health.refer', $healthCase->case_number),
        )->assertRedirect(route('care.health.index'));

        $referral = TreatmentReferral::query()->first();
        $this->assertNotNull($referral);

        $this->assertDatabaseHas('treatment_referral_notifications', [
            'user_id' => $vetHead->id,
            'treatment_referral_id' => $referral->id,
        ]);

        $this->actingAs($vetHead)
            ->get(route('vet.referrals.treatment.index'))
            ->assertOk()
            ->assertSee($referral->referral_number, false)
            ->assertSee('id="referralModal"', false);
    }

    public function test_vet_head_can_approve_treatment_referral(): void
    {
        [$careHead, $healthCase] = $this->seedHealthCase();
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $this->actingAs($careHead)->post(route('care.health.refer', $healthCase->case_number));
        $referral = TreatmentReferral::query()->firstOrFail();

        $this->actingAs($vetHead)
            ->post(route('vet.referrals.treatment.approve', $referral->referral_number))
            ->assertRedirect(route('vet.referrals.treatment.index'));

        $this->assertDatabaseHas('treatment_referrals', [
            'id' => $referral->id,
            'status' => TreatmentReferralStatus::Approved->value,
            'reviewed_by' => $vetHead->id,
        ]);

        $this->assertDatabaseHas('hospital_cases', [
            'treatment_referral_id' => $referral->id,
            'health_case_id' => $healthCase->id,
            'animal_id' => $healthCase->animal_id,
            'admitted_by' => $vetHead->id,
        ]);

        $hospitalCase = \App\Models\HospitalCase::query()->first();
        $this->assertNotNull($hospitalCase);

        $this->actingAs($vetHead)
            ->get(route('vet.cases.hospital.index'))
            ->assertOk()
            ->assertSee($hospitalCase->case_number, false);

        $this->actingAs($vetHead)
            ->get(route('vet.cases.hospital.show', $hospitalCase->case_number))
            ->assertOk()
            ->assertSee($healthCase->description, false);
    }

    public function test_supervisor_can_list_health_cases_filtered_by_date(): void
    {
        [$careHead, $healthCase] = $this->seedHealthCase();

        $supervisor = User::query()->where('role', UserRole::GroupSupervisor->value)->firstOrFail();

        Sanctum::actingAs($supervisor);

        $today = now()->toDateString();

        $this->getJson("/api/auth/supervisor/health-cases?date={$today}")
            ->assertOk()
            ->assertJsonPath('data.0.case_number', $healthCase->case_number);

        $yesterday = now()->subDay()->toDateString();

        $this->getJson("/api/auth/supervisor/health-cases?date={$yesterday}")
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    /** @return array{0: User, 1: HealthCase} */
    private function seedHealthCase(
        HealthCaseFollowUpKind $followUpKind = HealthCaseFollowUpKind::NeedsReferral,
    ): array {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $careHead = User::factory()->create([
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G003',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $healthCase = HealthCase::create([
            'case_number' => 'HC-2026-001',
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'description' => 'خمول',
            'follow_up_kind' => $followUpKind,
            'status' => HealthCaseStatus::New,
        ]);

        HealthCaseNotification::create([
            'user_id' => $careHead->id,
            'health_case_id' => $healthCase->id,
            'title' => 'حالة صحية جديدة',
            'message' => 'اختبار',
        ]);

        return [$careHead, $healthCase];
    }
}
