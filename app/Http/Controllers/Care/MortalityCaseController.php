<?php

namespace App\Http\Controllers\Care;

use App\Enums\MortalityCaseStatus;
use App\Http\Controllers\Controller;
use App\Models\MortalityCase;
use App\Services\MortalityCaseNotificationService;
use App\Services\MortalityCaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class MortalityCaseController extends Controller
{
    public function index(Request $request): View
    {
        return view('care.mortality.index', $this->viewData($request));
    }

    public function directorIndex(Request $request): View
    {
        return directorPage('care.mortality.index', $this->viewData($request, readOnly: true));
    }

    public function approve(Request $request, MortalityCase $mortalityCase, MortalityCaseService $service): RedirectResponse
    {
        $user = $service->careHeadUser();
        $service->approve($mortalityCase, $user);

        return redirect()
            ->route('care.mortality.index')
            ->with('success', "تم اعتماد حالة النفوق {$mortalityCase->case_number}.");
    }

    public function referForAutopsy(Request $request, MortalityCase $mortalityCase, MortalityCaseService $service): RedirectResponse
    {
        $user = $service->careHeadUser();

        $data = $request->validate([
            'autopsy_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $service->referForAutopsy($mortalityCase, $user, $data['autopsy_reason'] ?? null);

        return redirect()
            ->route('care.mortality.index')
            ->with('success', "تم إحالة حالة النفوق {$mortalityCase->case_number} للتشريح.");
    }

    public function markNotificationRead(
        Request $request,
        MortalityCase $mortalityCase,
        MortalityCaseService $service,
        MortalityCaseNotificationService $notifier,
    ): RedirectResponse {
        $user = $service->careHeadUser();
        $notifier->markAsReadForUser($mortalityCase, $user);

        return redirect()->route('care.mortality.index', ['case' => $mortalityCase->case_number]);
    }

    public function attachment(MortalityCase $mortalityCase, MortalityCaseService $service): Response
    {
        $service->careHeadUser();

        if (! $mortalityCase->attachment_path) {
            abort(404, 'لا يوجد مرفق لهذه الحالة.');
        }

        if (! Storage::disk('public')->exists($mortalityCase->attachment_path)) {
            abort(404, 'ملف المرفق غير موجود على الخادم.');
        }

        return Storage::disk('public')->response($mortalityCase->attachment_path);
    }

    /** @return array<string, mixed> */
    private function viewData(Request $request, bool $readOnly = false): array
    {
        $query = MortalityCase::query()
            ->with(['animal', 'supervisor'])
            ->orderByDesc('death_date')
            ->orderByDesc('created_at');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($status = $request->query('status')) {
            if (in_array($status, array_column(MortalityCaseStatus::cases(), 'value'), true)) {
                $query->where('status', $status);
            }
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('case_number', 'like', "%{$search}%")
                    ->orWhere('subject_code', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
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
            'mortalityCasesForJs' => $this->mortalityCasesForJs($cases, $portalBase),
            'portalBase' => $portalBase,
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'status' => $request->query('status', ''),
            ],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    private function mortalityCasesForJs($cases, string $portalBase): array
    {
        return $cases->getCollection()->mapWithKeys(function (MortalityCase $case) use ($portalBase) {
            $animal = $case->animal;

            return [$case->case_number => [
                'case_number' => $case->case_number,
                'status' => $case->status->value,
                'status_label' => $case->status->label(),
                'animal_code' => $animal?->code ?? $case->subject_code,
                'animal_name' => $animal?->name,
                'animal_species' => $animal?->species ?? $case->subject_type,
                'group' => $case->group,
                'supervisor' => $case->supervisor?->name,
                'death_date' => $case->death_date?->format('Y-m-d'),
                'death_cause' => $case->displayCause(),
                'cause_apparent' => $case->isCauseApparent(),
                'notes' => $case->notes,
                'has_attachment' => $case->has_attachment,
                'attachment_url' => $case->has_attachment
                    ? $portalBase.'/mortality/'.$case->case_number.'/attachment'
                    : null,
                'autopsy_reason' => $case->autopsy_reason,
                'reviewed_at' => $case->reviewed_at?->format('Y-m-d'),
            ]];
        })->all();
    }
}
