<?php

namespace App\Http\Resources;

use App\Enums\QuarantineStatus;
use App\Models\Animal;
use App\Models\Quarantine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Quarantine */
class QuarantineApiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $animal = $this->animal;
        $doctor = $this->assignedDoctor();
        $lastNote = $this->notes->first();
        $lastVaccine = $this->vaccines->first();

        return [
            'id' => $this->case_number,
            'case_number' => $this->case_number,
            'animal_code' => $animal?->code,
            'animal_name' => $animal?->name ?: $animal?->species,
            'species' => $animal?->species,
            'gender' => $animal?->gender,
            'expected_group' => $animal?->group,
            'entry_date' => $this->entry_date?->toDateString(),
            'status' => $this->status->value,
            'duration_days' => $this->entry_date?->diffInDays(now()) ?? 0,
            'responsible_doctor' => $doctor?->name,
            'approximate_age' => $animal ? $this->formatAge($animal) : null,
            'animal_source' => $animal?->origin,
            'initial_health_status' => $this->initial_health_status,
            'general_notes' => $this->initial_notes,
            'last_vaccine' => $lastVaccine ? [
                'name' => $lastVaccine->name,
                'date' => $lastVaccine->administered_at->toDateString(),
                'note' => $lastVaccine->note,
                'doctor_name' => $lastVaccine->author?->name,
            ] : null,
            'last_note_date' => $lastNote?->noted_at?->toDateString(),
            'last_note_text' => $lastNote?->note,
            'health_notes' => $this->notes
                ->map(fn ($note) => [
                    'date' => $note->noted_at->toDateString(),
                    'text' => $note->note,
                    'author' => $note->author?->name,
                ])
                ->values()
                ->all(),
            'preventive_vaccines' => $this->vaccines
                ->map(fn ($vaccine) => [
                    'name' => $vaccine->name,
                    'date' => $vaccine->administered_at->toDateString(),
                    'note' => $vaccine->note,
                    'doctor_name' => $vaccine->author?->name,
                ])
                ->values()
                ->all(),
            'photo_url' => $animal?->photo_path ? Storage::url($animal->photo_path) : null,
            'is_unread' => (bool) ($this->is_unread ?? false),
            'can_manage' => $this->status === QuarantineStatus::UnderFollowUp,
        ];
    }

    private function formatAge(Animal $animal): string
    {
        return $animal->formattedAge();
    }
}
