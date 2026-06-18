<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Services\RecordsMortalityLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecordsMortalityLogController extends Controller
{
    public function index(Request $request, RecordsMortalityLogService $service): View
    {
        return view('records.logs.mortality', $service->indexViewData($request, '/records'));
    }

    public function directorIndex(Request $request, RecordsMortalityLogService $service): View
    {
        return directorPage('records.logs.mortality', $service->indexViewData($request, '/director/records', readOnly: true));
    }
}
