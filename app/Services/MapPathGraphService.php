<?php

namespace App\Services;

use App\Models\MapLocation;
use App\Models\MapPathEdge;
use App\Models\MapPathNode;
use Illuminate\Support\Collection;

class MapPathGraphService
{
    public const MAP_IMAGE_WIDTH = 4516;

    public const MAP_IMAGE_HEIGHT = 3374;

    /** @var array{north: float, south: float, west: float, east: float} */
    public const GEO_BOUNDS = [
        'north' => 32.8901,
        'south' => 32.8850,
        'west' => 13.1721,
        'east' => 13.1789,
    ];

    public function syncFromLocations(): void
    {
        $locations = MapLocation::query()->where('is_active', true)->get();
        $normalized = $this->normalizedLocations($locations);

        MapPathEdge::query()->delete();
        MapPathNode::query()->delete();

        $entrance = MapPathNode::create([
            'name' => 'بوابة الدخول',
            'x' => 0.5,
            'y' => 0.92,
            'is_active' => true,
        ]);

        $hub = MapPathNode::create([
            'name' => 'المفترق الرئيسي',
            'x' => $this->average($normalized->pluck('x')->all(), 0.5),
            'y' => $this->average($normalized->pluck('y')->all(), 0.55),
            'is_active' => true,
        ]);

        $this->connect($entrance->id, $hub->id, $this->estimateMeters($entrance, $hub));

        $locationNodes = [];

        foreach ($normalized as $item) {
            /** @var MapLocation $location */
            $location = $item['location'];

            $node = MapPathNode::create([
                'name' => $location->name,
                'x' => $item['x'],
                'y' => $item['y'],
                'map_location_id' => $location->id,
                'is_active' => true,
            ]);

            $locationNodes[$location->id] = $node;
            $this->connect($hub->id, $node->id, $this->estimateMeters($hub, $node));
        }

        $serviceEntrance = $locations->first(
            fn (MapLocation $location) => $location->category === 'service'
                && str_contains($location->name, 'بوابة')
        );

        if ($serviceEntrance && isset($locationNodes[$serviceEntrance->id])) {
            $gateNode = $locationNodes[$serviceEntrance->id];
            $this->connect($entrance->id, $gateNode->id, $this->estimateMeters($entrance, $gateNode));
        }
    }

    /**
     * @return array{
     *     bounds: array{north: float, south: float, west: float, east: float},
     *     image_width: int,
     *     image_height: int,
     *     nodes: list<array<string, mixed>>,
     *     edges: list<array<string, mixed>>
     * }
     */
    public function navigationPayload(): array
    {
        if (! MapPathNode::query()->where('is_active', true)->exists()) {
            $this->syncFromLocations();
        }

        $nodes = MapPathNode::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn (MapPathNode $node) => [
                'id' => $node->id,
                'name' => $node->name,
                'x' => (float) $node->x,
                'y' => (float) $node->y,
                'map_location_id' => $node->map_location_id,
            ])
            ->values()
            ->all();

        $edges = MapPathEdge::query()
            ->get()
            ->flatMap(function (MapPathEdge $edge) {
                return [
                    [
                        'from' => $edge->from_node_id,
                        'to' => $edge->to_node_id,
                        'distance' => $edge->distance_meters,
                    ],
                    [
                        'from' => $edge->to_node_id,
                        'to' => $edge->from_node_id,
                        'distance' => $edge->distance_meters,
                    ],
                ];
            })
            ->unique(fn (array $edge) => $edge['from'].'-'.$edge['to'])
            ->values()
            ->all();

        return [
            'bounds' => self::GEO_BOUNDS,
            'image_width' => self::MAP_IMAGE_WIDTH,
            'image_height' => self::MAP_IMAGE_HEIGHT,
            'nodes' => $nodes,
            'edges' => $edges,
        ];
    }

    /**
     * @param Collection<int, MapLocation> $locations
     * @return Collection<int, array{location: MapLocation, x: float, y: float}>
     */
    private function normalizedLocations(Collection $locations): Collection
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

        return $locations->map(function (MapLocation $location) use (
            $useNormalizedValues,
            $minLat,
            $maxLat,
            $minLng,
            $maxLng
        ) {
            $lat = (float) $location->latitude;
            $lng = (float) $location->longitude;

            return [
                'location' => $location,
                'x' => $useNormalizedValues
                    ? $lng
                    : $this->normalize($lng, $minLng, $maxLng),
                'y' => $useNormalizedValues
                    ? $lat
                    : 1 - $this->normalize($lat, $minLat, $maxLat),
            ];
        });
    }

    private function normalize(float $value, float $min, float $max): float
    {
        if ($max <= $min) {
            return 0.5;
        }

        return max(0.04, min(0.96, ($value - $min) / ($max - $min)));
    }

    /**
     * @param list<float> $values
     */
    private function average(array $values, float $fallback): float
    {
        if ($values === []) {
            return $fallback;
        }

        return array_sum($values) / count($values);
    }

    private function estimateMeters(MapPathNode $from, MapPathNode $to): int
    {
        $dx = ((float) $from->x - (float) $to->x) * self::MAP_IMAGE_WIDTH;
        $dy = ((float) $from->y - (float) $to->y) * self::MAP_IMAGE_HEIGHT;
        $pixels = sqrt(($dx * $dx) + ($dy * $dy));

        return max(15, (int) round($pixels * 0.35));
    }

    private function connect(int $fromNodeId, int $toNodeId, int $distanceMeters): void
    {
        if ($fromNodeId === $toNodeId) {
            return;
        }

        $pair = [
            'from_node_id' => min($fromNodeId, $toNodeId),
            'to_node_id' => max($fromNodeId, $toNodeId),
        ];

        MapPathEdge::updateOrCreate(
            $pair,
            ['distance_meters' => $distanceMeters]
        );
    }
}
