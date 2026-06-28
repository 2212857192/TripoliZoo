<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\QuarantineStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\Quarantine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class QuarantineCloseTest extends TestCase
{
    use RefreshDatabase;

    public function test_vet_head_can_close_quarantine_case_from_web(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'QZ-CLOSE-01',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => now(),
        ]);

        $quarantine = Quarantine::create([
            'case_number' => 'QR-2026-CLOSE',
            'animal_id' => $animal->id,
            'reason' => '',
            'initial_health_status' => 'ضعيف',
            'status' => QuarantineStatus::UnderFollowUp,
            'entry_date' => now(),
            'responsible_vet_id' => $vet->id,
            'created_by' => $vetHead->id,
        ]);

        $response = $this->actingAs($vetHead)->post(route('quarantine.close', $quarantine), [
            'close_reason' => 'نفوق داخل الحجر',
            'close_notes' => 'توثيق إداري',
        ]);

        $response->assertRedirect(route('quarantine.index'));
        $response->assertSessionHas('success');

        $quarantine->refresh();
        $this->assertSame(QuarantineStatus::Failed, $quarantine->status);
        $this->assertSame('نفوق داخل الحجر', $quarantine->close_reason);
        $this->assertSame('توثيق إداري', $quarantine->close_notes);
    }

    public function test_veterinarian_can_add_note_and_vaccine_from_mobile_api(): void
    {
        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'QZ-NOTE-01',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'أنثى',
            'status' => AnimalStatus::Quarantine->value,
            'registered_at' => now(),
        ]);

        $quarantine = Quarantine::create([
            'case_number' => 'QR-2026-NOTE',
            'animal_id' => $animal->id,
            'reason' => '',
            'initial_health_status' => 'جيد',
            'status' => QuarantineStatus::UnderFollowUp,
            'entry_date' => now(),
            'responsible_vet_id' => $vet->id,
            'created_by' => $vet->id,
        ]);

        Sanctum::actingAs($vet);

        $this->postJson("/api/auth/doctor/quarantines/{$quarantine->case_number}/notes", [
            'note' => 'تحسّن ملحوظ في الشهية',
        ])->assertOk()
            ->assertJsonPath('message', 'تم تسجيل الملاحظة الصحية.');

        $this->postJson("/api/auth/doctor/quarantines/{$quarantine->case_number}/vaccines", [
            'name' => 'لقاح الحمى القلاعية',
            'administered_at' => now()->toDateString(),
            'note' => 'جرعة أولى',
        ])->assertOk()
            ->assertJsonPath('message', 'تم تسجيل الجرعة الوقائية.');

        $this->assertDatabaseHas('quarantine_notes', [
            'quarantine_id' => $quarantine->id,
            'note' => 'تحسّن ملحوظ في الشهية',
        ]);

        $this->assertDatabaseHas('quarantine_vaccines', [
            'quarantine_id' => $quarantine->id,
            'name' => 'لقاح الحمى القلاعية',
        ]);
    }
}
