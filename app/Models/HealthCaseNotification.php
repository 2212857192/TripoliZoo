<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthCaseNotification extends Model
{
    protected $fillable = [
        'user_id',
        'health_case_id',
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

    public function healthCase(): BelongsTo
    {
        return $this->belongsTo(HealthCase::class);
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }
}
