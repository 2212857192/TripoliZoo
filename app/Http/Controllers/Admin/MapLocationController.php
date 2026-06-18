<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnimalProfile;
use App\Models\MapLocation;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MapLocationController extends Controller
{
    public function index(): View
    {
        return view('admin.map-locations.index', [
            'locations' => MapLocation::query()
                ->with('animalProfile.animal')
                ->where(function ($query) {
                    $query->whereNull('animal_profile_id')
                        ->orWhereHas('animalProfile', fn ($q) => $q->listed());
                })
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.map-locations.create', [
            'profiles'        => AnimalProfile::listed()->with('animal')->orderByDesc('id')->get(),
            'usedProfileIds'  => $this->usedProfileIds(),
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
            'location'       => $mapLocation,
            'profiles'       => AnimalProfile::listed()->with('animal')->orderByDesc('id')->get(),
            'usedProfileIds' => $this->usedProfileIds(excludeLocation: $mapLocation->id),
        ]);
    }

    public function update(Request $request, MapLocation $mapLocation): RedirectResponse
    {
        $mapLocation->update($this->validated($request, excludeLocationId: $mapLocation->id));

        AdminActivityLogger::log('map_location', $mapLocation->id, 'updated', "تعديل موقع: {$mapLocation->name}");

        return redirect()
            ->route('admin.map-locations.index')
            ->with('success', 'تم تحديث الموقع.');
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
    private function validated(Request $request, ?int $excludeLocationId = null): array
    {
        $uniqueRule = Rule::unique('map_locations', 'animal_profile_id')
            ->whereNotNull('animal_profile_id');

        if ($excludeLocationId) {
            $uniqueRule->ignore($excludeLocationId);
        }

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'category'         => ['required', Rule::in(['enclosure', 'service', 'dining'])],
            'latitude'         => ['required', 'numeric', 'between:0,1'],
            'longitude'        => ['required', 'numeric', 'between:0,1'],
            'animal_profile_id'=> ['nullable', 'integer', 'exists:animal_profiles,id', $uniqueRule],
            'is_active'        => ['nullable', 'boolean'],
        ], [
            'animal_profile_id.unique' => 'هذا الحيوان مرتبط بموقع آخر على الخريطة بالفعل.',
        ]);

        $data['description'] = null;
        $data['is_active']   = $request->boolean('is_active');

        if ($data['category'] !== 'enclosure') {
            $data['animal_profile_id'] = null;
        } elseif (! empty($data['animal_profile_id'])) {
            abort_unless(
                AnimalProfile::listed()->whereKey($data['animal_profile_id'])->exists(),
                422,
                'لا يمكن ربط موقع بحيوان تحت الحجر الصحي.'
            );
        }

        return $data;
    }

    /** @return Collection<int, int> */
    private function usedProfileIds(?int $excludeLocation = null): Collection
    {
        return MapLocation::query()
            ->whereNotNull('animal_profile_id')
            ->when($excludeLocation, fn ($q) => $q->where('id', '!=', $excludeLocation))
            ->pluck('animal_profile_id');
    }
}
