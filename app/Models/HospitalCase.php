<?php

namespace App\Models;

use App\Enums\HospitalCaseStatus;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalCase extends Model
{
    protected $fillable = [
        'case_number',
        'treatment_referral_id',
        'health_case_id',
        'animal_id',
        'group',
        'chief_complaint',
        'status',
        'admitted_by',
        'admitted_at',
        'closed_at',
        'closing_outcome',
    ];

    protected function casts(): array
    {
        return [
            'status' => HospitalCaseStatus::class,
            'admitted_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function treatmentReferral(): BelongsTo
    {
        return $this->belongsTo(TreatmentReferral::class);
    }

    public function healthCase(): BelongsTo
    {
        return $this->belongsTo(HealthCase::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class)->withoutGlobalScope(ExcludeQuarantineAnimals::class);
    }

    public function admitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admitted_by');
    }

    public function procedures(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(MedicalCaseProcedure::class, 'caseable');
    }

    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HospitalCaseNotification::class);
    }

    public function getRouteKeyName(): string
    {
        return 'case_number';
    }
}
