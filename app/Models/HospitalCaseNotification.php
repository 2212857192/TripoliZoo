<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalCaseNotification extends Model
{
    protected $fillable = [
        'user_id',
        'hospital_case_id',
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

    public function hospitalCase(): BelongsTo
    {
        return $this->belongsTo(HospitalCase::class);
    }
}
