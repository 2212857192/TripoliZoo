<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Services\RecordsDashboardService;
use Illuminate\View\View;

class RecordsDashboardController extends Controller
{
    public function index(RecordsDashboardService $dashboard): View
    {
        return view('records.dashboard', [
            'portalBase' => '/records',
            'stats' => $dashboard->stats(),
            'recentRecords' => $dashboard->recentRecords(portalBase: '/records'),
        ]);
    }
}
