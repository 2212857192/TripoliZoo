<?php

namespace App\Http\Controllers;

use App\Models\AnimalProfile;
use App\Models\MapLocation;
use App\Models\TicketType;
use App\Models\VisitSetting;
use App\Support\MapCoordinates;
use App\Support\VisitSettingPresenter;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class VisitorAppController extends Controller
{
    public function index(): View
    {
        return view('visitor-app', [
            'settings' => VisitSetting::current(),
            'profiles' => AnimalProfile::listed()
                ->with('animal')
                ->where('is_visible', true)
                ->get(),
            'locations' => MapLocation::where('is_active', true)->get(),
            'ticketTypes' => TicketType::where('is_active', true)->orderBy('price')->get(),
        ]);
    }

    public function profile(AnimalProfile $profile): View
    {
        abort_unless($profile->is_visible, 404);
        $profile->load([
            'animal',
            'mapLocations' => fn ($query) => $query->where('is_active', true),
        ]);
        abort_if($profile->animal === null, 404);

        return view('visitor.animal-profile', [
            'profile' => $profile,
            'animal' => $profile->animal,
            'mapLocation' => $profile->mapLocations->first(),
            'settings' => VisitSetting::current(),
        ]);
    }

    public function visitInfo(): View
    {
        $settings = VisitSetting::current();
        $visitInfo = VisitSettingPresenter::toPublicArray($settings);

        return view('visitor.visit-info', [
            'visitInfo' => $visitInfo,
        ]);
    }

    public function map(): View
    {
        $locations = MapLocation::query()
            ->with('animalProfile.animal')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('animal_profile_id')
                    ->orWhereHas('animalProfile', fn ($profile) => $profile->listed()->where('is_visible', true));
            })
            ->orderBy('name')
            ->get();

        return view('visitor-map', [
            'mapImageUrl' => URL::asset('map.PNG'),
            'locations' => $locations
                ->map(function (MapLocation $location) {
                    $position = MapCoordinates::position($location);
                    $animal = $location->animalProfile?->animal;

                    if ($position === null) {
                        return null;
                    }

                    return [
                        'id' => $location->id,
                        'name' => $location->name,
                        'category' => $location->category,
                        'description' => $location->description ?: $animal?->displayLabel(),
                        'x' => $position['x'],
                        'y' => $position['y'],
                        'animal_profile_id' => $location->animalProfile?->id,
                        'animal_photo_url' => $location->animalProfile?->imageUrl() ?? $animal?->displayPhotoUrl(),
                    ];
                })
                ->filter()
                ->values(),
        ]);
    }
}
