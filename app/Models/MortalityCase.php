<?php

namespace App\Models;

use App\Enums\MortalityCaseStatus;
use App\Enums\MortalityVictimKind;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MortalityCase extends Model
{
    protected $fillable = [
        'case_number',
        'animal_id',
        'subject_code',
        'subject_type',
        'supervisor_id',
        'group',
        'victim_kind',
        'death_cause',
        'notes',
        'death_date',
        'has_attachment',
        'attachment_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'autopsy_reason',
    ];

    protected function casts(): array
    {
        return [
            'victim_kind' => MortalityVictimKind::class,
            'status' => MortalityCaseStatus::class,
            'death_date' => 'date',
            'has_attachment' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class)->withoutGlobalScope(ExcludeQuarantineAnimals::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(MortalityCaseNotification::class);
    }

    public function autopsyReferral(): HasOne
    {
        return $this->hasOne(AutopsyReferral::class);
    }

    public function getRouteKeyName(): string
    {
        return 'case_number';
    }

    public function canBeActedOn(): bool
    {
        return $this->status === MortalityCaseStatus::New;
    }

    public function displayCause(): string
    {
        $cause = trim((string) $this->death_cause);

        return $cause !== '' ? $cause : 'غير ظاهر';
    }

    public function isCauseApparent(): bool
    {
        return trim((string) $this->death_cause) !== '';
    }
}
