<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\AnimalProfile;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AnimalProfileController extends Controller
{
    public function index(): View
    {
        return view('admin.animals.index', [
            'profiles' => AnimalProfile::with('animal')->orderByDesc('id')->get(),
        ]);
    }

    public function create(): View
    {
        $animals = Animal::query()
            ->whereDoesntHave('profile')
            ->where('status', 'active')
            ->orderBy('species')
            ->get();

        return view('admin.animals.create', [
            'animals' => $animals,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'animal_id' => ['required', 'exists:animals,id', 'unique:animal_profiles,animal_id'],
            'description' => ['required', 'string', 'min:20', 'max:600'],
            'scientific_name' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $animal = Animal::findOrFail($data['animal_id']);
        $path = $request->file('image')->store('animal-profiles', 'public');

        $profile = AnimalProfile::create([
            'animal_id' => $animal->id,
            'description' => $data['description'],
            'scientific_name' => $data['scientific_name'],
            'display_code' => $animal->code,
            'image_path' => $path,
            'is_visible' => true,
            'created_by' => $request->user()->id,
        ]);

        AdminActivityLogger::log('animal_profile', $profile->id, 'created', "إضافة محتوى تعريفي: {$animal->displayLabel()}");

        return redirect()
            ->route('admin.animals.show', $profile)
            ->with('success', 'تم نشر المحتوى التعريفي.');
    }

    public function show(AnimalProfile $profile): View
    {
        $profile->load('animal');

        return view('admin.animals.show', ['profile' => $profile]);
    }

    public function edit(AnimalProfile $profile): View
    {
        $profile->load('animal');

        return view('admin.animals.edit', ['profile' => $profile]);
    }

    public function update(Request $request, AnimalProfile $profile): RedirectResponse
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'min:20', 'max:600'],
            'scientific_name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($profile->image_path) {
                Storage::disk('public')->delete($profile->image_path);
            }
            $data['image_path'] = $request->file('image')->store('animal-profiles', 'public');
        }

        unset($data['image']);
        $profile->update($data);

        AdminActivityLogger::log('animal_profile', $profile->id, 'updated', 'تعديل محتوى تعريفي');

        return redirect()
            ->route('admin.animals.show', $profile)
            ->with('success', 'تم حفظ التعديلات.');
    }

    public function toggleVisibility(AnimalProfile $profile): RedirectResponse
    {
        $profile->update(['is_visible' => ! $profile->is_visible]);

        $summary = $profile->is_visible ? 'إظهار محتوى حيوان للزوار' : 'إخفاء محتوى حيوان من تطبيق الزائر';
        AdminActivityLogger::log('animal_profile', $profile->id, 'visibility', $summary);

        return back()->with('success', $profile->is_visible ? 'أصبح المحتوى ظاهراً للزوار.' : 'تم إخفاء المحتوى عن الزوار.');
    }
}
