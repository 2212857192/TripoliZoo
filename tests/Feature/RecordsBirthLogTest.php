<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\User;
use App\Services\BirthRegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecordsBirthLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_officer_sees_birth_log_from_registrations(): void
    {
        [$supervisor, $mother] = $this->seedMother();

        Sanctum::actingAs($supervisor);
        $this->post('/api/auth/supervisor/birth-registrations', $this->birthPayload($mother, '2026-06-01'))
            ->assertCreated();

        $newborn = Animal::withQuarantine()
            ->where('mother_id', $mother->id)
            ->firstOrFail();

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/births')
            ->assertOk()
            ->assertSee($newborn->code, false)
            ->assertSee($mother->code, false)
            ->assertSee('قيد المتابعة', false)
            ->assertSee('سجل الولادات', false);
    }

    public function test_birth_log_shows_completion_date_after_follow_up_period(): void
    {
        [$supervisor, $mother] = $this->seedMother();

        Sanctum::actingAs($supervisor);
        $birthDate = now()->subDays(BirthRegistrationService::FOLLOW_UP_DAYS + 1)->toDateString();
        $this->post('/api/auth/supervisor/birth-registrations', $this->birthPayload($mother, $birthDate))
            ->assertCreated();

        $newborn = Animal::withQuarantine()
            ->where('mother_id', $mother->id)
            ->firstOrFail();

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $expectedCompletion = $newborn->birth_date
            ->copy()
            ->addDays(BirthRegistrationService::FOLLOW_UP_DAYS)
            ->format('Y-m-d');

        $this->actingAs($officer)
            ->get('/records/logs/births?status=completed')
            ->assertOk()
            ->assertSee($newborn->code, false)
            ->assertSee($expectedCompletion, false);
    }

    /** @return array{0: User, 1: Animal} */
    private function seedMother(): array
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

        return [$supervisor, $mother];
    }

    /** @return array<string, mixed> */
    private function birthPayload(Animal $mother, string $birthDate): array
    {
        return [
            'mother_code' => $mother->code,
            'birth_date' => $birthDate,
            'birth_count' => 1,
            'newborns' => [
                ['gender' => 'male', 'distinguishing_mark' => 'بقعة'],
            ],
            'newborn_photos' => [
                UploadedFile::fake()->create('newborn0.jpg', 100, 'image/jpeg'),
            ],
        ];
    }
}
