<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\HealthCase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class HealthCaseAttachmentController extends Controller
{
    public function show(Request $request, HealthCase $healthCase): Response
    {
        /** @var User $user */
        $user = $request->user();
        $this->authorizeAccess($user, $healthCase);

        if (! $healthCase->attachment_path) {
            abort(404, 'لا يوجد مرفق لهذه الحالة.');
        }

        if (! Storage::disk('public')->exists($healthCase->attachment_path)) {
            abort(404, 'ملف المرفق غير موجود على الخادم.');
        }

        return Storage::disk('public')->response($healthCase->attachment_path);
    }

    private function authorizeAccess(User $user, HealthCase $healthCase): void
    {
        if ($user->role === UserRole::GroupSupervisor->value
            && $healthCase->supervisor_id === $user->id) {
            return;
        }

        if ($user->role === UserRole::CareHead->value) {
            return;
        }

        abort(403, 'ليس لديك صلاحية لعرض هذا المرفق.');
    }
}
