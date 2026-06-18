<?php

namespace App\Http\Controllers\Api;

use App\Enums\HealthReportStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\HealthReportResource;
use App\Models\Animal;
use App\Models\HealthReport;
use App\Models\User;
use App\Services\HealthReportNotificationService;
use App\Services\HealthReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupervisorHealthReportController extends Controller
{
    public function animals(Request $request, HealthReportService $service): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $animals = $service->animalsForSupervisor($supervisor)->map(fn (Animal $animal) => [
            'id' => $animal->code,
            'name' => $animal->name,
            'type' => $animal->species,
            'label' => $animal->name
                ? "{$animal->code} — {$animal->name} ({$animal->species})"
                : "{$animal->code} — {$animal->species}",
        ]);

        return response()->json(['data' => $animals->values()]);
    }

    public function index(Request $request): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);
        $status = $request->query('status');

        $query = HealthReport::query()
            ->with(['animal', 'assignedVet'])
            ->where('supervisor_id', $supervisor->id)
            ->orderByDesc('created_at');

        if (is_string($status) && in_array($status, array_column(HealthReportStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        return response()->json([
            'data' => HealthReportResource::collection($query->get()),
        ]);
    }

    public function show(Request $request, HealthReport $healthReport): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);
        $this->authorizeReport($healthReport, $supervisor);

        $healthReport->load(['animal', 'assignedVet']);

        return response()->json([
            'data' => new HealthReportResource($healthReport),
        ]);
    }

    public function store(Request $request, HealthReportService $service): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $data = $request->validate([
            'animal_code' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:2000'],
            'is_urgent' => ['sometimes', 'boolean'],
            'attachment' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $animal = $service->findRegisteredAnimalForGroup(
            $data['animal_code'],
            $supervisor->assigned_group,
        );

        if (! $animal) {
            return response()->json([
                'message' => 'الحيوان غير موجود في مجموعتك أو غير متاح للبلاغ.',
            ], 422);
        }

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('health-reports', 'public')
            : null;

        $report = $service->createReport(
            $supervisor,
            $animal,
            $data['description'],
            (bool) ($data['is_urgent'] ?? false),
            $attachmentPath,
        );

        return response()->json([
            'message' => 'تم إرسال البلاغ الصحي بنجاح.',
            'data' => new HealthReportResource($report->load(['animal', 'assignedVet'])),
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

    private function authorizeReport(HealthReport $report, User $supervisor): void
    {
        if ($report->supervisor_id !== $supervisor->id) {
            abort(403, 'ليس لديك صلاحية على هذا البلاغ.');
        }
    }
}
