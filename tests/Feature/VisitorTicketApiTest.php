<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\TicketSale;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VisitorTicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_list_active_ticket_types(): void
    {
        TicketType::create([
            'name' => 'تذكرة الكبار',
            'price' => 10,
            'target_description' => 'فوق 12 سنة',
            'visitor_nationality' => 'مواطن',
            'visitor_age_group' => 'بالغ',
            'is_active' => true,
        ]);

        TicketType::create([
            'name' => 'تذكرة موقوفة',
            'price' => 5,
            'visitor_nationality' => 'مواطن',
            'visitor_age_group' => 'طفل',
            'is_active' => false,
        ]);

        $this->getJson('/api/ticket-types')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'تذكرة الكبار');
    }

    public function test_visitor_can_purchase_cash_tickets_and_list_them(): void
    {
        $visitor = User::factory()->create([
            'role' => UserRole::Visitor->value,
            'status' => 'active',
        ]);

        $type = TicketType::create([
            'name' => 'تذكرة الكبار',
            'price' => 10,
            'target_description' => 'فوق 12 سنة',
            'visitor_nationality' => 'مواطن',
            'visitor_age_group' => 'بالغ',
            'is_active' => true,
        ]);

        Sanctum::actingAs($visitor);

        $this->postJson('/api/tickets/purchase/cash', [
            'items' => [
                ['ticket_type_id' => $type->id, 'quantity' => 2],
            ],
        ])
            ->assertCreated()
            ->assertJsonCount(2, 'data');

        $this->assertDatabaseCount('ticket_sales', 2);

        $sale = TicketSale::query()->first();
        $this->assertSame($visitor->id, $sale->sold_by);
        $this->assertSame('cash', $sale->payment_method);

        $this->getJson('/api/tickets')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.ticket_number', $sale->ticket_number);
    }

    public function test_electronic_purchase_requires_plutu_configuration(): void
    {
        config([
            'plutu.api_key' => null,
            'plutu.access_token' => null,
        ]);

        $visitor = User::factory()->create([
            'role' => UserRole::Visitor->value,
            'status' => 'active',
        ]);

        $type = TicketType::create([
            'name' => 'تذكرة الكبار',
            'price' => 10,
            'visitor_nationality' => 'مواطن',
            'visitor_age_group' => 'بالغ',
            'is_active' => true,
        ]);

        Sanctum::actingAs($visitor);

        $this->postJson('/api/tickets/purchase/electronic/verify', [
            'items' => [
                ['ticket_type_id' => $type->id, 'quantity' => 1],
            ],
            'mobile' => '0912345678',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['payment']);
    }
}
