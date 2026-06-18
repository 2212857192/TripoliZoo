<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitSetting;
use App\Support\VisitSettingPresenter;
use Illuminate\Http\JsonResponse;

class VisitorVisitInfoController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = VisitSetting::current();

        return response()->json([
            'data' => VisitSettingPresenter::toPublicArray($settings),
        ]);
    }
}
