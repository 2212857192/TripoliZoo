<?php

namespace App\Http\Controllers\Care;

use App\Enums\AnimalStatus;
use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Services\BirthRegistrationNotificationService;
use App\Services\BirthRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class BirthRegistrationController extends Controller
{
    public function index(Request $request, BirthRegistrationService $service): View
    {
        $service->promoteAllExpired();

        return view('care.births.index', $this->viewData($request, $service));
    }

    public function directorIndex(Request $request, BirthRegistrationService $service): View
    {
        $service->promoteAllExpired();

        return directorPage('care.births.index', $this->viewData($request, $service, readOnly: true));
    }

    public function markNotificationRead(
        Request $request,
        int $birthRegistration,
        BirthRegistrationService $service,
        BirthRegistrationNotificationService $notifier,
    ): RedirectResponse {
        $user = $service->careHeadUser();
        $registration = \App\Models\BirthRegistration::query()->findOrFail($birthRegistration);
        $notifier->markAsReadForUser($registration, $user);

        return redirect()->route('care.births.index', ['animal' => $request->query('animal')]);
    }

    public function photo(Animal $animal, BirthRegistrationService $service): Response
    {
        $service->careHeadUser();

        if (! $animal->birth_registration_id || ! $animal->photo_path) {
            abort(404, 'لا توجد صورة لهذا المولود.');
        }

        if (! Storage::disk('public')->exists($animal->photo_path)) {
            abort(404, 'ملف الصورة غير موجود على الخادم.');
        }

        return Storage::disk('public')->response($animal->photo_path);
    }

    public function directorPhoto(Animal $animal, BirthRegistrationService $service): Response
    {
        abort_unless(auth()->user()?->hasPortal(\App\Enums\Portal::Director), 403);

        if (! $animal->birth_registration_id || ! $animal->photo_path) {
            abort(404, 'لا توجد صورة لهذا المولود.');
        }

        if (! Storage::disk('public')->exists($animal->photo_path)) {
            abort(404, 'ملف الصورة غير موجود على الخادم.');
        }

        return Storage::disk('public')->response($animal->photo_path);
    }

    /** @return array<string, mixed> */
    private function viewData(Request $request, BirthRegistrationService $service, bool $readOnly = false): array
    {
        $query = Animal::withQuarantine()
            ->with(['mother', 'birthRegistration.supervisor'])
            ->whereNotNull('birth_registration_id')
            ->orderByDesc('birth_date')
            ->orderByDesc('created_at');

        if ($status = $request->query('status')) {
            if ($status === 'monitoring') {
                $query->where('status', AnimalStatus::UnderBirthFollowUp->value);
            } elseif ($status === 'completed') {
                $query->where('status', AnimalStatus::Active->value);
            } elseif ($status === 'deceased') {
                $query->whereIn('status', [
                    AnimalStatus::Dead->value,
                    AnimalStatus::PendingMortalityApproval->value,
                ]);
            }
        }

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhere('species', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('mother', function ($motherQuery) use ($search) {
                        $motherQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        $newborns = $query->paginate(15)->withQueryString();
        $portalBase = $readOnly ? '/director/care' : '/care';

        return [
            'newborns' => $newborns,
            'readOnly' => $readOnly,
            'highlightAnimal' => $request->query('animal'),
            'newbornsForJs' => $this->newbornsForJs($newborns, $service, $portalBase),
            'portalBase' => $portalBase,
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'status' => $request->query('status', ''),
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    private function newbornsForJs($newborns, BirthRegistrationService $service, string $portalBase): array
    {
        return $newborns->getCollection()->mapWithKeys(function (Animal $animal) use ($service, $portalBase) {
            $mother = $animal->mother;
            $supervisor = $animal->birthRegistration?->supervisor;
            $monitoring = $service->isMonitoring($animal);
            $daysRemaining = $service->daysRemaining($animal);
            $displayStatus = $service->displayStatus($animal);

            return [$animal->code => [
                'code' => $animal->code,
                'name' => $animal->name,
                'species' => $animal->species,
                'group' => $animal->group,
                'gender' => $animal->gender,
                'mother_code' => $mother?->code,
                'mother_name' => $mother?->name,
                'birth_date' => $animal->birth_date?->format('Y-m-d'),
                'days_remaining' => $daysRemaining,
                'days_label' => $this->daysLabel($daysRemaining, $monitoring),
                'status' => $displayStatus['key'],
                'status_label' => $displayStatus['label'],
                'supervisor' => $supervisor?->name,
                'mark' => $animal->distinguishing_marks ?: '—',
                'notes' => $animal->registration_note ?: '—',
                'has_photo' => (bool) $animal->photo_path,
                'photo_url' => $animal->photo_path
                    ? $portalBase.'/births/'.$animal->code.'/photo'
                    : null,
            ]];
        })->all();
    }

    private function daysLabel(?int $daysRemaining, bool $monitoring): string
    {
        if (! $monitoring) {
            return '—';
        }

        if ($daysRemaining === null) {
            return '—';
        }

        if ($daysRemaining === 0) {
            return 'انتهت المدة ⚠️';
        }

        if ($daysRemaining === 1) {
            return 'يوم واحد';
        }

        if ($daysRemaining <= 3) {
            return "{$daysRemaining} أيام ⚠️";
        }

        return "{$daysRemaining} يوماً";
    }
}
