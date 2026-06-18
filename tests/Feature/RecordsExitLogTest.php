<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\AnimalExit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordsExitLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_officer_sees_exit_log(): void
    {
        [$animal, $exit] = $this->seedExit('transfer', 'حديقة طرابلس البحرية', 'برنامج تبادل حيوانات');

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/exits')
            ->assertOk()
            ->assertSee($animal->code, false)
            ->assertSee('حديقة طرابلس البحرية', false)
            ->assertSee('برنامج تبادل حيوانات', false)
            ->assertSee('نقل', false)
            ->assertSee('سجل الحيوانات الخارجة', false);
    }

    public function test_exit_log_filters_by_exit_type(): void
    {
        [$transferAnimal] = $this->seedExit('transfer', 'حديقة أ', 'سبب أ');
        [$giftAnimal] = $this->seedExit('gift', 'مركز ب', 'سبب ب');

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $this->actingAs($officer)
            ->get('/records/logs/exits?exit_type=transfer')
            ->assertOk()
            ->assertSee($transferAnimal->code, false)
            ->assertDontSee($giftAnimal->code, false);

        $this->actingAs($officer)
            ->get('/records/logs/exits?exit_type=gift')
            ->assertOk()
            ->assertSee($giftAnimal->code, false)
            ->assertDontSee($transferAnimal->code, false);
    }

    /** @return array{0: Animal, 1: AnimalExit} */
    private function seedExit(string $exitType, string $recipient, string $reason): array
    {
        static $counter = 0;
        $counter++;

        $officer = User::factory()->create([
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'E'.$counter.'00',
            'name' => 'لونا',
            'species' => 'لاما',
            'group' => 'الدب واللامة',
            'gender' => 'أنثى',
            'age_method' => 'birth',
            'birth_date' => '2020-01-01',
            'origin' => 'وارد',
            'source' => 'records',
            'status' => AnimalStatus::Exited->value,
            'registered_at' => now()->subYear(),
        ]);

        $exit = AnimalExit::create([
            'animal_id' => $animal->id,
            'recorded_by' => $officer->id,
            'exit_date' => '2025-10-20',
            'exit_type' => $exitType,
            'recipient' => $recipient,
            'reason' => $reason,
        ]);

        return [$animal, $exit];
    }
}
