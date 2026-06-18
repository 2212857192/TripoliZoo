<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnimalProfile;
use Illuminate\Http\JsonResponse;

class VisitorAnimalController extends Controller
{
    public function index(): JsonResponse
    {
        $profiles = AnimalProfile::listed()
            ->with(['animal', 'mapLocations' => fn ($query) => $query->where('is_active', true)])
            ->where('is_visible', true)
            ->orderByDesc('id')
            ->get()
            ->map(fn (AnimalProfile $profile) => $this->transform($profile))
            ->values();

        return response()->json(['data' => $profiles]);
    }

    public function show(string $identifier): JsonResponse
    {
        $profile = AnimalProfile::listed()
            ->with(['animal', 'mapLocations' => fn ($query) => $query->where('is_active', true)])
            ->where('is_visible', true)
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                    ->orWhere('display_code', $identifier)
                    ->orWhereHas('animal', fn ($animalQuery) => $animalQuery->where('code', $identifier));
            })
            ->firstOrFail();

        return response()->json(['data' => $this->transform($profile)]);
    }

    private function transform(AnimalProfile $profile): array
    {
        $animal = $profile->animal;
        $location = $profile->mapLocations->first();
        $age = $animal?->formattedAge();
        $stats = array_filter([
            'الرمز' => $animal?->code,
            'المجموعة' => $animal?->group,
            'الجنس' => $animal?->gender,
            'العمر' => ($age && $age !== '—') ? $age : null,
        ]);

        return [
            'id' => $profile->id,
            'animal_id' => $animal?->id,
            'name' => $profile->visitorDisplayName(),
            'sci_name' => $profile->visitorSubtitle(),
            'category' => $this->categoryForGroup($animal?->group),
            'image' => $profile->imageUrl(),
            'desc' => $profile->description,
            'habitat' => $animal?->origin ?: $animal?->registration_note ?: '',
            'stats' => $stats,
            'facts' => array_values(array_filter([
                $animal?->distinguishing_marks,
                $animal?->prior_history,
            ])),
            'fact_items' => array_values(array_filter([
                $animal?->origin ? ['label' => 'الأصل الجغرافي', 'value' => $animal->origin] : null,
                $animal?->distinguishing_marks ? ['label' => 'علامات مميزة', 'value' => $animal->distinguishing_marks] : null,
                $animal?->prior_history ? ['label' => 'تاريخ سابق', 'value' => $animal->prior_history] : null,
            ])),
            'location' => $location?->name ?: 'حديقة حيوان طرابلس',
            'map_location' => $location ? [
                'id' => $location->id,
                'name' => $location->name,
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
            ] : null,
            'qr_code' => $animal?->code ?: $profile->display_code,
            'qr_payload' => $profile->qrPayload(),
        ];
    }

    private function categoryForGroup(?string $group): string
    {
        return match ($group) {
            'القططية' => 'predators',
            'الطيور' => 'birds',
            default => 'mammals',
        };
    }
}
