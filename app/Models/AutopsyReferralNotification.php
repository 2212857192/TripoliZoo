<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutopsyReferralNotification extends Model
{
    protected $fillable = [
        'user_id',
        'autopsy_referral_id',
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

    public function autopsyReferral(): BelongsTo
    {
        return $this->belongsTo(AutopsyReferral::class);
    }
}
