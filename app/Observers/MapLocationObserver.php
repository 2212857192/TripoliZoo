<?php

namespace App\Observers;

use App\Models\MapLocation;
use App\Services\MapPathGraphService;

class MapLocationObserver
{
    public function __construct(private readonly MapPathGraphService $pathGraphService) {}

    public function saved(MapLocation $mapLocation): void
    {
        $this->pathGraphService->syncFromLocations();
    }

    public function deleted(MapLocation $mapLocation): void
    {
        $this->pathGraphService->syncFromLocations();
    }
}
