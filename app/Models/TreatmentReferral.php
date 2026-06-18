<?php

namespace App\Models;

use App\Enums\TreatmentReferralStatus;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TreatmentReferral extends Model
{
    protected $fillable = [
        'referral_number',
        'health_case_id',
        'animal_id',
        'group',
        'status',
        'referred_by',
        'referred_at',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => TreatmentReferralStatus::class,
            'referred_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function healthCase(): BelongsTo
    {
        return $this->belongsTo(HealthCase::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class)->withoutGlobalScope(ExcludeQuarantineAnimals::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TreatmentReferralNotification::class);
    }

    public function hospitalCase(): HasOne
    {
        return $this->hasOne(HospitalCase::class);
    }

    public function canBeActedOn(): bool
    {
        return $this->status === TreatmentReferralStatus::Pending;
    }

    public function getRouteKeyName(): string
    {
        return 'referral_number';
    }
}
