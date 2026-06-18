<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Services\RecordsEntryLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecordsEntryLogController extends Controller
{
    public function index(Request $request, RecordsEntryLogService $service): View
    {
        return view('records.logs.entries', $service->indexViewData($request, '/records'));
    }

    public function directorIndex(Request $request, RecordsEntryLogService $service): View
    {
        return directorPage('records.logs.entries', $service->indexViewData($request, '/director/records', readOnly: true));
    }
}
