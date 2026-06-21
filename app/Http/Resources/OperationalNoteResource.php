<?php

namespace App\Http\Resources;

use App\Models\OperationalNote;
use App\Support\ApiStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin OperationalNote */
class OperationalNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'note_number' => $this->note_number,
            'note_kind' => $this->note_kind->value,
            'note_kind_label' => $this->note_kind->label(),
            'summary' => $this->summary,
            'details' => $this->details,
            'group_name' => $this->group,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'registered_at' => $this->noted_at?->toIso8601String(),
            'noted_at' => $this->noted_at?->format('Y-m-d'),
            'has_attachment' => $this->has_attachment,
            'attachment_url' => ApiStorageUrl::fromPublicPath($this->attachment_path, $request),
        ];
    }
}
