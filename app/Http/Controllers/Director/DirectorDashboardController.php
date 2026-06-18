<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Services\DirectorDashboardService;
use Illuminate\View\View;

class DirectorDashboardController extends Controller
{
    public function index(DirectorDashboardService $dashboard): View
    {
        $alerts = $dashboard->feedAlerts();

        return view('director.dashboard', [
            'overviewStats' => $dashboard->overviewStats(),
            'todaySummary' => $dashboard->todaySummary(),
            'todayDate' => now()->format('Y-m-d'),
            'visits' => $dashboard->visits(),
            'operations' => $dashboard->operations(),
            'recentDecisions' => $dashboard->recentDecisions(),
            'charts' => $dashboard->charts(),
            'feedEvents' => $dashboard->feedEvents(),
            'feedAlerts' => $alerts,
            'feedAlertCount' => $alerts->count(),
        ]);
    }
}
