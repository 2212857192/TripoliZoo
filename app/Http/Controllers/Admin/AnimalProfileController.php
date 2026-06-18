<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnimalProfile;
use App\Services\AdminActivityLogger;
use App\Services\AdminAnimalProfileService;
use App\Services\AnimalQrCodeService;
use App\Support\PublicUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AnimalProfileController extends Controller
{
    public function index(Request $request, AdminAnimalProfileService $service): View
    {
        return view('admin.animals.index', $service->indexViewData($request));
    }

    public function create(AdminAnimalProfileService $service): View
    {
        return view('admin.animals.create', $service->createViewData());
    }

    public function store(Request $request, AdminAnimalProfileService $service): RedirectResponse
    {
        $profile = $service->store($request, $request->user());
        $profile->load('animal');

        $label = $profile->animal?->displayLabel() ?? 'حيوان #'.$profile->animal_id;
        AdminActivityLogger::log(
            'animal_profile',
            $profile->id,
            'created',
            'إضافة محتوى تعريفي: '.$label,
        );

        return redirect()
            ->route('admin.animals.index')
            ->with('success', 'تم نشر المحتوى التعريفي.');
    }

    public function show(AnimalProfile $profile, AdminAnimalProfileService $service): View
    {
        return view('admin.animals.show', $service->showViewData($profile));
    }

    public function edit(AnimalProfile $profile, AdminAnimalProfileService $service): View
    {
        return view('admin.animals.edit', $service->editViewData($profile));
    }

    public function update(Request $request, AnimalProfile $profile, AdminAnimalProfileService $service): RedirectResponse
    {
        $service->update($request, $profile);

        AdminActivityLogger::log('animal_profile', $profile->id, 'updated', 'تعديل محتوى تعريفي');

        return redirect()
            ->route('admin.animals.index')
            ->with('success', 'تم حفظ التعديلات.');
    }

    public function toggleVisibility(AnimalProfile $profile, AdminAnimalProfileService $service): RedirectResponse
    {
        $profile = $service->toggleVisibility($profile);

        $summary = $profile->is_visible
            ? 'إظهار محتوى حيوان للزوار'
            : 'إخفاء محتوى حيوان من تطبيق الزائر';
        AdminActivityLogger::log('animal_profile', $profile->id, 'visibility', $summary);

        return back()->with(
            'success',
            $profile->is_visible ? 'أصبح المحتوى ظاهراً للزوار.' : 'تم إخفاء المحتوى عن الزوار.',
        );
    }

    public function qrImage(Request $request, AnimalProfile $profile, AnimalQrCodeService $qrCodeService): Response
    {
        $profile->load('animal');
        abort_if($profile->animal === null, 404);

        $path = route('visitor.animal', $profile, absolute: false);
        $scanUrl = PublicUrl::absolute($path, $request->query('origin'));

        return response($qrCodeService->svg($scanUrl), 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
