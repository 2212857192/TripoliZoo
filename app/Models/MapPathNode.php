<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MapPathNode extends Model
{
    protected $fillable = [
        'node_key',
        'name',
        'x',
        'y',
        'map_location_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'x' => 'decimal:7',
            'y' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    public function mapLocation(): BelongsTo
    {
        return $this->belongsTo(MapLocation::class);
    }

    public function outgoingEdges(): HasMany
    {
        return $this->hasMany(MapPathEdge::class, 'from_node_id');
    }

    public function incomingEdges(): HasMany
    {
        return $this->hasMany(MapPathEdge::class, 'to_node_id');
    }
}
