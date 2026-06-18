<?php

namespace App\Http\Resources;

use App\Models\ReceivingTask;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin ReceivingTask */
class ReceivingTaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $animal = $this->animal;
        $issuer = $this->decisionIssuer;

        return [
            'id' => (string) $this->id,
            'task_number' => $this->task_number,
            'status' => $this->status->value,
            'task_type' => $this->task_type->value,
            'source' => $this->source->value,
            'animal_id' => $animal?->code ?? '',
            'animal_type' => $animal?->species ?? '',
            'animal_gender' => $animal?->gender,
            'group_name' => $animal?->group ?? '',
            'animal_image_url' => $animal?->photo_path ? Storage::url($animal->photo_path) : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'decision_type' => $this->task_type->value === 'after_health_release'
                ? 'health_release'
                : 'after_treatment',
            'decision_date' => $this->decision_date?->toDateString(),
            'decision_issued_by' => $issuer?->name ?? '—',
            'decision_issuer_role' => $issuer?->role,
            'decision_notes' => $this->decision_notes,
            'delay_reason' => $this->delay_reason,
            'delay_extra_note' => $this->delay_extra_note,
            'delay_recorded_at' => $this->delay_recorded_at?->toIso8601String(),
            'receipt_note' => $this->receipt_note,
        ];
    }
}
