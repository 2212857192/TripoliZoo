<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\MortalityVictimKind;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\BirthRegistration;
use App\Models\BirthRegistrationNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BirthRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_register_birth_and_create_newborn_animals(): void
    {
        [$supervisor, $mother, $careHead] = $this->seedMother();

        Sanctum::actingAs($supervisor);

        $response = $this->post('/api/auth/supervisor/birth-registrations', $this->birthPayload([
            ['gender' => 'male', 'distinguishing_mark' => 'بقعة بيضاء'],
            ['gender' => 'female', 'note' => 'نشط'],
        ]));

        $response->assertCreated();

        $registration = BirthRegistration::query()->first();
        $this->assertNotNull($registration);
        $this->assertSame(2, $registration->birth_count);

        $this->assertDatabaseHas('animals', [
            'mother_id' => $mother->id,
            'birth_registration_id' => $registration->id,
            'status' => AnimalStatus::UnderBirthFollowUp->value,
            'gender' => 'ذكر',
            'distinguishing_marks' => 'بقعة بيضاء',
        ]);

        $this->assertDatabaseHas('animals', [
            'mother_id' => $mother->id,
            'birth_registration_id' => $registration->id,
            'status' => AnimalStatus::UnderBirthFollowUp->value,
            'gender' => 'أنثى',
            'registration_note' => 'نشط',
        ]);

        $this->assertSame(
            2,
            Animal::withQuarantine()
                ->where('birth_registration_id', $registration->id)
                ->whereNotNull('photo_path')
                ->count()
        );

        $this->assertDatabaseHas('birth_registration_notifications', [
            'user_id' => $careHead->id,
            'birth_registration_id' => $registration->id,
        ]);
    }

    public function test_supervisor_mothers_endpoint_returns_only_female_active_animals(): void
    {
        [$supervisor] = $this->seedMother();

        Animal::withoutGlobalScopes()->create([
            'code' => 'G200',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        Sanctum::actingAs($supervisor);

        $response = $this->getJson('/api/auth/supervisor/animals/mothers');

        $response->assertOk();
        $codes = collect($response->json('data'))->pluck('id')->all();
        $this->assertSame(['G100'], $codes);
    }

    public function test_supervisor_newborns_endpoint_lists_animals_under_follow_up(): void
    {
        [$supervisor, $mother] = $this->seedMother();
        $registration = $this->registerBirth($supervisor, $mother, 1);

        $newborn = Animal::underBirthFollowUp()->first();

        Sanctum::actingAs($supervisor);

        $response = $this->getJson('/api/auth/supervisor/animals/newborns');

        $response->assertOk();
        $this->assertContains($newborn->code, collect($response->json('data'))->pluck('id')->all());
        $this->assertNotEmpty($registration->registration_number);
    }

    public function test_mortality_for_newborn_requires_valid_under_follow_up_animal(): void
    {
        [$supervisor, $mother] = $this->seedMother();
        $this->registerBirth($supervisor, $mother, 1);
        $newborn = Animal::underBirthFollowUp()->first();

        Sanctum::actingAs($supervisor);

        $this->postJson('/api/auth/supervisor/mortality-cases', [
            'animal_code' => $newborn->code,
            'victim_kind' => MortalityVictimKind::NewbornUnderFollowUp->value,
            'death_cause' => 'ضعف',
        ])
            ->assertCreated()
            ->assertJsonPath('data.victim_kind', MortalityVictimKind::NewbornUnderFollowUp->value);

        $this->postJson('/api/auth/supervisor/mortality-cases', [
            'animal_code' => $newborn->code,
            'victim_kind' => MortalityVictimKind::ZooAnimal->value,
            'death_cause' => 'ضعف',
        ])->assertStatus(422);

        $this->postJson('/api/auth/supervisor/mortality-cases', [
            'animal_code' => 'G999',
        ])->assertStatus(422);
    }

    public function test_newborns_appear_in_general_supervisor_animals_list(): void
    {
        [$supervisor, $mother] = $this->seedMother();
        $this->registerBirth($supervisor, $mother, 1);
        $newborn = Animal::underBirthFollowUp()->first();

        Sanctum::actingAs($supervisor);

        $codes = collect($this->getJson('/api/auth/supervisor/animals')->json('data'))
            ->pluck('id')
            ->all();

        $this->assertContains($newborn->code, $codes);
    }

    public function test_newborn_can_receive_health_report_like_regular_animal(): void
    {
        [$supervisor, $mother] = $this->seedMother();
        $this->registerBirth($supervisor, $mother, 1);
        $newborn = Animal::underBirthFollowUp()->first();

        Sanctum::actingAs($supervisor);

        $this->postJson('/api/auth/supervisor/health-reports', [
            'animal_code' => $newborn->code,
            'description' => 'ضعف عام لدى المولود',
        ])->assertCreated();
    }

    public function test_doctor_animals_list_includes_newborns_under_follow_up(): void
    {
        [$supervisor, $mother] = $this->seedMother();
        $this->registerBirth($supervisor, $mother, 1);
        $newborn = Animal::underBirthFollowUp()->first();

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        Sanctum::actingAs($vet);

        $codes = collect($this->getJson('/api/auth/doctor/animals')->json('data'))
            ->pluck('id')
            ->all();

        $this->assertContains($newborn->code, $codes);
    }

    public function test_newborn_becomes_active_automatically_after_30_days(): void
    {
        [$supervisor, $mother, $careHead] = $this->seedMother();

        $birthDate = now()->subDays(30)->toDateString();

        Sanctum::actingAs($supervisor);

        $this->post('/api/auth/supervisor/birth-registrations', $this->birthPayload([
            ['gender' => 'male'],
        ], birthDate: $birthDate))->assertCreated();

        $newborn = Animal::withQuarantine()->where('mother_id', $mother->id)->first();
        $this->assertSame(AnimalStatus::UnderBirthFollowUp->value, $newborn->status);

        $this->actingAs($careHead)
            ->get(route('care.births.index'))
            ->assertOk();

        $newborn->refresh();
        $this->assertSame(AnimalStatus::Active->value, $newborn->status);

        Sanctum::actingAs($supervisor);
        $this->getJson('/api/auth/supervisor/animals/newborns')
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $codes = collect($this->getJson('/api/auth/supervisor/animals')->json('data'))
            ->pluck('id')
            ->all();
        $this->assertContains($newborn->code, $codes);
    }

    public function test_care_births_index_shows_registered_newborns(): void
    {
        [$supervisor, $mother, $careHead] = $this->seedMother();
        $this->registerBirth($supervisor, $mother, 1);
        $newborn = Animal::underBirthFollowUp()->first();

        $this->actingAs($careHead)
            ->get(route('care.births.index'))
            ->assertOk()
            ->assertSee($newborn->code)
            ->assertSee('قيد المتابعة');
    }

    public function test_care_births_index_shows_deceased_newborn_status(): void
    {
        [$supervisor, $mother, $careHead] = $this->seedMother();
        $this->registerBirth($supervisor, $mother, 1);
        $newborn = Animal::underBirthFollowUp()->firstOrFail();
        $newborn->update(['status' => AnimalStatus::Dead->value]);

        $this->actingAs($careHead)
            ->get(route('care.births.index'))
            ->assertOk()
            ->assertSee($newborn->code)
            ->assertSee('badge-status-deceased', false)
            ->assertSee('نافق', false);
    }

    public function test_birth_registration_requires_newborn_photo(): void
    {
        [$supervisor, $mother] = $this->seedMother();

        Sanctum::actingAs($supervisor);

        $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/auth/supervisor/birth-registrations', [
                'mother_code' => $mother->code,
                'birth_date' => now()->toDateString(),
                'birth_count' => 1,
                'newborns' => json_encode([
                    ['gender' => 'male'],
                ]),
            ])
            ->assertStatus(422);
    }

    /** @return array{0: User, 1: Animal, 2: User} */
    private function seedMother(): array
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

        $mother = Animal::withoutGlobalScopes()->create([
            'code' => 'G100',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        return [$supervisor, $mother, $careHead];
    }

    private function registerBirth(User $supervisor, Animal $mother, int $count): BirthRegistration
    {
        Sanctum::actingAs($supervisor);

        $newborns = [];
        for ($i = 0; $i < $count; $i++) {
            $newborns[] = ['gender' => 'male'];
        }

        $this->post('/api/auth/supervisor/birth-registrations', $this->birthPayload($newborns, $mother->code))
            ->assertCreated();

        return BirthRegistration::query()->firstOrFail();
    }

    /** @param  list<array<string, mixed>>  $newborns
     * @return array<string, mixed>
     */
    private function birthPayload(
        array $newborns,
        ?string $motherCode = null,
        ?string $birthDate = null,
    ): array {
        $payload = [
            'mother_code' => $motherCode ?? 'G100',
            'birth_date' => $birthDate ?? now()->toDateString(),
            'birth_count' => count($newborns),
            'newborns' => [],
            'newborn_photos' => [],
        ];

        foreach ($newborns as $index => $newborn) {
            $payload['newborns'][$index] = [
                'gender' => $newborn['gender'],
                'distinguishing_mark' => $newborn['distinguishing_mark'] ?? null,
                'note' => $newborn['note'] ?? null,
            ];
            $payload['newborn_photos'][$index] = $newborn['photo']
                ?? UploadedFile::fake()->create("newborn{$index}.jpg", 100, 'image/jpeg');
        }

        return $payload;
    }
}
