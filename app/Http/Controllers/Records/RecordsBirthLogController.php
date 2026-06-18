<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Services\RecordsBirthLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecordsBirthLogController extends Controller
{
    public function index(Request $request, RecordsBirthLogService $service): View
    {
        return view('records.logs.births', $service->indexViewData($request, '/records'));
    }

    public function directorIndex(Request $request, RecordsBirthLogService $service): View
    {
        return directorPage('records.logs.births', $service->indexViewData($request, '/director/records', readOnly: true));
    }
}
