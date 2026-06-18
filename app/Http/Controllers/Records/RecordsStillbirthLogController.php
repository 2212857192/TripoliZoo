<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Services\RecordsStillbirthLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecordsStillbirthLogController extends Controller
{
    public function index(Request $request, RecordsStillbirthLogService $service): View
    {
        return view('records.logs.stillbirths', $service->indexViewData($request, '/records'));
    }

    public function directorIndex(Request $request, RecordsStillbirthLogService $service): View
    {
        return directorPage('records.logs.stillbirths', $service->indexViewData($request, '/director/records', readOnly: true));
    }
}
