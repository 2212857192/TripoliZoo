<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\HealthReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class HealthReportAttachmentController extends Controller
{
    public function show(Request $request, HealthReport $healthReport): Response
    {
        /** @var User $user */
        $user = $request->user();
        $this->authorizeAccess($user, $healthReport);

        if (! $healthReport->attachment_path) {
            abort(404, 'لا يوجد مرفق لهذا البلاغ.');
        }

        if (! Storage::disk('public')->exists($healthReport->attachment_path)) {
            abort(404, 'ملف المرفق غير موجود على الخادم.');
        }

        return Storage::disk('public')->response($healthReport->attachment_path);
    }

    private function authorizeAccess(User $user, HealthReport $report): void
    {
        if ($user->role === UserRole::GroupSupervisor->value
            && $report->supervisor_id === $user->id) {
            return;
        }

        if ($user->role === UserRole::Veterinarian->value
            && $user->assigned_group === $report->group) {
            return;
        }

        abort(403, 'ليس لديك صلاحية لعرض هذا المرفق.');
    }
}
