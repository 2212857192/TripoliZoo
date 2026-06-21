<?php

namespace App\Models;

use App\Enums\FieldCaseStatus;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldCase extends Model
{
    protected $fillable = [
        'case_number',
        'animal_id',
        'group',
        'open_reason',
        'initial_note',
        'status',
        'opened_by',
        'health_report_id',
        'hospital_case_id',
        'opened_at',
        'closed_at',
        'closing_note',
    ];

    protected function casts(): array
    {
        return [
            'status' => FieldCaseStatus::class,
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class)->withoutGlobalScope(ExcludeQuarantineAnimals::class);
    }

    public function opener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function healthReport(): BelongsTo
    {
        return $this->belongsTo(HealthReport::class);
    }

    public function hospitalCase(): BelongsTo
    {
        return $this->belongsTo(HospitalCase::class);
    }

    public function procedures(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(MedicalCaseProcedure::class, 'caseable');
    }

    public function getRouteKeyName(): string
    {
        return 'case_number';
    }
}
