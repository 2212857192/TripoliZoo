<?php

namespace App\Http\Resources;

use App\Models\HealthCase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin HealthCase */
class HealthCaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $animal = $this->animal;

        return [
            'id' => (string) $this->id,
            'case_number' => $this->case_number,
            'animal_id' => $animal?->code ?? '',
            'animal_type' => $animal?->species ?? '',
            'animal_name' => $animal?->name,
            'group_name' => $this->group,
            'description' => $this->description,
            'follow_up_kind' => $this->follow_up_kind->value,
            'follow_up_label' => $this->follow_up_kind->label(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'registered_at' => $this->created_at?->toIso8601String(),
            'has_attachment' => $this->has_attachment,
            'attachment_url' => $this->attachment_path
                ? $request->getSchemeAndHttpHost().'/api/auth/health-cases/'.$this->case_number.'/attachment'
                : null,
            'treatment_referral_number' => $this->treatmentReferral?->referral_number,
        ];
    }
}
