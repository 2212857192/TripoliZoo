<?php

namespace App\Http\Controllers\Api;

use App\Enums\HealthReportStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\HealthReportResource;
use App\Models\HealthReport;
use App\Models\User;
use App\Services\HealthReportNotificationService;
use App\Services\HealthReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorHealthReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vet = $this->veterinarianUser($request);
        $status = $request->query('status');

        $query = HealthReport::query()
            ->with(['animal', 'supervisor', 'assignedVet'])
            ->where('group', $vet->assigned_group)
            ->orderByDesc('created_at');

        if (is_string($status) && in_array($status, array_column(HealthReportStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        return response()->json([
            'data' => HealthReportResource::collection($query->get()),
        ]);
    }

    public function show(
        Request $request,
        HealthReport $healthReport,
        HealthReportService $service,
        HealthReportNotificationService $notifier
    ): JsonResponse {
        $vet = $this->veterinarianUser($request);
        $this->authorizeReport($healthReport, $vet);

        if ($healthReport->status === HealthReportStatus::Sent) {
            $healthReport = $service->markReceived($healthReport, $vet);
        }

        $notifier->markAsReadForUser($healthReport, $vet);

        $healthReport->load(['animal', 'supervisor', 'assignedVet']);

        return response()->json([
            'data' => new HealthReportResource($healthReport),
        ]);
    }

    public function close(Request $request, HealthReport $healthReport, HealthReportService $service): JsonResponse
    {
        $vet = $this->veterinarianUser($request);
        $this->authorizeReport($healthReport, $vet);

        if ($healthReport->status === HealthReportStatus::Closed) {
            return response()->json(['message' => 'تم إغلاق هذا البلاغ مسبقاً.'], 422);
        }

        $data = $request->validate([
            'doctor_note' => ['required', 'string', 'max:2000'],
            'field_case_opened' => ['sometimes', 'boolean'],
        ]);

        $report = $service->closeReport(
            $healthReport,
            $vet,
            $data['doctor_note'],
            (bool) ($data['field_case_opened'] ?? false),
        );

        return response()->json([
            'message' => 'تم إغلاق البلاغ بنجاح.',
            'data' => new HealthReportResource($report->load(['animal', 'supervisor', 'assignedVet'])),
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

    private function authorizeReport(HealthReport $report, User $vet): void
    {
        if ($report->group !== $vet->assigned_group) {
            abort(403, 'هذا البلاغ لا يخص مجموعتك.');
        }
    }
}
