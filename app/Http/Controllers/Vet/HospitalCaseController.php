<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Enums\HospitalCaseStatus;
use App\Models\HospitalCase;
use App\Services\HospitalCaseService;
use App\Services\MedicalCaseProcedureService;
use App\Services\TreatmentReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HospitalCaseController extends Controller
{
    public function index(Request $request, HospitalCaseService $service): View
    {
        return view('vet.cases.hospital', $service->indexViewData($request, '/vet'));
    }

    public function directorIndex(Request $request, HospitalCaseService $service): View
    {
        return directorPage('vet.cases.hospital', $service->indexViewData($request, '/director/vet', readOnly: true));
    }

    public function show(HospitalCase $hospitalCase): View
    {
        return view('vet.cases.hospital.show', $this->caseShowData($hospitalCase));
    }

    public function directorShow(HospitalCase $hospitalCase): View
    {
        return directorPage('vet.cases.hospital.show', $this->caseShowData($hospitalCase, readOnly: true));
    }

    public function approveDischarge(
        HospitalCase $hospitalCase,
        MedicalCaseProcedureService $procedureService,
        TreatmentReferralService $referralService,
    ): RedirectResponse {
        $vetHead = $referralService->vetHeadUser();
        $procedureService->issueVetHeadDecision($hospitalCase, $vetHead, 'discharge');

        return redirect()
            ->route('vet.cases.hospital.show', $hospitalCase)
            ->with('success', 'تم اعتماد خروج الحيوان من المستشفى.');
    }

    public function approveSlaughter(
        HospitalCase $hospitalCase,
        MedicalCaseProcedureService $procedureService,
        TreatmentReferralService $referralService,
    ): RedirectResponse {
        $vetHead = $referralService->vetHeadUser();
        $procedureService->issueVetHeadDecision($hospitalCase, $vetHead, 'slaughter');

        return redirect()
            ->route('vet.cases.hospital.show', $hospitalCase)
            ->with('success', 'تم اعتماد قرار الذبح الاضطراري.');
    }

    public function issueDecision(
        Request $request,
        HospitalCase $hospitalCase,
        MedicalCaseProcedureService $procedureService,
        TreatmentReferralService $referralService,
    ): RedirectResponse {
        $vetHead = $referralService->vetHeadUser();

        $data = $request->validate([
            'decision' => ['required', 'in:discharge,slaughter'],
            'note' => ['nullable', 'string', 'max:2000'],
        ], [
            'decision.required' => 'اختر نوع القرار الطبي.',
            'decision.in' => 'نوع القرار غير صالح.',
        ]);

        $procedureService->issueVetHeadDecision(
            $hospitalCase,
            $vetHead,
            $data['decision'],
            $data['note'] ?? null,
        );

        $message = match ($data['decision']) {
            'discharge' => 'تم اعتماد خروج الحيوان بعد العلاج.',
            'slaughter' => 'تم اعتماد قرار الذبح الاضطراري.',
        };

        return redirect()
            ->route('vet.cases.hospital.show', $hospitalCase)
            ->with('success', $message);
    }

    /** @return array<string, mixed> */
    private function caseShowData(HospitalCase $hospitalCase, bool $readOnly = false): array
    {
        $hospitalCase->load(['animal', 'admitter', 'healthCase.supervisor', 'treatmentReferral', 'procedures.nutritionRecommendation', 'procedures.recorder']);

        $animal = $hospitalCase->animal;
        $status = $hospitalCase->status;

        $followUps = $hospitalCase->procedures
            ->sortByDesc('recorded_at')
            ->values()
            ->map(function ($procedure) {
                $nutrition = $procedure->nutritionRecommendation;

                return [
                    'date' => $procedure->recorded_at?->format('Y-m-d — H:i') ?? '—',
                    'vet' => $procedure->recorder?->name ?? '—',
                    'diagnosis' => $procedure->diagnosis,
                    'treatment' => $procedure->treatment,
                    'note' => $procedure->note ?? '',
                    'nutrition' => $nutrition ? [
                        'text' => $nutrition->recommendation_text,
                        'start' => $nutrition->start_date?->format('Y-m-d'),
                        'end' => $nutrition->end_date?->format('Y-m-d'),
                    ] : null,
                    'status' => $procedure->case_result->label(),
                    'statusClass' => match ($procedure->case_result->value) {
                        'ready_for_discharge' => 'follow-status-ready',
                        'no_response' => 'follow-status-no-response',
                        default => 'follow-status-watch',
                    },
                ];
            })
            ->all();

        return [
            'id' => $hospitalCase->case_number,
            'hospitalCase' => $hospitalCase,
            'canIssueDecision' => ! $readOnly
                && auth()->user()?->isVetHead()
                && in_array($status, HospitalCaseStatus::awaitingVetHeadDecision(), true),
            'recommendedDecision' => match ($status) {
                HospitalCaseStatus::PendingDischargeApproval => 'discharge',
                HospitalCaseStatus::PendingSlaughterApproval => 'slaughter',
                default => null,
            },
            'caseData' => [
                'statusClass' => $status->headerStatusClass(),
                'statusText' => $status->label(),
                'vet' => $hospitalCase->admitter?->name ?? '—',
                'reason' => $hospitalCase->chief_complaint,
                'notes' => $hospitalCase->healthCase?->description ?? $hospitalCase->chief_complaint,
                'animalId' => $animal?->code ? '#'.$animal->code : '—',
                'animalType' => $animal?->species ?? '—',
                'animalName' => $animal?->name ?? '',
                'mark' => $animal?->distinguishing_marks ?? '',
                'animalEmoji' => '🐾',
                'animalPhotoUrl' => $animal?->displayPhotoUrl(),
                'gender' => $animal?->gender ?? '—',
                'age' => $animal?->formattedAge() ?? '—',
                'group' => $hospitalCase->group,
                'admissionDate' => $hospitalCase->admitted_at?->format('Y-m-d') ?? '—',
                'dischargeDate' => $hospitalCase->closed_at?->format('Y-m-d') ?? '',
                'followUps' => $followUps,
            ],
        ];
    }
}
