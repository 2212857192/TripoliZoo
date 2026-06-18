<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalNoteNotification extends Model
{
    protected $fillable = [
        'user_id',
        'operational_note_id',
        'title',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function operationalNote(): BelongsTo
    {
        return $this->belongsTo(OperationalNote::class);
    }
}
