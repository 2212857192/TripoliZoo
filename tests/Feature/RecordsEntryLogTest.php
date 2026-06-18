<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\QuarantineStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\Quarantine;
use App\Models\User;
use App\Services\ReceivingTaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordsEntryLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_entry_log_excludes_animals_still_in_quarantine(): void
    {
        [$animal] = $this->seedQuarantineEntry('2025-11-02');

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/entries')
            ->assertOk()
            ->assertDontSee($animal->code, false);
    }

    public function test_entry_log_lists_animals_after_health_release(): void
    {
        [$animal] = $this->seedReleasedEntryOnly();

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/entries')
            ->assertOk()
            ->assertSee($animal->code, false)
            ->assertSee('2025-09-15', false)
            ->assertSee('2025-10-08', false)
            ->assertSee('سجل الحيوانات الداخلة', false);
    }

    public function test_entry_log_shows_receipt_date_after_confirmation(): void
    {
        [$animal, $quarantine, $supervisor, $vetHead] = $this->seedReleasedEntry();

        $task = app(ReceivingTaskService::class)->createFromQuarantineRelease($quarantine->fresh('animal'), $vetHead);
        $this->assertNotNull($task);

        app(ReceivingTaskService::class)->confirmReceipt($task, $supervisor, 'تم التسليم');

        $receiptDate = $task->fresh()->received_at->format('Y-m-d');

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/entries?receipt=yes')
            ->assertOk()
            ->assertSee($animal->code, false)
            ->assertSee($receiptDate, false);

        $this->actingAs($officer)
            ->get('/records/logs/entries?receipt=no')
            ->assertOk()
            ->assertDontSee($animal->code, false);
    }

    /** @return array{0: Animal} */
    private function seedReleasedEntryOnly(): array
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'Q-2002',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'origin' => 'شراء',
            'source' => 'quarantine',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => '2025-09-15',
        ]);

        Quarantine::create([
            'case_number' => 'QR-2025-2002',
            'animal_id' => $animal->id,
            'reason' => '',
            'initial_health_status' => 'جيد',
            'status' => QuarantineStatus::HealthReleased,
            'entry_date' => '2025-09-15',
            'released_at' => '2025-10-08',
            'created_by' => $vetHead->id,
        ]);

        return [$animal];
    }

    /** @return array{0: Animal, 1: Quarantine} */
    private function seedQuarantineEntry(string $entryDate): array
    {
        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'Q-1045',
            'species' => 'زرافة نيلية',
            'group' => 'الثدييات الكبيرة',
            'gender' => 'أنثى',
            'origin' => 'شراء',
            'source' => 'quarantine',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => $entryDate,
        ]);

        $quarantine = Quarantine::create([
            'case_number' => 'QR-2025-1045',
            'animal_id' => $animal->id,
            'reason' => '',
            'initial_health_status' => 'جيد',
            'status' => QuarantineStatus::UnderFollowUp,
            'entry_date' => $entryDate,
            'created_by' => User::factory()->create(['role' => UserRole::VetHead->value])->id,
        ]);

        return [$animal, $quarantine];
    }

    /** @return array{0: Animal, 1: Quarantine, 2: User, 3: User} */
    private function seedReleasedEntry(): array
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'Q-2001',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'origin' => 'شراء',
            'source' => 'quarantine',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => '2025-09-15',
        ]);

        $quarantine = Quarantine::create([
            'case_number' => 'QR-2025-2001',
            'animal_id' => $animal->id,
            'reason' => '',
            'initial_health_status' => 'جيد',
            'status' => QuarantineStatus::HealthReleased,
            'entry_date' => '2025-09-15',
            'released_at' => '2025-10-08',
            'created_by' => $vetHead->id,
        ]);

        return [$animal, $quarantine, $supervisor, $vetHead];
    }
}
