<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuarantineVaccine extends Model
{
    protected $fillable = [
        'quarantine_id',
        'user_id',
        'name',
        'administered_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'administered_at' => 'date',
        ];
    }

    public function quarantine(): BelongsTo
    {
        return $this->belongsTo(Quarantine::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
