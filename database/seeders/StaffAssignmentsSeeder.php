<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Quarantine;
use App\Models\User;
use App\Services\QuarantineNotificationService;
use Illuminate\Database\Seeder;

class StaffAssignmentsSeeder extends Seeder
{
    public function run(): void
    {
        User::query()
            ->where('assigned_group', 'مجموعة الثديات الكبرى')
            ->update(['assigned_group' => 'الثدييات الكبيرة']);

        User::updateOrCreate(
            ['email' => 'ahmed@tripolizoo.ly'],
            [
                'name' => 'أحمد سالم',
                'password' => '12345678',
                'role' => UserRole::Veterinarian->value,
                'status' => 'active',
                'assigned_group' => 'الغزلان',
                'joined_at' => '2022-03-15',
            ]
        );

        User::updateOrCreate(
            ['email' => 'omar@tripolizoo.ly'],
            [
                'name' => 'عمر الفيتوري',
                'password' => '12345678',
                'role' => UserRole::Veterinarian->value,
                'status' => 'active',
                'assigned_group' => 'الثدييات الكبيرة',
                'joined_at' => now()->subMonths(8),
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@tripolizoo.ly'],
            [
                'name' => 'مشرف الغزلان',
                'password' => '12345678',
                'role' => UserRole::GroupSupervisor->value,
                'status' => 'active',
                'assigned_group' => 'الغزلان',
                'joined_at' => now()->subYear(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'khalid@tripolizoo.ly'],
            [
                'name' => 'خالد منصور',
                'password' => '12345678',
                'role' => UserRole::GroupSupervisor->value,
                'status' => 'active',
                'assigned_group' => 'الثدييات الكبيرة',
                'joined_at' => now()->subYear(),
            ]
        );

        $notifier = app(QuarantineNotificationService::class);

        foreach (Quarantine::with('animal')->get() as $quarantine) {
            $notifier->notifyGroupVets($quarantine);
        }
    }
}
