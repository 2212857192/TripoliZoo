<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthRegistrationNotification extends Model
{
    protected $fillable = [
        'user_id',
        'birth_registration_id',
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

    public function birthRegistration(): BelongsTo
    {
        return $this->belongsTo(BirthRegistration::class);
    }
}
