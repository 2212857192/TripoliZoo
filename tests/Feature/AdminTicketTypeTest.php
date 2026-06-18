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
                'name' => 'تذكرة الكبار',
                'price' => 10,
                'target_description' => 'فوق 12 سنة',
                'visitor_nationality' => 'مواطن',
                'visitor_age_group' => 'بالغ',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.tickets.index'));

        $ticket = TicketType::query()->where('name', 'تذكرة الكبار')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.tickets.update', $ticket), [
                'name' => 'تذكرة الكبار',
                'price' => 12,
                'target_description' => 'فوق 12 سنة',
                'visitor_nationality' => 'مواطن',
                'visitor_age_group' => 'بالغ',
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
