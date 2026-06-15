<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapLocation extends Model
{
    protected $fillable = [
        'name',
        'category',
        'latitude',
        'longitude',
        'description',
        'animal_profile_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    public function animalProfile(): BelongsTo
    {
        return $this->belongsTo(AnimalProfile::class);
    }
}
