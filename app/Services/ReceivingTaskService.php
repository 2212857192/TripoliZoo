<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Enums\ReceivingTaskSource;
use App\Enums\ReceivingTaskStatus;
use App\Enums\ReceivingTaskType;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\Quarantine;
use App\Models\ReceivingTask;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReceivingTaskService
{
    public function __construct(
        private ReceivingTaskNumberGenerator $numbers,
        private SupervisorNotificationService $supervisorNotifier,
        private CareNotificationService $careNotifier,
        private VetNotificationService $vetNotifier,
        private MedicalDecisionTreatmentCollector $treatmentCollector,
    ) {}

    public function createFromQuarantineRelease(Quarantine $quarantine, User $issuer): ?ReceivingTask
    {
        $quarantine->loadMissing('animal');
        $animal = $quarantine->animal;
        $group = $animal->group;

        $supervisor = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::GroupSupervisor->value)
            ->where('assigned_group', $group)
            ->first();

        if (! $supervisor) {
            return null;
        }

        $task = null;

        $treatments = $this->treatmentCollector->fromQuarantine($quarantine);

        DB::transaction(function () use ($quarantine, $animal, $supervisor, $issuer, $treatments, &$task) {
            Animal::withQuarantine()
                ->whereKey($animal->id)
                ->update(['status' => AnimalStatus::PendingReceipt->value]);

            $task = ReceivingTask::create([
                'task_number' => $this->numbers->next(),
                'animal_id' => $animal->id,
                'quarantine_id' => $quarantine->id,
                'supervisor_id' => $supervisor->id,
                'status' => ReceivingTaskStatus::Pending,
                'task_type' => ReceivingTaskType::AfterHealthRelease,
                'source' => ReceivingTaskSource::Quarantine,
                'decision_date' => now()->toDateString(),
                'decision_issued_by' => $issuer->id,
                'decision_notes' => 'اكتملت فترة الحجر الصحي وصدر قرار الإفراج.',
                'decision_treatments' => $treatments ?: null,
            ]);
        });

        if ($task) {
            $fresh = $task->fresh(['animal', 'decisionIssuer', 'supervisor']);
            $this->supervisorNotifier->notifyReceivingTask($fresh);
            $this->careNotifier->notifyMedicalDecision($fresh);
            $this->vetNotifier->notifyMedicalDecision($fresh);
        }

        return $task;
    }

    /**
     * @param  iterable<int, array{treatment?: string|null}|object>  $followUps
     */
    public function createFromHospitalDecision(
        Animal $animal,
        User $issuer,
        ReceivingTaskType $taskType,
        iterable $followUps,
        ?string $decisionNotes = null,
    ): ?ReceivingTask {
        $supervisor = User::query()
            ->where('status', 'active')
            ->where('role', UserRole::GroupSupervisor->value)
            ->where('assigned_group', $animal->group)
            ->first();

        if (! $supervisor) {
            return null;
        }

        $treatments = $this->treatmentCollector->fromFollowUps($followUps);
        $task = null;

        DB::transaction(function () use ($animal, $issuer, $supervisor, $taskType, $decisionNotes, $treatments, &$task) {
            Animal::withQuarantine()
                ->whereKey($animal->id)
                ->update(['status' => AnimalStatus::PendingReceipt->value]);

            $task = ReceivingTask::create([
                'task_number' => $this->numbers->next(),
                'animal_id' => $animal->id,
                'supervisor_id' => $supervisor->id,
                'status' => ReceivingTaskStatus::Pending,
                'task_type' => $taskType,
                'source' => ReceivingTaskSource::Hospital,
                'decision_date' => now()->toDateString(),
                'decision_issued_by' => $issuer->id,
                'decision_notes' => $decisionNotes,
                'decision_treatments' => $treatments ?: null,
            ]);
        });

        if ($task) {
            $fresh = $task->fresh(['animal', 'decisionIssuer', 'supervisor']);
            $this->supervisorNotifier->notifyReceivingTask($fresh);
            $this->careNotifier->notifyMedicalDecision($fresh);
            $this->vetNotifier->notifyMedicalDecision($fresh);
        }

        return $task;
    }

    public function collectTreatmentsFromFollowUps(iterable $followUps): array
    {
        return $this->treatmentCollector->fromFollowUps($followUps);
    }

    public function confirmReceipt(ReceivingTask $task, User $supervisor, ?string $note = null): void
    {
        DB::transaction(function () use ($task, $note) {
            $task->update([
                'status' => ReceivingTaskStatus::Received,
                'receipt_note' => $note,
                'received_at' => now(),
                'delay_reason' => null,
                'delay_extra_note' => null,
                'delay_recorded_at' => null,
            ]);

            Animal::withQuarantine()
                ->whereKey($task->animal_id)
                ->update(['status' => AnimalStatus::Active->value]);
        });

        $fresh = $task->fresh(['animal', 'supervisor', 'decisionIssuer']);
        $this->careNotifier->notifyReceivingCompleted($fresh);
        $this->vetNotifier->notifyReceivingCompleted($fresh);
    }

    public function recordTemporaryDelay(ReceivingTask $task, string $reason, ?string $extraNote = null): void
    {
        $task->update([
            'status' => ReceivingTaskStatus::TemporarilyUnable,
            'delay_reason' => $reason,
            'delay_extra_note' => $extraNote,
            'delay_recorded_at' => now(),
        ]);

        $fresh = $task->fresh(['animal', 'supervisor', 'decisionIssuer']);
        $this->careNotifier->notifyReceivingDelay($fresh);
        $this->vetNotifier->notifyReceivingDelay($fresh);
    }
}
