<?php

use App\Models\MapLocation;
use App\Support\MapCoordinates;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        MapLocation::withoutEvents(function (): void {
            MapLocation::query()->each(function (MapLocation $location): void {
                $latitude = (float) $location->latitude;
                $longitude = (float) $location->longitude;

                if (MapCoordinates::isNormalized($latitude, $longitude)) {
                    return;
                }

                $position = MapCoordinates::gpsToNormalized($latitude, $longitude);

                if ($position === null) {
                    return;
                }

                $location->update([
                    'latitude' => $position['y'],
                    'longitude' => $position['x'],
                ]);
            });
        });

        app(\App\Services\MapPathGraphService::class)->syncFromLocations();
    }

    public function down(): void
    {
        // Irreversible: legacy GPS values cannot be restored reliably.
    }
};
