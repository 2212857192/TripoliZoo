<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\FieldCaseStatus;
use App\Enums\HealthCaseFollowUpKind;
use App\Enums\MortalityCaseStatus;
use App\Enums\MortalityVictimKind;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\FieldCase;
use App\Models\MortalityCase;
use App\Models\MortalityCaseNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MortalityCaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_create_mortality_case_and_notify_care_head(): void
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

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G001',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        Sanctum::actingAs($supervisor);

        $response = $this->postJson('/api/auth/supervisor/mortality-cases', [
            'animal_code' => 'G001',
            'victim_kind' => MortalityVictimKind::ZooAnimal->value,
            'death_cause' => 'إصابة واضحة في الرقبة',
            'notes' => 'وجد ميتاً قرب السياج',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('mortality_cases', [
            'animal_id' => $animal->id,
            'supervisor_id' => $supervisor->id,
            'status' => MortalityCaseStatus::New->value,
            'death_cause' => 'إصابة واضحة في الرقبة',
        ]);

        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'status' => AnimalStatus::PendingMortalityApproval->value,
        ]);

        $mortalityCase = MortalityCase::query()->first();

        $this->assertDatabaseHas('mortality_case_notifications', [
            'user_id' => $careHead->id,
            'mortality_case_id' => $mortalityCase->id,
        ]);
    }

    public function test_supervisor_mortality_without_cause_stores_null_cause(): void
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
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

        $this->postJson('/api/auth/supervisor/mortality-cases', [
            'animal_code' => 'G002',
            'victim_kind' => MortalityVictimKind::ZooAnimal->value,
        ])->assertCreated();

        $this->assertDatabaseHas('mortality_cases', [
            'death_cause' => null,
        ]);
    }

    public function test_care_head_sees_mortality_case_on_index_page(): void
    {
        [$careHead, $mortalityCase] = $this->seedMortalityCase();

        $this->actingAs($careHead)
            ->get(route('care.mortality.index'))
            ->assertOk()
            ->assertSee($mortalityCase->case_number, false)
            ->assertSee('id="mortalityModal"', false);
    }

    public function test_care_head_can_approve_mortality_case_with_apparent_cause(): void
    {
        [$careHead, $mortalityCase] = $this->seedMortalityCase();

        $this->actingAs($careHead)
            ->post(route('care.mortality.approve', $mortalityCase->case_number))
            ->assertRedirect(route('care.mortality.index'));

        $this->assertDatabaseHas('mortality_cases', [
            'id' => $mortalityCase->id,
            'status' => MortalityCaseStatus::Approved->value,
            'reviewed_by' => $careHead->id,
        ]);

        $this->assertDatabaseHas('animals', [
            'id' => $mortalityCase->animal_id,
            'status' => AnimalStatus::Dead->value,
        ]);
    }

    public function test_care_head_can_refer_mortality_case_for_autopsy(): void
    {
        [$careHead, $mortalityCase] = $this->seedMortalityCase(deathCause: null);

        $this->actingAs($careHead)
            ->post(route('care.mortality.refer-autopsy', $mortalityCase->case_number), [
                'autopsy_reason' => 'سبب غير ظاهر',
            ])
            ->assertRedirect(route('care.mortality.index'));

        $this->assertDatabaseHas('mortality_cases', [
            'id' => $mortalityCase->id,
            'status' => MortalityCaseStatus::ReferredForAutopsy->value,
            'autopsy_reason' => 'سبب غير ظاهر',
        ]);

        $this->assertDatabaseHas('autopsy_referrals', [
            'mortality_case_id' => $mortalityCase->id,
            'transfer_reason' => 'سبب غير ظاهر',
        ]);
    }

    public function test_pending_mortality_animal_cannot_receive_new_health_case(): void
    {
        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'G099',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        Sanctum::actingAs($supervisor);

        $this->postJson('/api/auth/supervisor/mortality-cases', [
            'animal_code' => 'G099',
            'victim_kind' => MortalityVictimKind::ZooAnimal->value,
            'death_cause' => 'نفوق موثق',
        ])->assertCreated();

        $this->postJson('/api/auth/supervisor/health-cases', [
            'animal_code' => 'G099',
            'description' => 'محاولة تسجيل بعد النفوق',
            'follow_up_kind' => HealthCaseFollowUpKind::NoReferral->value,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'لا يمكن تنفيذ الإجراء، الحيوان موقوف بانتظار اعتماد حالة النفوق.');
    }

    public function test_dead_animal_cannot_receive_new_health_case(): void
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

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G100',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        Sanctum::actingAs($supervisor);

        $this->postJson('/api/auth/supervisor/mortality-cases', [
            'animal_code' => 'G100',
            'victim_kind' => MortalityVictimKind::ZooAnimal->value,
            'death_cause' => 'نفوق موثق',
        ])->assertCreated();

        $mortalityCase = MortalityCase::query()->where('animal_id', $animal->id)->firstOrFail();

        $this->actingAs($careHead)
            ->post(route('care.mortality.approve', $mortalityCase->case_number))
            ->assertRedirect(route('care.mortality.index'));

        Sanctum::actingAs($supervisor);

        $this->postJson('/api/auth/supervisor/health-cases', [
            'animal_code' => 'G100',
            'description' => 'محاولة تسجيل بعد النفوق',
            'follow_up_kind' => HealthCaseFollowUpKind::NoReferral->value,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'لا يمكن تنفيذ الإجراء، الحيوان غير نشط.');
    }

    public function test_supervisor_can_register_mortality_with_open_field_case(): void
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
            'code' => 'G097',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $fieldCase = FieldCase::create([
            'case_number' => 'FC-2026-097',
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'open_reason' => 'متابعة ميدانية',
            'status' => FieldCaseStatus::Active,
            'opened_by' => $vet->id,
            'opened_at' => now(),
        ]);

        Sanctum::actingAs($supervisor);

        $this->postJson('/api/auth/supervisor/mortality-cases', [
            'animal_code' => 'G097',
            'victim_kind' => MortalityVictimKind::ZooAnimal->value,
            'death_cause' => 'نفوق مفاجئ',
        ])->assertCreated();

        $this->assertDatabaseHas('field_cases', [
            'id' => $fieldCase->id,
            'status' => FieldCaseStatus::Closed->value,
            'closing_note' => 'أُغلقت تلقائياً بسبب تسجيل حالة نفوق.',
        ]);

        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'status' => AnimalStatus::PendingMortalityApproval->value,
        ]);
    }

    public function test_supervisor_can_create_health_case_while_field_case_is_open(): void
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
            'code' => 'G098',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        FieldCase::create([
            'case_number' => 'FC-2026-098',
            'animal_id' => $animal->id,
            'group' => $animal->group,
            'open_reason' => 'متابعة ميدانية',
            'status' => FieldCaseStatus::Active,
            'opened_by' => $vet->id,
            'opened_at' => now(),
        ]);

        Sanctum::actingAs($supervisor);

        $this->postJson('/api/auth/supervisor/health-cases', [
            'animal_code' => 'G098',
            'description' => 'ملاحظة للرعاية رغم وجود حالة ميدانية',
            'follow_up_kind' => HealthCaseFollowUpKind::NoReferral->value,
        ])
            ->assertCreated()
            ->assertJsonPath('data.description', 'ملاحظة للرعاية رغم وجود حالة ميدانية');
    }

    /** @return array{0: User, 1: MortalityCase} */
    private function seedMortalityCase(?string $deathCause = 'إصابة واضحة'): array
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

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'G010',
            'name' => 'ريم',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        $mortalityCase = MortalityCase::create([
            'case_number' => 'MC-2026-001',
            'animal_id' => $animal->id,
            'subject_code' => $animal->code,
            'subject_type' => $animal->species,
            'supervisor_id' => $supervisor->id,
            'group' => $animal->group,
            'victim_kind' => MortalityVictimKind::ZooAnimal,
            'death_cause' => $deathCause,
            'notes' => 'ملاحظة تجريبية',
            'death_date' => now()->toDateString(),
            'status' => MortalityCaseStatus::New,
        ]);

        MortalityCaseNotification::create([
            'user_id' => $careHead->id,
            'mortality_case_id' => $mortalityCase->id,
            'title' => 'حالة نفوق',
            'message' => 'اختبار',
        ]);

        return [$careHead, $mortalityCase];
    }
}
