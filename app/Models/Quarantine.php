<?php

namespace App\Models;

use App\Enums\QuarantineStatus;
use App\Enums\UserRole;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Quarantine extends Model
{
    protected $fillable = [
        'case_number',
        'animal_id',
        'reason',
        'initial_health_status',
        'status',
        'entry_date',
        'released_at',
        'closed_at',
        'close_reason',
        'close_notes',
        'close_documentation_path',
        'initial_notes',
        'responsible_vet_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'released_at' => 'date',
            'closed_at' => 'date',
            'status' => QuarantineStatus::class,
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class)->withoutGlobalScope(ExcludeQuarantineAnimals::class);
    }

    public function responsibleVet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_vet_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(QuarantineNote::class)->orderByDesc('noted_at');
    }

    public function vaccines(): HasMany
    {
        return $this->hasMany(QuarantineVaccine::class)->orderByDesc('administered_at');
    }

    public function receivingTask(): HasOne
    {
        return $this->hasOne(ReceivingTask::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(QuarantineNotification::class);
    }

    public function isUnderFollowUp(): bool
    {
        return $this->status === QuarantineStatus::UnderFollowUp;
    }

    public function passedQuarantine(): bool
    {
        return $this->status === QuarantineStatus::HealthReleased;
    }

    /** @param  Builder<self>  $query */
    public function scopePassedQuarantine(Builder $query): Builder
    {
        return $query->where('status', QuarantineStatus::HealthReleased);
    }

    public function getRouteKeyName(): string
    {
        return 'case_number';
    }

    /**
     * الطبيب البيطري المسؤول (مستخدم تطبيق الطبيب فقط) — وليس رئيس المستشفى.
     *
     * @param  Collection<string, User>|null  $doctorsByGroup
     */
    public function assignedDoctor(?Collection $doctorsByGroup = null): ?User
    {
        if ($this->responsibleVet?->isVeterinarian()) {
            return $this->responsibleVet;
        }

        $this->loadMissing('animal');
        $group = $this->animal?->group;

        if (! $group) {
            return null;
        }

        if ($doctorsByGroup) {
            return $doctorsByGroup->get($group);
        }

        return User::query()
            ->where('status', 'active')
            ->where('role', UserRole::Veterinarian->value)
            ->where('assigned_group', $group)
            ->first();
    }
}
