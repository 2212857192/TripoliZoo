<?php

namespace Tests\Feature;

use App\Enums\AnimalStatus;
use App\Enums\UserRole;
use App\Models\AdminActivityLog;
use App\Models\Animal;
use App\Models\AnimalProfile;
use App\Models\MapLocation;
use App\Models\TicketSale;
use App\Models\TicketType;
use App\Models\User;
use App\Models\VisitSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_dynamic_stats_and_activities(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
            'name' => 'مدير النظام',
        ]);

        User::factory()->create([
            'role' => UserRole::Director->value,
            'status' => 'active',
        ]);

        User::factory()->create([
            'role' => UserRole::Director->value,
            'status' => 'inactive',
        ]);

        $activeTicket = TicketType::create([
            'name' => 'بالغ',
            'price' => 10,
            'target_description' => 'زائر بالغ',
            'visitor_nationality' => 'ليبي',
            'visitor_age_group' => 'adult',
            'is_active' => true,
        ]);

        TicketType::create([
            'name' => 'قديم',
            'price' => 5,
            'target_description' => 'نوع معطل',
            'visitor_nationality' => 'ليبي',
            'visitor_age_group' => 'child',
            'is_active' => false,
        ]);

        $animal = Animal::withoutGlobalScopes()->create([
            'code' => 'ADM-DASH-01',
            'name' => 'ليو',
            'species' => 'أسد',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'source' => 'records',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        Animal::withoutGlobalScopes()->create([
            'code' => 'ADM-DASH-02',
            'species' => 'غزال',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'source' => 'records',
            'status' => AnimalStatus::Active->value,
            'registered_at' => now(),
        ]);

        AnimalProfile::create([
            'animal_id' => $animal->id,
            'description' => 'محتوى تعريفي للأسد يظهر للزوار في تطبيق الحديقة.',
            'scientific_name' => 'Panthera leo',
            'display_code' => $animal->code,
            'image_path' => 'animal-profiles/lion.jpg',
            'is_visible' => true,
            'created_by' => $admin->id,
        ]);

        MapLocation::create([
            'name' => 'مملكة الأسود',
            'category' => 'حيوانات',
            'latitude' => 32.85,
            'longitude' => 13.18,
            'description' => 'منطقة الأسود',
            'is_active' => true,
        ]);

        MapLocation::create([
            'name' => 'موقع معطل',
            'category' => 'خدمات',
            'latitude' => 32.86,
            'longitude' => 13.19,
            'description' => 'موقع غير نشط',
            'is_active' => false,
        ]);

        TicketSale::create([
            'ticket_number' => 'TKT-001',
            'ticket_type_id' => $activeTicket->id,
            'customer_name' => 'زائر',
            'quantity' => 2,
            'unit_price' => 10,
            'total_amount' => 20,
            'payment_method' => 'cash',
            'sold_by' => $admin->id,
            'sold_at' => now(),
        ]);

        VisitSetting::current()->update([
            'status_text' => 'مفتوحة اليوم',
            'status_visible' => true,
        ]);

        AdminActivityLog::create([
            'user_id' => $admin->id,
            'entity_type' => 'animal_profile',
            'entity_id' => 1,
            'action' => 'created',
            'summary' => 'إضافة محتوى تعريفي: ليو (أسد)',
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('2 حساب مسجل', false)
            ->assertSee('2 أنواع مسجلة', false)
            ->assertSee('1 محتوى تعريفي', false)
            ->assertSee('2 موقع مضاف', false)
            ->assertSee('تذاكر مباعة اليوم', false)
            ->assertSee('2', false)
            ->assertSee('حيوانات بلا محتوى تعريفي', false)
            ->assertSee('مدير النظام', false)
            ->assertSee('إضافة محتوى تعريفي: ليو (أسد)', false);
    }
}
