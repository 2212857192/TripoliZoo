<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\AnimalProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorAnimalWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_visible_animal_profile_page_is_shown_for_qr_scan(): void
    {
        $animal = Animal::create([
            'code' => 'C-100',
            'name' => 'ليو',
            'species' => 'الأسد الأفريقي',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'status' => 'active',
            'origin' => 'أفريقيا',
        ]);

        $profile = AnimalProfile::create([
            'animal_id' => $animal->id,
            'description' => 'محتوى تعريفي منشور للزائر عن الأسد الأفريقي في حديقة طرابلس.',
            'display_code' => $animal->code,
            'is_visible' => true,
        ]);

        $this->get(route('visitor.animal', $profile))
            ->assertOk()
            ->assertSee('ليو', false)
            ->assertSee('الأسد الأفريقي', false)
            ->assertSee('محتوى تعريفي منشور للزائر', false)
            ->assertSee('C-100', false)
            ->assertSee('أفريقيا', false);
    }

    public function test_hidden_animal_profile_page_returns_not_found(): void
    {
        $animal = Animal::create([
            'code' => 'C-101',
            'species' => 'زرافة',
            'group' => 'الثدييات الكبيرة',
            'gender' => 'أنثى',
            'status' => 'active',
        ]);

        $profile = AnimalProfile::create([
            'animal_id' => $animal->id,
            'description' => 'محتوى مخفي لا يجب أن يظهر للزوار عند مسح الرمز.',
            'display_code' => $animal->code,
            'is_visible' => false,
        ]);

        $this->get(route('visitor.animal', $profile))->assertNotFound();
    }

    public function test_animal_profile_exposes_absolute_qr_url(): void
    {
        $animal = Animal::create([
            'code' => 'C-102',
            'name' => 'فهد',
            'species' => 'فهد',
            'group' => 'القططية',
            'gender' => 'أنثى',
            'status' => 'active',
        ]);

        $profile = AnimalProfile::create([
            'animal_id' => $animal->id,
            'description' => 'محتوى تعريفي للفهد يظهر عند مسح رمز الاستجابة السريعة.',
            'display_code' => $animal->code,
            'is_visible' => true,
        ]);

        $url = $profile->visitorQrUrl();

        $this->assertStringContainsString('/app/animals/'.$profile->id, $url);
        $this->assertStringStartsWith('http', $url);
    }

    public function test_visitor_qr_url_uses_request_host_when_admin_opened_via_lan_ip(): void
    {
        config([
            'app.url' => 'http://127.0.0.1:8000',
            'app.visitor_public_url' => null,
        ]);

        $animal = Animal::create([
            'code' => 'C-104',
            'name' => 'نسر',
            'species' => 'نسر',
            'group' => 'الطيور',
            'gender' => 'ذكر',
            'status' => 'active',
        ]);

        $profile = AnimalProfile::create([
            'animal_id' => $animal->id,
            'description' => 'محتوى تعريفي للنسر يظهر عند مسح رمز الاستجابة السريعة.',
            'display_code' => $animal->code,
            'is_visible' => true,
        ]);

        $request = \Illuminate\Http\Request::create('http://192.168.0.15:8000/admin/animals', 'GET');
        $this->app->instance('request', $request);
        \Illuminate\Support\Facades\Request::swap($request);

        $this->assertSame(
            'http://192.168.0.15:8000/app/animals/'.$profile->id,
            $profile->visitorQrUrl(),
        );
    }

    public function test_admin_can_fetch_animal_qr_image(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $animal = Animal::create([
            'code' => 'C-103',
            'name' => 'زرافة',
            'species' => 'زرافة',
            'group' => 'الثدييات الكبيرة',
            'gender' => 'أنثى',
            'status' => 'active',
        ]);

        $profile = AnimalProfile::create([
            'animal_id' => $animal->id,
            'description' => 'محتوى تعريفي للزرافة يظهر عند مسح رمز الاستجابة السريعة.',
            'display_code' => $animal->code,
            'is_visible' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.animals.qr', $profile))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml; charset=UTF-8')
            ->assertSee('<svg', false);
    }

    public function test_qr_image_uses_browser_origin_when_provided(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $animal = Animal::create([
            'code' => 'C-105',
            'name' => 'فيل',
            'species' => 'فيل أفريقي',
            'group' => 'الثدييات الكبيرة',
            'gender' => 'ذكر',
            'status' => 'active',
        ]);

        $profile = AnimalProfile::create([
            'animal_id' => $animal->id,
            'description' => 'محتوى تعريفي للفيل يظهر عند مسح رمز الاستجابة السريعة من الموبايل.',
            'display_code' => $animal->code,
            'is_visible' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.animals.qr', $profile).'?origin='.urlencode('http://192.168.7.3:8000'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml; charset=UTF-8')
            ->assertSee('<svg', false);
    }
}
