<?php

namespace App\Support;

use App\Enums\ReceivingTaskSource;
use App\Enums\ReceivingTaskStatus;
use App\Enums\ReceivingTaskType;
use App\Models\Animal;
use App\Models\HospitalCase;
use App\Models\ReceivingTask;
use Carbon\CarbonInterface;

final class MedicalDecisionListItem
{
    public function __construct(
        public readonly string $referenceNumber,
        public readonly string $typeLabel,
        public readonly string $typeKey,
        public readonly ?Animal $animal,
        public readonly string $sourceLabel,
        public readonly ?CarbonInterface $decisionDate,
        public readonly string $receivingStatusLabel,
        public readonly string $receivingStatusBadgeClass,
        public readonly string $receivingStatusFilterKey,
        public readonly string $showUrl,
        public readonly string $searchText,
    ) {}

    public static function fromReceivingTask(ReceivingTask $task, string $showUrl): self
    {
        $animal = $task->animal;

        return new self(
            referenceNumber: $task->task_number,
            typeLabel: $task->task_type->careDecisionLabel(),
            typeKey: $task->task_type->careDecisionTypeKey(),
            animal: $animal,
            sourceLabel: $task->source->fromLabel(),
            decisionDate: $task->decision_date,
            receivingStatusLabel: $task->status->label(),
            receivingStatusBadgeClass: match ($task->status) {
                ReceivingTaskStatus::Pending => 'badge-pending',
                ReceivingTaskStatus::Received => 'badge-completed',
                ReceivingTaskStatus::TemporarilyUnable => 'badge-review',
            },
            receivingStatusFilterKey: $task->status->careStatusKey(),
            showUrl: $showUrl,
            searchText: trim(implode(' ', array_filter([
                $task->task_number,
                $animal?->code,
                $animal?->name,
                $animal?->species,
                $task->task_type->careDecisionLabel(),
            ]))),
        );
    }

    public static function fromSlaughterCase(HospitalCase $case, string $showUrl): self
    {
        $animal = $case->animal;

        return new self(
            referenceNumber: $case->case_number,
            typeLabel: 'ذبح اضطراري',
            typeKey: 'slaughter',
            animal: $animal,
            sourceLabel: ReceivingTaskSource::Hospital->fromLabel(),
            decisionDate: $case->closed_at,
            receivingStatusLabel: 'لا يتطلب استلام',
            receivingStatusBadgeClass: 'badge-none',
            receivingStatusFilterKey: 'not-required',
            showUrl: $showUrl,
            searchText: trim(implode(' ', array_filter([
                $case->case_number,
                $animal?->code,
                $animal?->name,
                $animal?->species,
                'ذبح اضطراري',
            ]))),
        );
    }

    public function sortTimestamp(): int
    {
        return $this->decisionDate?->getTimestamp() ?? 0;
    }

    public function formattedDecisionDate(): string
    {
        return $this->decisionDate?->format('Y-m-d') ?? '—';
    }

    public function sourceFilterKey(): string
    {
        return match ($this->sourceLabel) {
            ReceivingTaskSource::Quarantine->fromLabel() => 'quarantine',
            default => 'hospital',
        };
    }
}
