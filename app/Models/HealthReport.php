<?php

namespace App\Models;

use App\Enums\HealthReportStatus;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthReport extends Model
{
    protected $fillable = [
        'report_number',
        'animal_id',
        'supervisor_id',
        'group',
        'description',
        'is_urgent',
        'has_attachment',
        'attachment_path',
        'status',
        'assigned_vet_id',
        'doctor_note',
        'doctor_updated_at',
        'field_case_opened',
    ];

    protected function casts(): array
    {
        return [
            'status' => HealthReportStatus::class,
            'is_urgent' => 'boolean',
            'has_attachment' => 'boolean',
            'field_case_opened' => 'boolean',
            'doctor_updated_at' => 'datetime',
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

    public function assignedVet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_vet_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(HealthReportNotification::class);
    }

    public function fieldCase(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FieldCase::class);
    }

    public function getRouteKeyName(): string
    {
        return 'report_number';
    }
}
