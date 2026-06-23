<?php

namespace App\Services;

use App\Models\MapGpsCalibrationPoint;
use Illuminate\Support\Facades\DB;

class MapGpsCalibrationService
{
    /**
     * @return array{
     *     points: list<array{lat: float, lng: float, pixel_x: float, pixel_y: float, label: string|null}>,
     *     boundary_polygon: list<array{lat: float, lng: float}>
     * }
     */
    public function payload(): array
    {
        $points = MapGpsCalibrationPoint::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (MapGpsCalibrationPoint $point) => [
                'lat' => (float) $point->latitude,
                'lng' => (float) $point->longitude,
                'pixel_x' => (float) $point->pixel_x,
                'pixel_y' => (float) $point->pixel_y,
                'label' => $point->label,
            ])
            ->values()
            ->all();

        $settings = DB::table('map_gps_settings')->orderBy('id')->first();
        $boundary = $settings
            ? json_decode($settings->boundary_polygon ?? '[]', true)
            : [];

        return [
            'points' => $points,
            'boundary_polygon' => is_array($boundary) ? $boundary : [],
        ];
    }
}
