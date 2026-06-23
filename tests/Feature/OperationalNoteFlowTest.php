<?php

namespace Tests\Feature;

use App\Enums\OperationalNoteKind;
use App\Enums\OperationalNoteStatus;
use App\Enums\UserRole;
use App\Models\OperationalNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OperationalNoteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_create_operational_note_and_notify_care_head(): void
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

        Sanctum::actingAs($supervisor);

        $response = $this->postJson('/api/auth/supervisor/operational-notes', [
            'note_kind' => OperationalNoteKind::Feeding->value,
            'summary' => 'تأخر وصول الغذاء للمجموعة',
            'details' => 'تفاصيل إضافية عن التأخير',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('operational_notes', [
            'supervisor_id' => $supervisor->id,
            'group' => 'الغزلان',
            'note_kind' => OperationalNoteKind::Feeding->value,
            'summary' => 'تأخر وصول الغذاء للمجموعة',
            'status' => OperationalNoteStatus::New->value,
        ]);

        $note = OperationalNote::query()->first();
        $this->assertDatabaseHas('operational_note_notifications', [
            'user_id' => $careHead->id,
            'operational_note_id' => $note->id,
        ]);
    }

    public function test_supervisor_can_create_operational_note_with_attachment(): void
    {
        Storage::fake('public');

        $supervisor = User::factory()->create([
            'role' => UserRole::GroupSupervisor->value,
            'assigned_group' => 'القرود',
            'status' => 'active',
        ]);

        Sanctum::actingAs($supervisor);

        $this->post('/api/auth/supervisor/operational-notes', [
            'note_kind' => OperationalNoteKind::General->value,
            'summary' => 'تمزق في الشبك',
            'attachment' => UploadedFile::fake()->create('fence.jpg', 100, 'image/jpeg'),
        ])->assertCreated();

        $note = OperationalNote::query()->first();
        $this->assertTrue($note->has_attachment);
        $this->assertNotNull($note->attachment_path);
        Storage::disk('public')->assertExists($note->attachment_path);
    }

    public function test_care_head_sees_operational_notes_on_index_page(): void
    {
        [$careHead, $note] = $this->seedOperationalNote();

        $this->actingAs($careHead)
            ->get(route('care.notes.index'))
            ->assertOk()
            ->assertSee($note->note_number, false)
            ->assertSee('تحديد كمراجعة', false)
            ->assertDontSee('id="noteModal"', false);
    }

    public function test_care_head_can_mark_operational_note_as_reviewed(): void
    {
        [$careHead, $note] = $this->seedOperationalNote();

        $this->actingAs($careHead)
            ->post(route('care.notes.review', $note->note_number))
            ->assertRedirect(route('care.notes.index', ['status' => 'reviewed']));

        $this->assertDatabaseHas('operational_notes', [
            'id' => $note->id,
            'status' => OperationalNoteStatus::Reviewed->value,
            'reviewed_by' => $careHead->id,
        ]);
    }

    /** @return array{0: User, 1: OperationalNote} */
    private function seedOperationalNote(): array
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

        Sanctum::actingAs($supervisor);
        $this->postJson('/api/auth/supervisor/operational-notes', [
            'note_kind' => OperationalNoteKind::Feeding->value,
            'summary' => 'تأخر وصول الغذاء للمجموعة',
        ])->assertCreated();

        return [$careHead, OperationalNote::query()->firstOrFail()];
    }
}
