<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OperationalNote;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class OperationalNoteAttachmentController extends Controller
{
    public function show(OperationalNote $operationalNote): Response
    {
        if (! $operationalNote->attachment_path) {
            abort(404, 'لا يوجد مرفق لهذه الملاحظة.');
        }

        if (! Storage::disk('public')->exists($operationalNote->attachment_path)) {
            abort(404, 'ملف المرفق غير موجود على الخادم.');
        }

        return Storage::disk('public')->response($operationalNote->attachment_path);
    }
}
