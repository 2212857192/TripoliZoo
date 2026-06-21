<?php

namespace App\Http\Controllers\Api;

use App\Enums\HealthCaseFollowUpKind;
use App\Enums\AnimalStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\HealthCaseResource;
use App\Models\Animal;
use App\Models\HealthCase;
use App\Models\User;
use App\Services\AnimalLifecycleService;
use App\Services\HealthCaseService;
use App\Services\HealthReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupervisorHealthCaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $cases = HealthCase::query()
            ->with(['animal', 'treatmentReferral'])
            ->where('supervisor_id', $supervisor->id)
            ->when($request->query('date'), function ($query, string $date) {
                $query->whereDate('created_at', $date);
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => HealthCaseResource::collection($cases),
        ]);
    }

    public function show(Request $request, HealthCase $healthCase): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);
        $this->authorizeCase($healthCase, $supervisor);

        $healthCase->load(['animal', 'treatmentReferral']);

        return response()->json([
            'data' => new HealthCaseResource($healthCase),
        ]);
    }

    public function store(Request $request, HealthCaseService $service, HealthReportService $healthReports): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $data = $request->validate([
            'animal_code' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:2000'],
            'follow_up_kind' => ['required', Rule::in(array_column(HealthCaseFollowUpKind::cases(), 'value'))],
            'animal_notes' => [
                Rule::requiredIf($request->input('follow_up_kind') === HealthCaseFollowUpKind::NeedsReferral->value),
                'nullable',
                'string',
                'max:2000',
            ],
            'attachment' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $animal = $healthReports->findRegisteredAnimalForGroup(
            $data['animal_code'],
            $supervisor->assigned_group,
        );

        if (! $animal) {
            $blockedAnimal = Animal::withQuarantine()
                ->where('code', $data['animal_code'])
                ->where('group', $supervisor->assigned_group)
                ->whereIn('status', [
                    AnimalStatus::Dead->value,
                    AnimalStatus::PendingMortalityApproval->value,
                ])
                ->first();

            if ($blockedAnimal) {
                $message = $blockedAnimal->status === AnimalStatus::PendingMortalityApproval->value
                    ? AnimalLifecycleService::PENDING_MORTALITY_MESSAGE
                    : AnimalLifecycleService::INACTIVE_MESSAGE;

                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return response()->json([
                'message' => 'الحيوان غير موجود في مجموعتك أو غير متاح للتسجيل.',
            ], 422);
        }

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('health-cases', 'public')
            : null;

        $healthCase = $service->createCase(
            $supervisor,
            $animal,
            $data['description'],
            HealthCaseFollowUpKind::from($data['follow_up_kind']),
            $attachmentPath,
            $data['animal_notes'] ?? null,
        );

        return response()->json([
            'message' => 'تم تسجيل الحالة الصحية وإرسالها لقسم الرعاية.',
            'data' => new HealthCaseResource($healthCase->load(['animal', 'treatmentReferral'])),
        ], 201);
    }

    private function supervisorUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->role !== UserRole::GroupSupervisor->value || ! $user->assigned_group) {
            abort(403, 'هذا المسار مخصص لمشرفي المجموعات.');
        }

        return $user;
    }

    private function authorizeCase(HealthCase $healthCase, User $supervisor): void
    {
        if ($healthCase->supervisor_id !== $supervisor->id) {
            abort(403, 'ليس لديك صلاحية على هذه الحالة.');
        }
    }
}
