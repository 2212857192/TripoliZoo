<?php

namespace App\Models;

use App\Support\MapCoordinates;
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

    public function hasNormalizedCoordinates(): bool
    {
        return MapCoordinates::isNormalized(
            (float) $this->latitude,
            (float) $this->longitude,
        );
    }

    /** @return array{x: float, y: float}|null */
    public function mapPosition(): ?array
    {
        return MapCoordinates::position($this);
    }
}
