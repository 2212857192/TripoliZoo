<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_web_login(): void
    {
        User::factory()->create([
            'email' => 'admin@tripolizoo.com',
            'password' => '12345678',
            'role' => 'مدير النظام',
            'status' => 'active',
        ]);

        $this->post('/login', [
            'email' => 'admin@tripolizoo.com',
            'password' => '12345678',
        ])->assertRedirect('/admin/dashboard');

        $this->get('/admin/dashboard')->assertOk();
    }

    public function test_care_head_can_open_health_page(): void
    {
        $care = User::factory()->create([
            'email' => 'suad@tripolizoo.ly',
            'password' => '12345678',
            'role' => 'رئيس قسم الرعاية والتغذية',
            'status' => 'active',
        ]);

        $this->actingAs($care)
            ->get('/care/health')
            ->assertOk();
    }
}
