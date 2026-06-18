<?php

namespace App\Http\Resources;

use App\Models\MortalityCase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin MortalityCase */
class MortalityCaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $animal = $this->animal;

        return [
            'id' => (string) $this->id,
            'case_number' => $this->case_number,
            'animal_id' => $animal?->code ?? $this->subject_code,
            'animal_type' => $animal?->species ?? $this->subject_type,
            'animal_name' => $animal?->name,
            'group_name' => $this->group,
            'victim_kind' => $this->victim_kind->value,
            'victim_kind_label' => $this->victim_kind->label(),
            'death_cause' => $this->displayCause(),
            'notes' => $this->notes,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'registered_at' => $this->created_at?->toIso8601String(),
            'death_date' => $this->death_date?->format('Y-m-d'),
            'has_attachment' => $this->has_attachment,
            'attachment_url' => $this->attachment_path
                ? $request->getSchemeAndHttpHost().'/api/auth/mortality-cases/'.$this->case_number.'/attachment'
                : null,
        ];
    }
}
