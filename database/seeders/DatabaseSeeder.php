<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\AnimalProfile;
use App\Models\MapLocation;
use App\Models\TicketType;
use App\Models\User;
use App\Models\VisitSetting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->delete();

        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@tripolizoo.com',
            'password' => '12345678',
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
            'joined_at' => now()->subYears(2),
        ]);

        User::create([
            'name' => 'خالد المنصوري',
            'email' => 'director@tripolizoo.com',
            'password' => '12345678',
            'role' => UserRole::Director->value,
            'status' => 'active',
            'joined_at' => now()->subYears(3),
        ]);

        User::create([
            'name' => 'خالد منصور',
            'email' => 'khalid@tripolizoo.ly',
            'password' => '12345678',
            'role' => UserRole::GroupSupervisor->value,
            'status' => 'active',
            'assigned_group' => 'مجموعة الثديات الكبرى',
            'joined_at' => now()->subYear(),
        ]);

        User::create([
            'name' => 'أحمد سالم',
            'email' => 'ahmed@tripolizoo.ly',
            'password' => '12345678',
            'role' => UserRole::Veterinarian->value,
            'status' => 'active',
            'assigned_group' => 'مجموعة الثديات الكبرى',
            'joined_at' => '2022-03-15',
        ]);

        User::create([
            'name' => 'سعاد مسعود',
            'email' => 'suad@tripolizoo.ly',
            'password' => '12345678',
            'role' => UserRole::CareHead->value,
            'status' => 'active',
            'joined_at' => '2023-01-10',
        ]);

        User::create([
            'name' => 'مسؤول السجلات',
            'email' => 'records@tripolizoo.ly',
            'password' => '12345678',
            'role' => UserRole::RecordsOfficer->value,
            'status' => 'active',
            'joined_at' => now()->subMonths(6),
        ]);

        User::create([
            'name' => 'زائر تجريبي',
            'email' => 'visitor@tripolizoo.ly',
            'password' => '12345678',
            'role' => UserRole::Visitor->value,
            'status' => 'active',
            'phone' => '+218 91 000 0001',
            'joined_at' => now()->toDateString(),
        ]);

        VisitSetting::current();

        TicketType::insert([
            [
                'name' => 'تذكرة الكبار — مواطن',
                'price' => 10,
                'target_description' => 'دخول فردي',
                'visitor_nationality' => 'مواطن',
                'visitor_age_group' => 'بالغ',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تذكرة الأطفال — مواطن',
                'price' => 5,
                'target_description' => 'دخول طفل',
                'visitor_nationality' => 'مواطن',
                'visitor_age_group' => 'طفل',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تذكرة أجنبي — بالغ',
                'price' => 25,
                'target_description' => 'دخول سياحي',
                'visitor_nationality' => 'أجنبي',
                'visitor_age_group' => 'بالغ',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $lion = Animal::create([
            'code' => 'ANM-0012',
            'name' => 'سيمبا',
            'species' => 'أسد أفريقي',
            'group' => 'القططية',
            'gender' => 'ذكر',
            'status' => 'active',
            'registered_at' => '2018-02-14',
        ]);

        $elephant = Animal::create([
            'code' => 'LRG-0120',
            'name' => null,
            'species' => 'فيل آسيوي',
            'group' => 'الثدييات الكبيرة',
            'gender' => 'أنثى',
            'status' => 'active',
            'registered_at' => now()->subYears(4),
        ]);

        Animal::create([
            'code' => 'ANM-1045',
            'name' => null,
            'species' => 'غزال الريم',
            'group' => 'الغزلان',
            'gender' => 'ذكر',
            'status' => 'active',
            'registered_at' => '2026-06-07',
        ]);

        $lionProfile = AnimalProfile::create([
            'animal_id' => $lion->id,
            'description' => 'الأسد الإفريقي من أكبر القطط البرية في العالم. يعيش في مجموعات تُعرف بـ (الفخر). يتميز الذكر بعُرفه الكثيف الذي يزداد قتامةً مع التقدم في السن.',
            'scientific_name' => 'Panthera leo',
            'display_code' => 'L-01',
            'image_path' => null,
            'is_visible' => true,
        ]);

        AnimalProfile::create([
            'animal_id' => $elephant->id,
            'description' => 'الفيل الآسيوي أصغر حجماً من الأفريقي، ويتميز بأذنين أصغر ورأس أكثر تحدباً. يُعدّ من أكثر الحيوانات ذكاءً في العالم.',
            'scientific_name' => 'Elephas maximus',
            'display_code' => 'E-04',
            'image_path' => null,
            'is_visible' => true,
        ]);

        MapLocation::create([
            'name' => 'قفص الأسد الأفريقي',
            'category' => 'enclosure',
            'latitude' => 32.8492,
            'longitude' => 13.1782,
            'description' => 'يقع في المنطقة الشمالية للحديقة.',
            'animal_profile_id' => $lionProfile->id,
            'is_active' => true,
        ]);

        MapLocation::create([
            'name' => 'بوابة الدخول الرئيسية',
            'category' => 'service',
            'latitude' => 32.8485,
            'longitude' => 13.1785,
            'description' => 'نقطة الدخول والتذاكر',
            'is_active' => true,
        ]);
    }
}
