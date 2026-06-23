<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_gps_calibration_points', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('pixel_x', 8, 7);
            $table->decimal('pixel_y', 8, 7);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('map_gps_settings', function (Blueprint $table) {
            $table->id();
            $table->json('boundary_polygon');
            $table->timestamps();
        });

        $bounds = [
            'north' => 32.8901,
            'south' => 32.8850,
            'west' => 13.1721,
            'east' => 13.1789,
        ];

        DB::table('map_gps_settings')->insert([
            'boundary_polygon' => json_encode([
                ['lat' => $bounds['north'], 'lng' => $bounds['west']],
                ['lat' => $bounds['north'], 'lng' => $bounds['east']],
                ['lat' => $bounds['south'], 'lng' => $bounds['east']],
                ['lat' => $bounds['south'], 'lng' => $bounds['west']],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $points = [
            ['شمال غرب', $bounds['north'], $bounds['west'], 0.04, 0.04],
            ['شمال شرق', $bounds['north'], $bounds['east'], 0.96, 0.04],
            ['جنوب شرق', $bounds['south'], $bounds['east'], 0.96, 0.96],
            ['جنوب غرب', $bounds['south'], $bounds['west'], 0.04, 0.96],
            ['الوسط', ($bounds['north'] + $bounds['south']) / 2, ($bounds['west'] + $bounds['east']) / 2, 0.50, 0.50],
            ['البوابة', $bounds['south'] + 0.001, ($bounds['west'] + $bounds['east']) / 2, 0.50, 0.92],
        ];

        foreach ($points as $index => [$label, $lat, $lng, $x, $y]) {
            DB::table('map_gps_calibration_points')->insert([
                'label' => $label,
                'latitude' => $lat,
                'longitude' => $lng,
                'pixel_x' => $x,
                'pixel_y' => $y,
                'sort_order' => $index,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('map_gps_calibration_points');
        Schema::dropIfExists('map_gps_settings');
    }
};
