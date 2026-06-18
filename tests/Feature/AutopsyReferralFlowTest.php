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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AutopsyReferralFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_referring_mortality_case_creates_autopsy_referral_and_notifies_vet_head(): void
    {
        [$careHead, $mortalityCase] = $this->seedMortalityCase(deathCause: null);

        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $this->actingAs($careHead)
            ->post(route('care.mortality.refer-autopsy', $mortalityCase->case_number), [
                'autopsy_reason' => 'سبب غير ظاهر',
            ])
            ->assertRedirect(route('care.mortality.index'));

        $referral = AutopsyReferral::query()->first();
        $this->assertNotNull($referral);

        $this->assertDatabaseHas('autopsy_referrals', [
            'mortality_case_id' => $mortalityCase->id,
            'animal_id' => $mortalityCase->animal_id,
            'status' => AutopsyReferralStatus::Pending->value,
            'transfer_reason' => 'سبب غير ظاهر',
            'referred_by' => $careHead->id,
        ]);

        $this->assertDatabaseHas('autopsy_referral_notifications', [
            'user_id' => $vetHead->id,
            'autopsy_referral_id' => $referral->id,
        ]);
    }

    public function test_vet_head_sees_autopsy_referral_on_index_page(): void
    {
        [$careHead, $mortalityCase, $referral, $vetHead] = $this->seedAutopsyReferral();

        $this->actingAs($vetHead)
            ->get(route('vet.referrals.autopsy.index'))
            ->assertOk()
            ->assertSee($referral->referral_number, false);
    }

    public function test_vet_head_can_view_autopsy_referral_details(): void
    {
        [, , $referral, $vetHead] = $this->seedAutopsyReferral();

        $this->actingAs($vetHead)
            ->get(route('vet.referrals.autopsy.show', $referral->referral_number))
            ->assertOk()
            ->assertSee($referral->referral_number, false)
            ->assertSee('توثيق نتيجة التشريح', false);
    }

    public function test_vet_head_can_document_autopsy_referral(): void
    {
        Storage::fake('public');

        [$careHead, $mortalityCase, $referral, $vetHead] = $this->seedAutopsyReferral();
        $animal = Animal::withoutGlobalScopes()->findOrFail($mortalityCase->animal_id);

        $this->actingAs($vetHead)
            ->post(route('vet.referrals.autopsy.document', $referral->referral_number), [
                'final_death_cause' => 'التهاب رئوي حاد',
                'autopsy_notes' => 'تقرير تشريح مكتمل',
                'documented_at' => now()->toDateString(),
                'report' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('vet.referrals.autopsy.show', $referral->referral_number));

        $referral->refresh();

        $this->assertDatabaseHas('autopsy_referrals', [
            'id' => $referral->id,
            'status' => AutopsyReferralStatus::Documented->value,
            'final_death_cause' => 'التهاب رئوي حاد',
            'documented_by' => $vetHead->id,
        ]);

        $this->assertNotNull($referral->report_path);
        Storage::disk('public')->assertExists($referral->report_path);

        $this->assertDatabaseHas('animals', [
            'id' => $animal->id,
            'status' => AnimalStatus::Dead->value,
        ]);
    }

    public function test_care_head_sees_autopsy_referrals_read_only(): void
    {
        [$careHead, , $referral] = $this->seedAutopsyReferral();

        $this->actingAs($careHead)
            ->get(route('care.referrals.autopsy.index'))
            ->assertOk()
            ->assertSee($referral->referral_number, false)
            ->assertSee('id="autopsyModal"', false);
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

        return [$careHead, $mortalityCase];
    }

    /** @return array{0: User, 1: MortalityCase, 2: AutopsyReferral, 3: User} */
    private function seedAutopsyReferral(): array
    {
        [$careHead, $mortalityCase] = $this->seedMortalityCase(deathCause: null);

        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $this->actingAs($careHead)->post(
            route('care.mortality.refer-autopsy', $mortalityCase->case_number),
            ['autopsy_reason' => 'سبب غير ظاهر'],
        );

        $referral = AutopsyReferral::query()->firstOrFail();

        return [$careHead, $mortalityCase, $referral, $vetHead];
    }
}
