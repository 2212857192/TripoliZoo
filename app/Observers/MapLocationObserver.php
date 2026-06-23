<?php

namespace App\Observers;

use App\Models\MapLocation;
use App\Models\MapPathNode;
use App\Services\MapPathGraphService;

class MapLocationObserver
{
    public function __construct(private readonly MapPathGraphService $pathGraphService) {}

    public function saved(MapLocation $mapLocation): void
    {
        // Only re-link this location to the nearest pathway node.
        // Never rebuild the whole graph — that destroys GeoJSON routing data.
        $this->pathGraphService->linkLocationToNearestNode($mapLocation);
    }

    public function deleted(MapLocation $mapLocation): void
    {
        MapPathNode::query()
            ->where('map_location_id', $mapLocation->id)
            ->update(['map_location_id' => null]);
    }
}
