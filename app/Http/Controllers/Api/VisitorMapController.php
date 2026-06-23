<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MapLocation;
use App\Services\MapGpsCalibrationService;
use App\Services\MapPathGraphService;
use App\Support\MapCoordinates;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class VisitorMapController extends Controller
{
    public function __construct(
        private readonly MapPathGraphService $pathGraphService,
        private readonly MapGpsCalibrationService $calibrationService,
    ) {}

    public function show(): JsonResponse
    {
        return response()->json([
            'data' => $this->activeMapPayload(),
        ]);
    }

    public function active(): JsonResponse
    {
        return response()->json($this->activeMapPayload());
    }

    /**
     * @return array<string, mixed>
     */
    private function activeMapPayload(): array
    {
        $locations = MapLocation::query()
            ->with('animalProfile.animal')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('animal_profile_id')
                    ->orWhereHas('animalProfile', fn ($profile) => $profile->listed()->where('is_visible', true));
            })
            ->orderBy('name')
            ->get();

        $navigation = $this->pathGraphService->navigationPayload();
        $transformedLocations = $this->transformLocations($locations, $navigation['nodes']);

        return [
            'map_id' => 1,
            'image_url' => '/map.PNG',
            'image_width' => $navigation['image_width'],
            'image_height' => $navigation['image_height'],
            'locations' => $transformedLocations,
            'nodes' => $navigation['nodes'],
            'edges' => $navigation['edges'],
            'navigation' => $navigation,
            'calibration' => $this->calibrationService->payload(),
        ];
    }

    /**
     * @param  Collection<int, MapLocation>  $locations
     * @param  list<array<string, mixed>>  $nodes
     * @return list<array<string, mixed>>
     */
    private function transformLocations(Collection $locations, array $nodes): array
    {
        return $locations
            ->map(function (MapLocation $location) use ($nodes) {
                $position = MapCoordinates::position($location);
                $animal = $location->animalProfile?->animal;

                if ($position === null) {
                    return null;
                }

                $nearest = $this->nearestNodeForPosition(
                    $nodes,
                    $position['x'],
                    $position['y'],
                    $location->id,
                );

                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'category' => $location->category,
                    'location_type' => $location->category,
                    'description' => $location->description ?: $animal?->displayLabel(),
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'x' => $position['x'],
                    'y' => $position['y'],
                    'nearest_node_id' => $nearest['id'] ?? null,
                    'nearest_node_key' => $nearest['node_key'] ?? null,
                    'animal_profile_id' => $location->animal_profile_id,
                    'animal_name' => $animal?->name ?: $animal?->species,
                    'animal_code' => $animal?->code,
                    'animal_group' => $animal?->group,
                    'animal_photo_url' => $location->animalProfile?->imageUrl() ?? $animal?->displayPhotoUrl(),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  list<array<string, mixed>>  $nodes
     * @return array{id: int, node_key: string|null}|null
     */
    private function nearestNodeForPosition(
        array $nodes,
        float $x,
        float $y,
        ?int $excludeLocationId = null,
    ): ?array {
        $best = null;
        $bestDistance = PHP_FLOAT_MAX;

        foreach ($nodes as $node) {
            if ($excludeLocationId !== null
                && ($node['map_location_id'] ?? null) === $excludeLocationId) {
                continue;
            }

            $dx = ((float) $node['x']) - $x;
            $dy = ((float) $node['y']) - $y;
            $distance = ($dx * $dx) + ($dy * $dy);
            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $best = [
                    'id' => (int) $node['id'],
                    'node_key' => $node['node_key'] ?? null,
                ];
            }
        }

        return $best;
    }
}
