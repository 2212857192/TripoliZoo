<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTicketTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_newly_created_active_ticket_appears_in_visitor_api(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.tickets.store'), [
                'target_description' => 'تذكرة عائلية',
                'price' => 25,
                'visitor_nationality' => 'مواطن',
                'visitor_age_group' => 'جميع الأعمار',
            ])
            ->assertRedirect(route('admin.tickets.index'));

        $this->getJson('/api/ticket-types')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'تذكرة عائلية');
    }

    public function test_admin_can_manage_ticket_types(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.tickets.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.tickets.store'), [
                'target_description' => 'تذكرة الكبار',
                'price' => 10,
                'visitor_nationality' => 'مواطن',
                'visitor_age_group' => '12 سنة فأكثر',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.tickets.index'));

        $ticket = TicketType::query()->where('target_description', 'تذكرة الكبار')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.tickets.update', $ticket), [
                'target_description' => 'تذكرة الكبار',
                'price' => 12,
                'visitor_nationality' => 'مواطن',
                'visitor_age_group' => '12 سنة فأكثر',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.tickets.index'));

        $ticket->refresh();
        $this->assertSame('12.00', $ticket->price);

        $this->actingAs($admin)
            ->patch(route('admin.tickets.toggle', $ticket))
            ->assertRedirect();

        $ticket->refresh();
        $this->assertFalse($ticket->is_active);
    }
}
