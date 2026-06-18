<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Enums\HealthReportStatus;
use App\Enums\HospitalCaseStatus;
use App\Models\Animal;
use App\Models\HealthReport;
use App\Models\HospitalCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HealthReportService
{
    public function __construct(
        private HealthReportNumberGenerator $numbers,
        private HealthReportNotificationService $notifier,
        private FieldCaseService $fieldCases,
    ) {}

    public function createReport(
        User $supervisor,
        Animal $animal,
        string $description,
        bool $isUrgent = false,
        ?string $attachmentPath = null,
    ): HealthReport {
        $report = null;

        DB::transaction(function () use ($supervisor, $animal, $description, $isUrgent, $attachmentPath, &$report) {
            $report = HealthReport::create([
                'report_number' => $this->numbers->next(),
                'animal_id' => $animal->id,
                'supervisor_id' => $supervisor->id,
                'group' => $animal->group,
                'description' => $description,
                'is_urgent' => $isUrgent,
                'has_attachment' => $attachmentPath !== null,
                'attachment_path' => $attachmentPath,
                'status' => HealthReportStatus::Sent,
            ]);
        });

        $fresh = $report->fresh(['animal', 'supervisor']);
        $this->notifier->notifyNewReport($fresh);

        return $fresh;
    }

    public function markReceived(HealthReport $report, User $vet): HealthReport
    {
        if ($report->status !== HealthReportStatus::Sent) {
            return $report;
        }

        $report->update([
            'status' => HealthReportStatus::Received,
            'assigned_vet_id' => $vet->id,
            'doctor_updated_at' => now(),
        ]);

        $fresh = $report->fresh(['animal', 'supervisor', 'assignedVet']);
        $this->notifier->notifySupervisorOfUpdate(
            $fresh,
            'الطبيب استلم البلاغ — '.$fresh->report_number,
            'استلم د. '.$vet->name.' البلاغ الصحي للحيوان '.($fresh->animal?->code ?? '').' وهو قيد المتابعة.'
        );

        return $fresh;
    }

    public function closeReport(HealthReport $report, User $vet, string $note, bool $fieldCaseOpened = false): HealthReport
    {
        if ($report->status === HealthReportStatus::Closed) {
            return $report;
        }

        $report->update([
            'status' => HealthReportStatus::Closed,
            'assigned_vet_id' => $vet->id,
            'doctor_note' => $note,
            'doctor_updated_at' => now(),
            'field_case_opened' => $fieldCaseOpened,
        ]);

        $fresh = $report->fresh(['animal', 'supervisor', 'assignedVet']);
        $this->notifier->notifySupervisorOfUpdate(
            $fresh,
            'تم إغلاق البلاغ — '.$fresh->report_number,
            'أغلق د. '.$vet->name.' البلاغ: '.$note
        );

        if ($fieldCaseOpened) {
            $this->fieldCases->createFromHealthReport($fresh, $vet);
        }

        return $fresh;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Animal> */
    public function animalsForSupervisor(User $supervisor)
    {
        return $this->animalsForGroup($supervisor->assigned_group);
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Animal> */
    public function animalsForVet(User $vet)
    {
        return $this->animalsForGroup($vet->assigned_group, excludeInHospital: true);
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Animal> */
    public function animalsForGroup(?string $group, bool $excludeInHospital = false)
    {
        if (! $group) {
            return collect();
        }

        $query = Animal::withQuarantine()
            ->where('group', $group)
            ->whereIn('status', AnimalStatus::groupOperationalValues())
            ->whereNotNull('registered_at')
            ->orderBy('code');

        if ($excludeInHospital) {
            $query->whereNotIn('id', $this->animalIdsInOpenHospital($group));
        }

        return $query->get();
    }

    public function findRegisteredAnimalForGroup(
        string $code,
        ?string $group,
        bool $excludeInHospital = false,
    ): ?Animal {
        if (! $group) {
            return null;
        }

        $animal = Animal::withQuarantine()
            ->where('code', $code)
            ->where('group', $group)
            ->whereIn('status', AnimalStatus::groupOperationalValues())
            ->whereNotNull('registered_at')
            ->first();

        if (! $animal || ! $excludeInHospital) {
            return $animal;
        }

        $inHospital = HospitalCase::query()
            ->where('animal_id', $animal->id)
            ->whereIn('status', $this->openHospitalStatuses())
            ->exists();

        return $inHospital ? null : $animal;
    }

    /** @return list<string> */
    private function openHospitalStatuses(): array
    {
        return array_map(
            fn (HospitalCaseStatus $status) => $status->value,
            [
                ...HospitalCaseStatus::active(),
                ...HospitalCaseStatus::pendingHandover(),
            ],
        );
    }

    /** @return \Illuminate\Support\Collection<int, int> */
    private function animalIdsInOpenHospital(string $group)
    {
        return HospitalCase::query()
            ->where('group', $group)
            ->whereIn('status', $this->openHospitalStatuses())
            ->pluck('animal_id');
    }
}
