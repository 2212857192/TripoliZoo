<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BirthRegistration extends Model
{
    protected $fillable = [
        'registration_number',
        'mother_id',
        'supervisor_id',
        'group',
        'birth_date',
        'birth_count',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'mother_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function newborns(): HasMany
    {
        return $this->hasMany(Animal::class, 'birth_registration_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(BirthRegistrationNotification::class);
    }
}
