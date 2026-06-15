<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\AnimalProfile;
use App\Models\MapLocation;
use App\Models\TicketType;
use App\Models\User;
use App\Models\VisitSetting;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $employees = User::employees()->get();

        $activeEmployees = $employees->where('status', 'active')->count();
        $inactiveEmployees = $employees->where('status', 'inactive')->count();

        $ticketTypes = TicketType::all();
        $profiles = AnimalProfile::with('animal')->get();

        return view('admin.dashboard', [
            'employeeCount' => $employees->count(),
            'activeEmployees' => $activeEmployees,
            'inactiveEmployees' => $inactiveEmployees,
            'ticketTypeCount' => $ticketTypes->count(),
            'activeTicketTypes' => $ticketTypes->where('is_active', true)->count(),
            'inactiveTicketTypes' => $ticketTypes->where('is_active', false)->count(),
            'profileCount' => $profiles->count(),
            'visibleProfiles' => $profiles->where('is_visible', true)->count(),
            'hiddenProfiles' => $profiles->where('is_visible', false)->count(),
            'mapLocationCount' => MapLocation::count(),
            'visitSettings' => VisitSetting::current(),
            'recentActivities' => AdminActivityLog::with('user')
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
