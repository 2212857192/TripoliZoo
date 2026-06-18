<?php

namespace App\Http\Controllers\Api;

use App\Enums\MedicalCaseResult;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorMedicalCaseResource;
use App\Models\Animal;
use App\Models\User;
use App\Services\DoctorMedicalCaseService;
use App\Services\FieldCaseService;
use App\Services\HealthReportService;
use App\Services\MedicalCaseProcedureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorMedicalCaseController extends Controller
{
    public function animals(Request $request, HealthReportService $service): JsonResponse
    {
        $vet = $this->veterinarianUser($request);

        $animals = $service->animalsForVet($vet)->map(fn (Animal $animal) => [
            'id' => $animal->code,
            'name' => $animal->name,
            'type' => $animal->species,
            'label' => $animal->name
                ? "{$animal->code} — {$animal->name} ({$animal->species})"
                : "{$animal->code} — {$animal->species}",
        ]);

        return response()->json(['data' => $animals->values()]);
    }

    public function index(Request $request, DoctorMedicalCaseService $service): JsonResponse
    {
        $vet = $this->veterinarianUser($request);

        $cases = $service->listForVet($vet)->pluck('resource');

        return response()->json([
            'data' => DoctorMedicalCaseResource::collection($cases),
        ]);
    }

    public function show(Request $request, string $caseKey, DoctorMedicalCaseService $service): JsonResponse
    {
        $vet = $this->veterinarianUser($request);
        $resource = $service->findForVet($vet, $caseKey);

        if (! $resource) {
            abort(404, 'الحالة غير موجودة.');
        }

        return response()->json([
            'data' => $resource,
        ]);
    }

    public function storeFieldCase(
        Request $request,
        FieldCaseService $fieldCaseService,
        HealthReportService $service,
    ): JsonResponse
    {
        $vet = $this->veterinarianUser($request);

        $data = $request->validate([
            'animal_code' => ['required', 'string', 'max:50'],
            'open_reason' => ['required', 'string', 'max:2000'],
            'initial_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $animal = $service->findRegisteredAnimalForGroup(
            $data['animal_code'],
            $vet->assigned_group,
            excludeInHospital: true,
        );

        if (! $animal) {
            return response()->json([
                'message' => 'الحيوان غير موجود في مجموعتك أو غير متاح.',
            ], 422);
        }

        $fieldCase = $fieldCaseService->openManually(
            $vet,
            $animal,
            $data['open_reason'],
            $data['initial_note'] ?? null,
        );

        return response()->json([
            'message' => 'تم فتح الحالة الطبية الميدانية بنجاح.',
            'data' => DoctorMedicalCaseResource::fromFieldCase($fieldCase),
        ], 201);
    }

    public function storeProcedure(
        Request $request,
        string $caseKey,
        MedicalCaseProcedureService $procedureService,
    ): JsonResponse {
        $vet = $this->veterinarianUser($request);

        $data = $request->validate([
            'diagnosis' => ['required', 'string', 'max:2000'],
            'treatment' => ['required', 'string', 'max:2000'],
            'note' => ['nullable', 'string', 'max:2000'],
            'case_result' => ['required', 'in:'.implode(',', array_column(MedicalCaseResult::cases(), 'value'))],
            'nutrition' => ['nullable', 'array'],
            'nutrition.recommendation_text' => ['required_with:nutrition', 'string', 'max:2000'],
            'nutrition.start_date' => ['required_with:nutrition', 'date'],
            'nutrition.end_date' => ['required_with:nutrition', 'date', 'after_or_equal:nutrition.start_date'],
            'nutrition.note' => ['nullable', 'string', 'max:2000'],
        ]);

        $case = $procedureService->resolveCase($caseKey, $vet);

        $procedureService->record(
            $case,
            $vet,
            $data['diagnosis'],
            $data['treatment'],
            MedicalCaseResult::from($data['case_result']),
            $data['note'] ?? null,
            $data['nutrition'] ?? null,
        );

        $case->refresh()->load(['animal', 'procedures.nutritionRecommendation', 'procedures.recorder']);

        $resource = $case instanceof \App\Models\FieldCase
            ? DoctorMedicalCaseResource::fromFieldCase($case)
            : DoctorMedicalCaseResource::fromHospitalCase($case);

        return response()->json([
            'message' => 'تم تسجيل الإجراء الطبي بنجاح.',
            'data' => $resource,
        ], 201);
    }

    public function closeFieldCase(
        Request $request,
        string $caseKey,
        MedicalCaseProcedureService $procedureService,
    ): JsonResponse {
        $vet = $this->veterinarianUser($request);

        $case = $procedureService->resolveCase($caseKey, $vet);

        if (! $case instanceof \App\Models\FieldCase) {
            abort(422, 'إغلاق الحالة متاح للحالات الميدانية فقط.');
        }

        $closed = $procedureService->closeFieldCase($case, $vet);

        return response()->json([
            'message' => 'تم إغلاق الحالة الميدانية بنجاح.',
            'data' => DoctorMedicalCaseResource::fromFieldCase($closed),
        ]);
    }

    private function veterinarianUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->role !== UserRole::Veterinarian->value || ! $user->assigned_group) {
            abort(403, 'هذا المسار مخصص للأطباء البيطريين المسندين لمجموعة.');
        }

        return $user;
    }
}
