<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapGpsCalibrationPoint extends Model
{
    protected $fillable = [
        'label',
        'latitude',
        'longitude',
        'pixel_x',
        'pixel_y',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'pixel_x' => 'float',
        'pixel_y' => 'float',
        'is_active' => 'bool',
    ];
}
