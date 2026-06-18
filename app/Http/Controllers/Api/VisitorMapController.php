<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MapLocation;
use App\Services\MapPathGraphService;
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
                'image_url' => asset('map.PNG'),
                'locations' => $this->transformLocations($locations),
                'navigation' => $this->pathGraphService->navigationPayload(),
            ],
        ]);
    }

    /**
     * @param Collection<int, MapLocation> $locations
     * @return list<array<string, mixed>>
     */
    private function transformLocations(Collection $locations): array
    {
        $useNormalizedValues = $locations->every(
            fn (MapLocation $location) => (float) $location->latitude >= 0
                && (float) $location->latitude <= 1
                && (float) $location->longitude >= 0
                && (float) $location->longitude <= 1
        );

        $minLat = (float) $locations->min('latitude');
        $maxLat = (float) $locations->max('latitude');
        $minLng = (float) $locations->min('longitude');
        $maxLng = (float) $locations->max('longitude');

        return $locations
            ->map(function (MapLocation $location) use ($useNormalizedValues, $minLat, $maxLat, $minLng, $maxLng) {
                $lat = (float) $location->latitude;
                $lng = (float) $location->longitude;
                $animal = $location->animalProfile?->animal;

                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'category' => $location->category,
                    'description' => $location->description ?: $animal?->displayLabel(),
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'x' => $useNormalizedValues ? $lng : $this->normalize($lng, $minLng, $maxLng),
                    'y' => $useNormalizedValues ? $lat : 1 - $this->normalize($lat, $minLat, $maxLat),
                    'animal_profile_id' => $location->animal_profile_id,
                    'animal_name' => $animal?->name ?: $animal?->species,
                    'animal_code' => $animal?->code,
                    'animal_photo_url' => $location->animalProfile?->imageUrl() ?? $animal?->displayPhotoUrl(),
                ];
            })
            ->values()
            ->all();
    }

    private function normalize(float $value, float $min, float $max): float
    {
        if ($max <= $min) {
            return 0.5;
        }

        return max(0.04, min(0.96, ($value - $min) / ($max - $min)));
    }
}
