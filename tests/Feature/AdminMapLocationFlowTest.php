<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\MapLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMapLocationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_map_locations(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.map-locations.store'), [
                'name' => 'البوابة الرئيسية',
                'category' => 'service',
                'latitude' => '0.2500000',
                'longitude' => '0.4000000',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.map-locations.index'));

        $location = MapLocation::firstOrFail();

        $this->assertSame('البوابة الرئيسية', $location->name);
        $this->assertTrue($location->is_active);

        $this->actingAs($admin)
            ->put(route('admin.map-locations.update', $location), [
                'name' => 'بوابة الزوار',
                'category' => 'service',
                'latitude' => '0.3000000',
                'longitude' => '0.4500000',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.map-locations.index'));

        $this->assertDatabaseHas('map_locations', [
            'id' => $location->id,
            'name' => 'بوابة الزوار',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.map-locations.toggle', $location))
            ->assertRedirect();

        $this->assertFalse($location->fresh()->is_active);

        $this->actingAs($admin)
            ->delete(route('admin.map-locations.destroy', $location))
            ->assertRedirect(route('admin.map-locations.index'));

        $this->assertDatabaseMissing('map_locations', ['id' => $location->id]);
    }
}
