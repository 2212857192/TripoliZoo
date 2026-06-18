<?php

namespace App\Http\Resources;

use App\Models\Animal;
use App\Models\BirthRegistration;
use App\Services\BirthRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin BirthRegistration */
class BirthRegistrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mother = $this->mother;

        return [
            'id' => (string) $this->id,
            'registration_number' => $this->registration_number,
            'mother_id' => $mother?->code,
            'mother_name' => $mother?->name,
            'mother_species' => $mother?->species,
            'animal_type' => $mother?->species,
            'group_name' => $this->group,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'birth_count' => $this->birth_count,
            'registered_at' => $this->created_at?->toIso8601String(),
            'newborns' => $this->whenLoaded('newborns', function () {
                return $this->newborns->map(fn (Animal $animal) => [
                    'code' => $animal->code,
                    'gender' => $animal->gender,
                    'distinguishing_mark' => $animal->distinguishing_marks,
                    'note' => $animal->registration_note,
                    'photo_url' => $animal->displayPhotoUrl(),
                ])->values();
            }),
        ];
    }
}
