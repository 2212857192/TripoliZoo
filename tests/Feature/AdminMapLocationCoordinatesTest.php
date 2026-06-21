<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\MapLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMapLocationCoordinatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_saved_coordinates_are_returned_by_visitor_map_api(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.map-locations.store'), [
                'name' => 'مطعم الحديقة',
                'category' => 'dining',
                'latitude' => '0.3120000',
                'longitude' => '0.4680000',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.map-locations.index'));

        $location = MapLocation::firstOrFail();

        $this->assertDatabaseHas('map_locations', [
            'id' => $location->id,
            'latitude' => '0.3120000',
            'longitude' => '0.4680000',
        ]);

        $this->getJson('/api/map')
            ->assertOk()
            ->assertJsonPath('data.locations.0.id', $location->id)
            ->assertJsonPath('data.locations.0.x', 0.468)
            ->assertJsonPath('data.locations.0.y', 0.312);
    }

    public function test_updated_coordinates_are_returned_by_visitor_map_api(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SystemAdmin->value,
            'status' => 'active',
        ]);

        $location = MapLocation::create([
            'name' => 'دورة المياه',
            'category' => 'service',
            'latitude' => 0.20,
            'longitude' => 0.30,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.map-locations.update', $location), [
                'name' => 'دورة المياه',
                'category' => 'service',
                'latitude' => '0.6500000',
                'longitude' => '0.7200000',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.map-locations.index'));

        $this->getJson('/api/map')
            ->assertOk()
            ->assertJsonPath('data.locations.0.id', $location->id)
            ->assertJsonPath('data.locations.0.x', 0.72)
            ->assertJsonPath('data.locations.0.y', 0.65);
    }
}
