<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareNotification extends Model
{
    protected $fillable = [
        'user_id',
        'receiving_task_id',
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

    public function receivingTask(): BelongsTo
    {
        return $this->belongsTo(ReceivingTask::class);
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }
}
