<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Animal extends Model
{
    protected $fillable = [
        'code',
        'name',
        'species',
        'group',
        'gender',
        'distinguishing_marks',
        'photo_path',
        'age_method',
        'birth_date',
        'approx_age_value',
        'approx_age_unit',
        'origin',
        'source',
        'prior_history',
        'prior_history_file',
        'status',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'registered_at' => 'date',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(AnimalProfile::class);
    }

    public function displayLabel(): string
    {
        return $this->name
            ? "{$this->name} ({$this->species})"
            : $this->species;
    }
}
