<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Services\RecordsSlaughterLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecordsSlaughterLogController extends Controller
{
    public function index(Request $request, RecordsSlaughterLogService $service): View
    {
        return view('records.logs.slaughter', $service->indexViewData($request, '/records'));
    }

    public function directorIndex(Request $request, RecordsSlaughterLogService $service): View
    {
        return directorPage('records.logs.slaughter', $service->indexViewData($request, '/director/records', readOnly: true));
    }
}
