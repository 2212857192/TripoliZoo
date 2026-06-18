<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_portal_user_can_change_password_via_web_route(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
            'role' => UserRole::CareHead->value,
            'status' => 'active',
        ]);

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
            ->actingAs($user)
            ->postJson(route('account.password.update'), [
            'current_password' => 'oldpassword',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'تم تغيير كلمة المرور بنجاح.');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword1', $user->password));
    }

    public function test_web_change_password_rejects_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
            'role' => UserRole::VetHead->value,
            'status' => 'active',
        ]);

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
            ->actingAs($user)
            ->postJson(route('account.password.update'), [
                'current_password' => 'wrongpassword',
                'password' => 'newpassword1',
                'password_confirmation' => 'newpassword1',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password']);
    }
}
