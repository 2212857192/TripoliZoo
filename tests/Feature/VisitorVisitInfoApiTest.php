<?php

namespace Tests\Feature;

use App\Models\VisitSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorVisitInfoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_load_visit_info_from_backend(): void
    {
        VisitSetting::current()->update([
            'status_text' => 'مفتوحة — أهلاً بزوارنا',
            'status_visible' => true,
            'ambulance_phone' => '193',
            'security_phone' => '091-555-0123',
            'open_time' => '10:00',
            'close_time' => '18:00',
            'last_ticket_time_note' => 'قبل ساعة واحدة من موعد الإغلاق',
            'entry_instructions' => "• الإشراف على الأطفال.\n• الالتزام بالمسارات.",
        ]);

        $this->getJson('/api/visit-info')
            ->assertOk()
            ->assertJsonPath('data.status.text', 'مفتوحة — أهلاً بزوارنا')
            ->assertJsonPath('data.hours.working_hours_label', '10:00 - 18:00')
            ->assertJsonPath('data.hours.schedule_label', 'مفتوحة يومياً')
            ->assertJsonPath('data.hours.working_days_label', 'مفتوحة يومياً')
            ->assertJsonPath('data.hours.closed_days_label', null)
            ->assertJsonPath('data.hours.open_daily', true)
            ->assertJsonPath('data.ambulance_phone', '193')
            ->assertJsonPath('data.security_phone', '091-555-0123')
            ->assertJsonCount(2, 'data.guidelines')
            ->assertJsonMissingPath('data.ticket_types');
    }
}
