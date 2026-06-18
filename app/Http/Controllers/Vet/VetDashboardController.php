<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Services\VetDashboardService;
use Illuminate\View\View;

class VetDashboardController extends Controller
{
    public function index(VetDashboardService $dashboard): View
    {
        return view('vet.dashboard', [
            'stats' => $dashboard->stats(),
            'recentReferrals' => $dashboard->recentReferrals(),
            'urgentCases' => $dashboard->urgentCases(),
            'referralSummary' => $dashboard->referralSummary(),
            'recentAlerts' => $dashboard->recentAlerts(auth()->user()),
        ]);
    }
}
