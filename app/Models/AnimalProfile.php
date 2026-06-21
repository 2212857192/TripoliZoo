<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\PublicUrl;

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

    /**
     * محتوى تعريفي لحيوان مسجّل رسمياً داخل الحديقة.
     */
    public function scopeListed(Builder $query): Builder
    {
        return $query->whereHas('animal', fn (Builder $animalQuery) => $animalQuery->insideZooOfficially());
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return static::listed()
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->first();
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
        if (! $this->image_path) {
            return null;
        }

        return asset('storage/'.ltrim($this->image_path, '/'));
    }

    public function qrPayload(): array
    {
        return [
            'profile_id' => $this->id,
            'animal_code' => $this->animal?->code,
            'name' => $this->visitorDisplayName(),
            'sci' => $this->visitorSubtitle(),
            'zoo' => 'حديقة حيوان طرابلس',
        ];
    }

    public function visitorDisplayName(): string
    {
        return $this->animal?->name ?: $this->animal?->species ?: 'حيوان';
    }

    /** النص الذي يظهر تحت اسم الحيوان في تطبيق الزائر — يُجلب من بيانات الحيوان المختار. */
    public function visitorSubtitle(): string
    {
        $animal = $this->animal;

        if (! $animal) {
            return '';
        }

        if ($animal->name) {
            return $animal->species ?? '';
        }

        return $animal->group ?? '';
    }

    public function visitorQrUrl(): string
    {
        return PublicUrl::absolute(route('visitor.animal', $this, absolute: false));
    }

    public function qrScanPayload(): string
    {
        return $this->visitorQrUrl();
    }
}
