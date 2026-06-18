<?php

namespace Tests\Feature;

use App\Models\VisitSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorVisitInfoWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_visit_info_page_renders_backend_data(): void
    {
        VisitSetting::current()->update([
            'status_text' => 'مفتوحة — أهلاً بزوارنا',
            'status_visible' => true,
            'ambulance_phone' => '193',
            'open_time' => '10:00',
            'close_time' => '18:00',
            'entry_instructions' => '• الإشراف على الأطفال.',
        ]);

        $this->get('/app/visit-info')
            ->assertOk()
            ->assertSee('معلومات الزيارة')
            ->assertSee('مفتوحة — أهلاً بزوارنا')
            ->assertSee('10:00 - 18:00')
            ->assertSee('أرقام الطوارئ')
            ->assertSee('193')
            ->assertSee('الإشراف على الأطفال')
            ->assertDontSee('أسعار تذاكر الدخول')
            ->assertDontSee('موقع الحديقة');
    }
}
