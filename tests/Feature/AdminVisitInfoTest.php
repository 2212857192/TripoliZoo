<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\VisitSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVisitInfoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_visit_info_without_weekly_holiday_fields(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.visit-info.update'), [
            'status_text' => 'مغلقة اليوم لأعمال الصيانة',
            'status_visible' => '1',
            'urgent_alert' => 'الحديقة مغلقة مؤقتاً حتى إشعار آخر.',
            'ambulance_phone' => '193',
            'security_phone' => '091-555-0123',
            'entry_instructions' => '• الالتزام بالتعليمات.',
            'open_time' => '09:00',
            'close_time' => '17:00',
            'last_ticket_time_note' => 'قبل 16:00',
        ]);

        $response->assertRedirect(route('admin.visit-info.show'));

        $settings = VisitSetting::current()->fresh();

        $this->assertSame('مغلقة اليوم لأعمال الصيانة', $settings->status_text);
        $this->assertSame('09:00', substr((string) $settings->open_time, 0, 5));
        $this->assertSame('17:00', substr((string) $settings->close_time, 0, 5));
        $this->assertSame(VisitSetting::defaultWorkingDays(), $settings->working_days);
    }
}
