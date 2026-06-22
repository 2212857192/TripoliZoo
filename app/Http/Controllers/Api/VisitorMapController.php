<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MapLocation;
use App\Services\MapPathGraphService;
use App\Support\MapCoordinates;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class VisitorMapController extends Controller
{
    public function __construct(private readonly MapPathGraphService $pathGraphService) {}

    public function show(): JsonResponse
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

        return response()->json([
            'data' => [
                'image_url' => '/map.PNG',
                'locations' => $this->transformLocations($locations),
                'navigation' => $this->pathGraphService->navigationPayload(),
            ],
        ]);
    }

    /**
     * @param  Collection<int, MapLocation>  $locations
     * @return list<array<string, mixed>>
     */
    private function transformLocations(Collection $locations): array
    {
        return $locations
            ->map(function (MapLocation $location) {
                $position = MapCoordinates::position($location);
                $animal = $location->animalProfile?->animal;

                if ($position === null) {
                    return null;
                }

                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'category' => $location->category,
                    'description' => $location->description ?: $animal?->displayLabel(),
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'x' => $position['x'],
                    'y' => $position['y'],
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
}
