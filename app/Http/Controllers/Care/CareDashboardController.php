<?php

namespace App\Http\Controllers\Care;

use App\Http\Controllers\Controller;
use App\Services\CareDashboardService;
use App\Services\PortalDashboardService;
use Illuminate\View\View;

class CareDashboardController extends Controller
{
    public function index(CareDashboardService $careDashboard, PortalDashboardService $portalDashboard): View
    {
        return view('care.dashboard', [
            'stats' => $careDashboard->stats(),
            'reviewItems' => $careDashboard->reviewItems(),
            'referralSummary' => $careDashboard->referralSummary(),
            'recentAlerts' => $careDashboard->recentAlerts(auth()->user()),
            'receivingDelays' => $portalDashboard->recentReceivingDelays(),
        ]);
    }
}
