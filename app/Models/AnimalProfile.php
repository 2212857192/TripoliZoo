<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class AnimalProfile extends Model
{
    protected $fillable = [
        'animal_id',
        'description',
        'image_path',
        'scientific_name',
        'display_code',
        'is_visible',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mapLocations(): HasMany
    {
        return $this->hasMany(MapLocation::class);
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    public function qrPayload(): array
    {
        return [
            'profile_id' => $this->id,
            'animal_code' => $this->animal?->code,
            'name' => $this->animal?->species,
            'sci' => $this->scientific_name,
            'zoo' => 'حديقة حيوان طرابلس',
        ];
    }
}
