<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnimalProfile;
use App\Models\MapLocation;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MapLocationController extends Controller
{
    public function index(): View
    {
        return view('admin.map-locations.index', [
            'locations' => MapLocation::with('animalProfile.animal')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.map-locations.create', [
            'profiles' => AnimalProfile::with('animal')->where('is_visible', true)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $location = MapLocation::create($data);

        AdminActivityLogger::log('map_location', $location->id, 'created', "إضافة موقع: {$location->name}");

        return redirect()
            ->route('admin.map-locations.index')
            ->with('success', 'تم حفظ الموقع على الخريطة.');
    }

    public function edit(MapLocation $mapLocation): View
    {
        return view('admin.map-locations.edit', [
            'location' => $mapLocation,
            'profiles' => AnimalProfile::with('animal')->get(),
        ]);
    }

    public function update(Request $request, MapLocation $mapLocation): RedirectResponse
    {
        $mapLocation->update($this->validated($request));

        AdminActivityLogger::log('map_location', $mapLocation->id, 'updated', "تعديل موقع: {$mapLocation->name}");

        return redirect()
            ->route('admin.map-locations.index')
            ->with('success', 'تم تحديث الموقع.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['enclosure', 'service', 'dining'])],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string'],
            'animal_profile_id' => ['nullable', 'exists:animal_profiles,id'],
        ]);

        if ($data['category'] !== 'enclosure') {
            $data['animal_profile_id'] = null;
        }

        return $data;
    }
}
