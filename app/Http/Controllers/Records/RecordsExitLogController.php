<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Services\RecordsExitLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecordsExitLogController extends Controller
{
    public function index(Request $request, RecordsExitLogService $service): View
    {
        return view('records.logs.exits', $service->indexViewData($request, '/records'));
    }

    public function directorIndex(Request $request, RecordsExitLogService $service): View
    {
        return directorPage('records.logs.exits', $service->indexViewData($request, '/director/records', readOnly: true));
    }
}
