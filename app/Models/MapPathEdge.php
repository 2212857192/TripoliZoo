<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapPathEdge extends Model
{
    protected $fillable = [
        'edge_key',
        'from_node_id',
        'to_node_id',
        'distance_meters',
        'geometry',
        'is_active',
        'is_accessible',
    ];

    protected function casts(): array
    {
        return [
            'distance_meters' => 'integer',
            'geometry' => 'array',
            'is_active' => 'bool',
            'is_accessible' => 'bool',
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
