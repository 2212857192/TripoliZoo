<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MortalityCase;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MortalityCaseAttachmentController extends Controller
{
    public function show(MortalityCase $mortalityCase): Response
    {
        if (! $mortalityCase->attachment_path) {
            abort(404, 'لا يوجد مرفق لهذه الحالة.');
        }

        if (! Storage::disk('public')->exists($mortalityCase->attachment_path)) {
            abort(404, 'ملف المرفق غير موجود على الخادم.');
        }

        return Storage::disk('public')->response($mortalityCase->attachment_path);
    }
}
