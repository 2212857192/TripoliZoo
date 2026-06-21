<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'locations' => MapLocation::query()
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.map-locations.create');
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
        ]);
    }

    public function update(Request $request, MapLocation $mapLocation): RedirectResponse
    {
        $mapLocation->update($this->validated($request));

        AdminActivityLogger::log('map_location', $mapLocation->id, 'updated', "تعديل موقع: {$mapLocation->name}");

        return redirect()
            ->route('admin.map-locations.index')
            ->with('success', 'تم تحديث الموقع على الخريطة.');
    }

    public function toggle(MapLocation $mapLocation): RedirectResponse
    {
        $mapLocation->update(['is_active' => ! $mapLocation->is_active]);

        AdminActivityLogger::log(
            'map_location',
            $mapLocation->id,
            'visibility',
            $mapLocation->is_active ? "إظهار موقع: {$mapLocation->name}" : "إخفاء موقع: {$mapLocation->name}"
        );

        return back()->with('success', $mapLocation->is_active ? 'أصبح الموقع ظاهراً للزوار.' : 'تم إخفاء الموقع عن الزوار.');
    }

    public function destroy(MapLocation $mapLocation): RedirectResponse
    {
        $name = $mapLocation->name;
        $id = $mapLocation->id;

        $mapLocation->delete();

        AdminActivityLogger::log('map_location', $id, 'deleted', "حذف موقع: {$name}");

        return redirect()
            ->route('admin.map-locations.index')
            ->with('success', 'تم حذف الموقع من الخريطة.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['enclosure', 'service', 'dining'])],
            'latitude' => ['required', 'numeric', 'between:0,1'],
            'longitude' => ['required', 'numeric', 'between:0,1'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'latitude.between' => 'يرجى تحديد موقع صحيح على الخريطة.',
            'longitude.between' => 'يرجى تحديد موقع صحيح على الخريطة.',
        ]);

        $data['description'] = null;
        $data['animal_profile_id'] = null;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
