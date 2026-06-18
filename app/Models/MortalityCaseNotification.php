<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MortalityCaseNotification extends Model
{
    protected $fillable = [
        'user_id',
        'mortality_case_id',
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

    public function mortalityCase(): BelongsTo
    {
        return $this->belongsTo(MortalityCase::class);
    }
}
