<?php

namespace App\Http\Controllers\Api;

use App\Enums\AnimalStatus;
use App\Enums\MortalityVictimKind;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\MortalityCaseResource;
use App\Models\MortalityCase;
use App\Models\User;
use App\Services\AnimalLifecycleService;
use App\Services\BirthRegistrationService;
use App\Services\HealthReportService;
use App\Services\MortalityCaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupervisorMortalityCaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $cases = MortalityCase::query()
            ->with(['animal'])
            ->where('supervisor_id', $supervisor->id)
            ->when($request->query('date'), function ($query, string $date) {
                $query->whereDate('created_at', $date);
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => MortalityCaseResource::collection($cases),
        ]);
    }

    public function store(Request $request, MortalityCaseService $service, HealthReportService $healthReports, BirthRegistrationService $birthRegistrations): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $data = $request->validate([
            'animal_code' => ['required', 'string', 'max:50'],
            'victim_kind' => ['nullable', Rule::in(array_column(MortalityVictimKind::cases(), 'value'))],
            'animal_type' => ['nullable', 'string', 'max:120'],
            'death_cause' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $animal = $healthReports->findRegisteredAnimalForGroup(
            $data['animal_code'],
            $supervisor->assigned_group,
        );

        if (! $animal) {
            $blockedAnimal = \App\Models\Animal::withQuarantine()
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

        $isNewborn = $birthRegistrations->isMonitoring($animal);

        if (isset($data['victim_kind'])) {
            $victimKind = MortalityVictimKind::from($data['victim_kind']);

            if ($victimKind === MortalityVictimKind::NewbornUnderFollowUp && ! $isNewborn) {
                return response()->json([
                    'message' => 'الحيوان المحدد ليس مولوداً قيد المتابعة.',
                ], 422);
            }

            if ($victimKind === MortalityVictimKind::ZooAnimal && $isNewborn) {
                return response()->json([
                    'message' => 'هذا المولود قيد المتابعة — اختر نوع النافق «مولود قيد المتابعة».',
                ], 422);
            }
        } else {
            $victimKind = $isNewborn
                ? MortalityVictimKind::NewbornUnderFollowUp
                : MortalityVictimKind::ZooAnimal;
        }

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('mortality-cases', 'public')
            : null;

        $mortalityCase = $service->createCase(
            $supervisor,
            $victimKind,
            $data['animal_code'],
            $data['animal_type'] ?? $animal?->species,
            $data['death_cause'] ?? null,
            $data['notes'] ?? null,
            $animal,
            $attachmentPath,
        );

        return response()->json([
            'message' => 'تم تسجيل حالة النفوق وإرسالها لقسم الرعاية.',
            'data' => new MortalityCaseResource($mortalityCase->load(['animal'])),
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
}
