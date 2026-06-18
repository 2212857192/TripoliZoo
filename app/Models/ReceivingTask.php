<?php

namespace App\Models;

use App\Enums\ReceivingTaskSource;
use App\Enums\ReceivingTaskStatus;
use App\Enums\ReceivingTaskType;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivingTask extends Model
{
    protected $fillable = [
        'task_number',
        'animal_id',
        'quarantine_id',
        'supervisor_id',
        'status',
        'task_type',
        'source',
        'decision_date',
        'decision_issued_by',
        'decision_notes',
        'decision_treatments',
        'delay_reason',
        'delay_extra_note',
        'delay_recorded_at',
        'receipt_note',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReceivingTaskStatus::class,
            'task_type' => ReceivingTaskType::class,
            'source' => ReceivingTaskSource::class,
            'decision_date' => 'date',
            'decision_treatments' => 'array',
            'delay_recorded_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class)->withoutGlobalScope(ExcludeQuarantineAnimals::class);
    }

    public function quarantine(): BelongsTo
    {
        return $this->belongsTo(Quarantine::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function decisionIssuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decision_issued_by');
    }

    public function canConfirmReceipt(): bool
    {
        return in_array($this->status, [
            ReceivingTaskStatus::Pending,
            ReceivingTaskStatus::TemporarilyUnable,
        ], true);
    }

    public function canRecordDelay(): bool
    {
        return $this->status === ReceivingTaskStatus::Pending;
    }

    public function getRouteKeyName(): string
    {
        return 'task_number';
    }
}
