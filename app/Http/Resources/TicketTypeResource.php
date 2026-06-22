<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TicketType */
class TicketTypeResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->target_description ?? '',
            'title' => $this->target_description ?? '',
            'category_label' => $this->target_description ?? '',
            'price' => (int) round((float) $this->price),
            'subtitle' => $this->visitor_age_group ?? '',
            'is_local' => $this->visitor_nationality === 'مواطن',
            'visitor_nationality' => $this->visitor_nationality,
            'visitor_age_group' => $this->visitor_age_group,
        ];
    }
}
