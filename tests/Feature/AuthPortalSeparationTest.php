<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthPortalSeparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_director_can_login_to_web_and_access_dashboard(): void
    {
        $director = User::factory()->create([
            'role' => UserRole::Director->value,
            'status' => 'active',
            'password' => 'password123',
        ]);

        $this->post('/login', [
            'email' => $director->email,
            'password' => 'password123',
        ])->assertRedirect('/director/dashboard');

        $this->assertAuthenticatedAs($director);

        $this->get('/director/dashboard')->assertOk();
    }

    public function test_admin_can_create_director_employee(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.employees.store'), [
                'form_context' => 'add',
                'name' => 'مدير الحديقة الجديد',
                'email' => 'new-director@tripolizoo.ly',
                'role' => UserRole::Director->value,
                'status' => 'active',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $director = User::query()->where('email', 'new-director@tripolizoo.ly')->first();

        $this->assertNotNull($director);
        $this->assertSame(UserRole::Director->value, $director->role);
        $this->assertTrue($director->isEmployeeAccount());
        $this->assertTrue($director->canUseWebPortal());
        $this->assertSame('/director/dashboard', $director->homePath());
    }

    public function test_vet_head_can_login_to_web_but_not_mobile_app(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
            'password' => 'password123',
        ]);

        $this->post('/login', [
            'email' => $vetHead->email,
            'password' => 'password123',
        ])->assertRedirect('/vet/dashboard');

        $this->assertAuthenticatedAs($vetHead);

        $this->postJson('/api/auth/login', [
            'email' => $vetHead->email,
            'password' => 'password123',
        ])->assertForbidden()
            ->assertJsonPath('message', 'هذا الحساب مخصص للوحة الويب. استخدم موقع الإدارة لتسجيل الدخول.');
    }

    public function test_veterinarian_can_login_to_mobile_app_but_not_web(): void
    {
        $vet = User::factory()->create([
            'role' => UserRole::Veterinarian->value,
            'assigned_group' => 'الغزلان',
            'status' => 'active',
            'password' => 'password123',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $vet->email,
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonPath('user.role', 'doctor');

        $this->post('/login', [
            'email' => $vet->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_veterinarian_cannot_access_doctor_api_as_vet_head_role(): void
    {
        $vetHead = User::factory()->create([
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        Sanctum::actingAs($vetHead);

        $this->getJson('/api/auth/doctor/dashboard')
            ->assertForbidden();
    }
}
