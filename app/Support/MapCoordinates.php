<?php

namespace App\Support;

use App\Models\MapLocation;
use App\Services\MapPathGraphService;

class MapCoordinates
{
    public static function isNormalized(float $latitude, float $longitude): bool
    {
        return $latitude >= 0
            && $latitude <= 1
            && $longitude >= 0
            && $longitude <= 1;
    }

    /** @return array{x: float, y: float}|null */
    public static function position(MapLocation $location): ?array
    {
        return self::resolve(
            (float) $location->latitude,
            (float) $location->longitude,
        );
    }

    /** @return array{x: float, y: float}|null */
    public static function resolve(float $latitude, float $longitude): ?array
    {
        if (self::isNormalized($latitude, $longitude)) {
            return [
                'x' => $longitude,
                'y' => $latitude,
            ];
        }

        return self::gpsToNormalized($latitude, $longitude);
    }

    /** @return array{x: float, y: float}|null */
    public static function gpsToNormalized(float $latitude, float $longitude): ?array
    {
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return null;
        }

        $bounds = MapPathGraphService::GEO_BOUNDS;
        $lngSpan = $bounds['east'] - $bounds['west'];
        $latSpan = $bounds['north'] - $bounds['south'];

        if ($lngSpan <= 0 || $latSpan <= 0) {
            return null;
        }

        $x = ($longitude - $bounds['west']) / $lngSpan;
        $y = ($bounds['north'] - $latitude) / $latSpan;

        if ($x < -0.05 || $x > 1.05 || $y < -0.05 || $y > 1.05) {
            return null;
        }

        return [
            'x' => max(0.0, min(1.0, $x)),
            'y' => max(0.0, min(1.0, $y)),
        ];
    }
}
