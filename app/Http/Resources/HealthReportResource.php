<?php

namespace App\Http\Resources;

use App\Models\HealthReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin HealthReport */
class HealthReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $animal = $this->animal;
        $vet = $this->assignedVet;

        return [
            'id' => (string) $this->id,
            'report_number' => $this->report_number,
            'animal_id' => $animal?->code ?? '',
            'animal_type' => $animal?->species ?? '',
            'animal_name' => $animal?->name,
            'group_name' => $this->group,
            'description' => $this->description,
            'sent_at' => $this->created_at?->toIso8601String(),
            'status' => $this->status->value,
            'is_urgent' => $this->is_urgent,
            'assigned_doctor_name' => $vet?->name,
            'doctor_note' => $this->doctor_note,
            'doctor_updated_at' => $this->doctor_updated_at?->toIso8601String(),
            'field_case_opened' => $this->field_case_opened,
            'has_attachment' => $this->has_attachment,
            'attachment_url' => $this->attachment_path
                ? $request->getSchemeAndHttpHost().'/api/auth/health-reports/'.$this->report_number.'/attachment'
                : null,
        ];
    }
}
