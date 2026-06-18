<?php

namespace App\Http\Controllers\Care;

use App\Enums\HealthCaseFollowUpKind;
use App\Enums\HealthCaseStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\HealthCase;
use App\Models\User;
use App\Services\HealthCaseNotificationService;
use App\Services\HealthCaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class HealthCaseController extends Controller
{
    public function index(Request $request): View
    {
        return view('care.health.index', $this->viewData($request));
    }

    public function directorIndex(Request $request): View
    {
        return directorPage('care.health.index', $this->viewData($request, readOnly: true));
    }

    public function review(Request $request, HealthCase $healthCase, HealthCaseService $service): RedirectResponse
    {
        $user = $this->careHeadUser();
        $service->markReviewed($healthCase, $user);

        return redirect()
            ->route('care.health.index')
            ->with('success', "تم تحديث الحالة {$healthCase->case_number} إلى «تمت المراجعة».");
    }

    public function refer(Request $request, HealthCase $healthCase, HealthCaseService $service): RedirectResponse
    {
        $user = $this->careHeadUser();
        $service->referForTreatment($healthCase, $user);
        $healthCase->load('treatmentReferral');

        return redirect()
            ->route('care.health.index')
            ->with('success', "تم إحالة الحالة {$healthCase->case_number} للعلاج — رقم الإحالة: {$healthCase->treatmentReferral?->referral_number}.");
    }

    public function markNotificationRead(
        Request $request,
        HealthCase $healthCase,
        HealthCaseNotificationService $notifier,
    ): RedirectResponse {
        $user = $this->careHeadUser();
        $notifier->markAsReadForUser($healthCase, $user);

        return redirect()->route('care.health.index', ['case' => $healthCase->case_number]);
    }

    public function attachment(HealthCase $healthCase): Response
    {
        $this->careHeadUser();

        if (! $healthCase->attachment_path) {
            abort(404, 'لا يوجد مرفق لهذه الحالة.');
        }

        if (! Storage::disk('public')->exists($healthCase->attachment_path)) {
            abort(404, 'ملف المرفق غير موجود على الخادم.');
        }

        return Storage::disk('public')->response($healthCase->attachment_path);
    }

    /** @return array<string, mixed> */
    private function viewData(Request $request, bool $readOnly = false): array
    {
        $query = HealthCase::query()
            ->with(['animal', 'supervisor', 'treatmentReferral'])
            ->orderByDesc('created_at');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($followUp = $request->query('follow_up')) {
            if (in_array($followUp, array_column(HealthCaseFollowUpKind::cases(), 'value'), true)) {
                $query->where('follow_up_kind', $followUp);
            }
        }

        if ($status = $request->query('status')) {
            if (in_array($status, array_column(HealthCaseStatus::cases(), 'value'), true)) {
                $query->where('status', $status);
            }
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('case_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('animal', function ($animalQuery) use ($search) {
                        $animalQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('species', 'like', "%{$search}%");
                    });
            });
        }

        $cases = $query->paginate(15)->withQueryString();
        $portalBase = $readOnly ? '/director/care' : '/care';

        return [
            'cases' => $cases,
            'readOnly' => $readOnly,
            'highlightCase' => $request->query('case'),
            'healthCasesForJs' => $this->healthCasesForJs($cases, $portalBase),
            'portalBase' => $portalBase,
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'follow_up' => $request->query('follow_up', ''),
                'status' => $request->query('status', ''),
            ],
        ];
    }

    private function careHeadUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== UserRole::CareHead->value) {
            abort(403, 'هذا الإجراء مخصص لرئيس قسم الرعاية والتغذية.');
        }

        return $user;
    }

    /** @return array<string, array<string, mixed>> */
    private function healthCasesForJs($cases, string $portalBase): array
    {
        return $cases->getCollection()->mapWithKeys(function (HealthCase $case) use ($portalBase) {
            $animal = $case->animal;

            return [$case->case_number => [
                'case_number' => $case->case_number,
                'status' => $case->status->value,
                'status_label' => $case->status->label(),
                'follow_up_kind' => $case->follow_up_kind->value,
                'follow_up_label' => $case->follow_up_kind->label(),
                'description' => $case->description,
                'animal_code' => $animal?->code,
                'animal_name' => $animal?->name,
                'animal_species' => $animal?->species,
                'animal_gender' => $animal?->gender,
                'animal_mark' => $animal?->distinguishing_marks,
                'group' => $case->group,
                'supervisor' => $case->supervisor?->name,
                'date' => $case->created_at?->format('Y-m-d'),
                'has_attachment' => $case->has_attachment,
                'attachment_url' => $case->has_attachment
                    ? $portalBase.'/health/'.$case->case_number.'/attachment'
                    : null,
                'referral_number' => $case->treatmentReferral?->referral_number,
            ]];
        })->all();
    }
}
