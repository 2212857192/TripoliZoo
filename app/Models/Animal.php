<?php

namespace App\Models;

use App\Enums\AnimalStatus;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Animal extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new ExcludeQuarantineAnimals);

        static::saving(function (Animal $animal): void {
            if ($animal->isDirty('animal_group_id')) {
                if ($animal->animal_group_id) {
                    $animal->group = AnimalGroup::query()
                        ->whereKey($animal->animal_group_id)
                        ->value('name');
                }
            } elseif ($animal->isDirty('group') && filled($animal->group)) {
                $animal->animal_group_id = AnimalGroup::query()
                    ->where('name', $animal->group)
                    ->value('id');
            }
        });
    }

    protected $fillable = [
        'code',
        'name',
        'species',
        'group',
        'animal_group_id',
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
        'mother_id',
        'birth_registration_id',
        'registration_note',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'registered_at' => 'date',
        ];
    }

    public function animalGroup(): BelongsTo
    {
        return $this->belongsTo(AnimalGroup::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(AnimalProfile::class);
    }

    public function quarantines(): HasMany
    {
        return $this->hasMany(Quarantine::class);
    }

    public function activeQuarantine(): HasOne
    {
        return $this->hasOne(Quarantine::class)->latestOfMany();
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'mother_id');
    }

    public function birthRegistration(): BelongsTo
    {
        return $this->belongsTo(BirthRegistration::class);
    }

    public function exitRecord(): HasOne
    {
        return $this->hasOne(AnimalExit::class)->latestOfMany();
    }

    public function exits(): HasMany
    {
        return $this->hasMany(AnimalExit::class);
    }

    public function receivingTasks(): HasMany
    {
        return $this->hasMany(ReceivingTask::class);
    }

    public function hospitalCases(): HasMany
    {
        return $this->hasMany(HospitalCase::class);
    }

    /** حيوان حي مسجّل رسمياً داخل الحديقة (وليس مجرد وجود ملف أو مسار حجر). */
    public function scopeInsideZooOfficially(Builder $query): Builder
    {
        return $query
            ->whereIn('status', AnimalStatus::recordsListValues())
            ->where(function (Builder $builder) {
                $builder
                    ->where('status', AnimalStatus::UnderBirthFollowUp->value)
                    ->orWhere('source', 'records')
                    ->orWhereNull('source')
                    ->orWhereNotNull('birth_registration_id')
                    ->orWhereHas(
                        'receivingTasks',
                        fn (Builder $task) => $task->whereNotNull('received_at'),
                    );
            });
    }

    public function scopeUnderBirthFollowUp(Builder $query): Builder
    {
        return $query->withQuarantine()
            ->where('status', AnimalStatus::UnderBirthFollowUp->value);
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return static::withQuarantine()
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->first();
    }

    public function scopeWithQuarantine(Builder $query): Builder
    {
        return $query->withoutGlobalScope(ExcludeQuarantineAnimals::class);
    }

    public function scopeVisibleToAdmin(Builder $query): Builder
    {
        return $query->where('status', AnimalStatus::Active->value);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('status', AnimalStatus::Active->value);
    }

    public function scopeInQuarantine(Builder $query): Builder
    {
        return $query->withQuarantine()->where('status', AnimalStatus::Quarantine->value);
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === AnimalStatus::Active->value;
    }

    public function isInQuarantine(): bool
    {
        return $this->status === AnimalStatus::Quarantine->value;
    }

    public function hasExitedZoo(): bool
    {
        return $this->status === AnimalStatus::Exited->value;
    }

    public function isOfficiallyInsideZoo(): bool
    {
        if (! in_array($this->status, AnimalStatus::recordsListValues(), true)) {
            return false;
        }

        if ($this->status === AnimalStatus::UnderBirthFollowUp->value) {
            return true;
        }

        if ($this->source === 'records' || $this->source === null) {
            return true;
        }

        if ($this->birth_registration_id !== null) {
            return true;
        }

        if ($this->source === 'quarantine' && $this->status === AnimalStatus::Active->value) {
            return $this->receivingTasks()->whereNotNull('received_at')->exists();
        }

        return false;
    }

    public function statusEnum(): ?AnimalStatus
    {
        return AnimalStatus::tryFrom($this->status);
    }

    public function displayLabel(): string
    {
        return $this->name
            ? "{$this->name} ({$this->species})"
            : $this->species;
    }

    public function displayPhotoUrl(): ?string
    {
        if ($this->photo_path) {
            return Storage::url($this->photo_path);
        }

        $this->loadMissing('profile');

        return $this->profile?->imageUrl();
    }

    public function formattedAge(): string
    {
        if ($this->age_method === 'birth' && $this->birth_date) {
            $years = (int) $this->birth_date->diffInYears(now());
            if ($years >= 1) {
                return $years === 1 ? 'سنة واحدة' : "{$years} سنوات";
            }

            $months = (int) $this->birth_date->diffInMonths(now());
            if ($months >= 1) {
                return $months === 1 ? 'شهر واحد' : "{$months} أشهر";
            }

            $days = max(1, (int) $this->birth_date->diffInDays(now()));

            return $days === 1 ? 'يوم واحد' : "{$days} أيام";
        }

        if ($this->age_method === 'approx' && $this->approx_age_value) {
            return "{$this->approx_age_value} {$this->approx_age_unit}";
        }

        return '—';
    }
}
