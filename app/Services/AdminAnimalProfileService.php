<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\AnimalProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminAnimalProfileService
{
    /** @return array<string, mixed> */
    public function indexViewData(Request $request, bool $readOnly = false): array
    {
        $query = AnimalProfile::listed()
            ->with([
                'animal',
                'mapLocations' => fn ($locationQuery) => $locationQuery->where('is_active', true),
            ])
            ->orderByDesc('id');

        if ($visibility = $request->query('visibility')) {
            $query->where('is_visible', $visibility === 'visible');
        }

        if ($group = $request->query('group')) {
            $query->whereHas('animal', fn ($animalQuery) => $animalQuery->where('group', $group));
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('scientific_name', 'like', "%{$search}%")
                    ->orWhere('display_code', 'like', "%{$search}%")
                    ->orWhereHas('animal', function ($animalQuery) use ($search) {
                        $animalQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('species', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $profiles = $query->get();

        return [
            'profiles' => $profiles,
            'readOnly' => $readOnly,
            'stats' => [
                'total' => $profiles->count(),
                'visible' => $profiles->where('is_visible', true)->count(),
                'hidden' => $profiles->where('is_visible', false)->count(),
            ],
            'filters' => [
                'q' => $request->query('q', ''),
                'visibility' => $request->query('visibility', ''),
                'group' => $request->query('group', ''),
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function createViewData(): array
    {
        return [
            'animals' => $this->eligibleAnimalsQuery()->get(),
        ];
    }

    /** @return array<string, mixed> */
    public function showViewData(AnimalProfile $profile): array
    {
        $profile->load([
            'animal',
            'creator',
            'mapLocations' => fn ($query) => $query->where('is_active', true),
        ]);

        abort_if($profile->animal === null, 404);

        return [
            'profile' => $profile,
            'mapLocation' => $profile->mapLocations->first(),
            'qrPayload' => $profile->qrPayload(),
            'visitorUrl' => route('visitor.animal', $profile),
        ];
    }

    /** @return array<string, mixed> */
    public function editViewData(AnimalProfile $profile): array
    {
        $profile->load('animal');
        abort_if($profile->animal === null, 404);

        return ['profile' => $profile];
    }

    public function store(Request $request, User $user): AnimalProfile
    {
        $data = $request->validate(
            [
                'animal_id' => ['required', 'integer', 'unique:animal_profiles,animal_id'],
                'description' => ['required', 'string', 'min:20', 'max:600'],
                'image' => ['required', 'image', 'max:5120'],
            ],
            $this->validationMessages(),
        );

        $animal = $this->findEligibleAnimal((int) $data['animal_id']);
        $path = $request->file('image')->store('animal-profiles', 'public');

        return DB::transaction(function () use ($data, $animal, $path, $user) {
            return AnimalProfile::create([
                'animal_id' => $animal->id,
                'description' => $data['description'],
                'display_code' => $animal->code,
                'image_path' => $path,
                'is_visible' => true,
                'created_by' => $user->id,
            ]);
        });
    }

    public function update(Request $request, AnimalProfile $profile): AnimalProfile
    {
        abort_if($profile->animal === null, 404);

        $data = $request->validate(
            [
                'description' => ['required', 'string', 'min:20', 'max:600'],
                'image' => ['nullable', 'image', 'max:5120'],
            ],
            $this->validationMessages(),
        );

        if ($request->hasFile('image')) {
            if ($profile->image_path) {
                Storage::disk('public')->delete($profile->image_path);
            }
            $data['image_path'] = $request->file('image')->store('animal-profiles', 'public');
        }

        unset($data['image']);
        $profile->update($data);

        return $profile->fresh(['animal', 'mapLocations']);
    }

    public function toggleVisibility(AnimalProfile $profile): AnimalProfile
    {
        abort_if($profile->animal === null, 404);

        $profile->update(['is_visible' => ! $profile->is_visible]);

        return $profile->fresh(['animal']);
    }

    public function findEligibleAnimal(int $animalId): Animal
    {
        $animal = $this->eligibleAnimalsQuery()
            ->whereKey($animalId)
            ->first();

        if (! $animal) {
            throw ValidationException::withMessages([
                'animal_id' => 'الحيوان غير متاح لإضافة محتوى تعريفي.',
            ]);
        }

        return $animal;
    }

    private function eligibleAnimalsQuery()
    {
        return Animal::query()
            ->insideZooOfficially()
            ->whereDoesntHave('profile')
            ->orderBy('species')
            ->orderBy('code');
    }

    /** @return array<string, string> */
    private function validationMessages(): array
    {
        return [
            'animal_id.required' => 'يرجى اختيار الحيوان.',
            'animal_id.unique' => 'يوجد محتوى تعريفي لهذا الحيوان مسبقاً.',
            'description.required' => 'الوصف التعريفي مطلوب.',
            'description.min' => 'الوصف التعريفي يجب أن لا يقل عن 20 حرفاً.',
            'description.max' => 'الوصف التعريفي يجب ألا يتجاوز 600 حرفاً.',
            'image.required' => 'صورة الحيوان مطلوبة.',
            'image.image' => 'يجب رفع ملف صورة بصيغة JPG أو PNG.',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 5 ميجابايت.',
        ];
    }
}
