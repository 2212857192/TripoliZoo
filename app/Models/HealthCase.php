<?php

namespace App\Models;

use App\Enums\HealthCaseFollowUpKind;
use App\Enums\HealthCaseStatus;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HealthCase extends Model
{
    protected $fillable = [
        'case_number',
        'animal_id',
        'supervisor_id',
        'group',
        'description',
        'follow_up_kind',
        'has_attachment',
        'attachment_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'referred_by',
        'referred_at',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_kind' => HealthCaseFollowUpKind::class,
            'status' => HealthCaseStatus::class,
            'has_attachment' => 'boolean',
            'reviewed_at' => 'datetime',
            'referred_at' => 'datetime',
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

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function treatmentReferral(): HasOne
    {
        return $this->hasOne(TreatmentReferral::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(HealthCaseNotification::class);
    }

    public function getRouteKeyName(): string
    {
        return 'case_number';
    }

    public function canBeActedOn(): bool
    {
        return $this->status === HealthCaseStatus::New;
    }
}
