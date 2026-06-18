<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapPathEdge extends Model
{
    protected $fillable = [
        'from_node_id',
        'to_node_id',
        'distance_meters',
    ];

    protected function casts(): array
    {
        return [
            'distance_meters' => 'integer',
        ];
    }

    public function fromNode(): BelongsTo
    {
        return $this->belongsTo(MapPathNode::class, 'from_node_id');
    }

    public function toNode(): BelongsTo
    {
        return $this->belongsTo(MapPathNode::class, 'to_node_id');
    }
}
