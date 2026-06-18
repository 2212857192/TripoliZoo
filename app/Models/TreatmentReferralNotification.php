<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentReferralNotification extends Model
{
    protected $fillable = [
        'user_id',
        'treatment_referral_id',
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

    public function treatmentReferral(): BelongsTo
    {
        return $this->belongsTo(TreatmentReferral::class);
    }
}
