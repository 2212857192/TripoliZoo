<?php

namespace App\Models;

use App\Enums\AutopsyReferralStatus;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutopsyReferral extends Model
{
    protected $fillable = [
        'referral_number',
        'mortality_case_id',
        'animal_id',
        'group',
        'status',
        'referred_by',
        'referred_at',
        'transfer_reason',
        'documented_by',
        'documented_at',
        'final_death_cause',
        'autopsy_notes',
        'report_path',
    ];

    protected function casts(): array
    {
        return [
            'status' => AutopsyReferralStatus::class,
            'referred_at' => 'datetime',
            'documented_at' => 'datetime',
        ];
    }

    public function mortalityCase(): BelongsTo
    {
        return $this->belongsTo(MortalityCase::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class)->withoutGlobalScope(ExcludeQuarantineAnimals::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function documenter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'documented_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AutopsyReferralNotification::class);
    }

    public function getRouteKeyName(): string
    {
        return 'referral_number';
    }

    public function canBeDocumented(): bool
    {
        return $this->status === AutopsyReferralStatus::Pending;
    }
}
