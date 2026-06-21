<?php

namespace Tests\Feature;

use App\Models\MapLocation;
use App\Services\MapPathGraphService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorMapApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_map_locations_are_returned_with_public_map_image(): void
    {
        MapLocation::create([
            'name' => 'البوابة الرئيسية',
            'category' => 'service',
            'latitude' => 0.25,
            'longitude' => 0.40,
            'description' => 'مدخل الزوار الرئيسي',
            'is_active' => true,
        ]);

        MapLocation::create([
            'name' => 'موقع مخفي',
            'category' => 'service',
            'latitude' => 0.50,
            'longitude' => 0.50,
            'is_active' => false,
        ]);

        app(MapPathGraphService::class)->syncFromLocations();

        $this->getJson('/api/map')
            ->assertOk()
            ->assertJsonPath('data.image_url', '/map.PNG')
            ->assertJsonCount(1, 'data.locations')
            ->assertJsonPath('data.locations.0.name', 'البوابة الرئيسية')
            ->assertJsonPath('data.locations.0.x', 0.40)
            ->assertJsonPath('data.locations.0.y', 0.25)
            ->assertJsonStructure([
                'data' => [
                    'navigation' => [
                        'bounds' => ['north', 'south', 'west', 'east'],
                        'image_width',
                        'image_height',
                        'nodes',
                        'edges',
                    ],
                ],
            ]);
    }
}
