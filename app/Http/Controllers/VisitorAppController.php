<?php

namespace App\Http\Controllers;

use App\Models\AnimalProfile;
use App\Models\MapLocation;
use App\Models\TicketType;
use App\Models\VisitSetting;
use Illuminate\View\View;

class VisitorAppController extends Controller
{
    public function index(): View
    {
        return view('visitor-app', [
            'settings' => VisitSetting::current(),
            'profiles' => AnimalProfile::with('animal')
                ->where('is_visible', true)
                ->get(),
            'locations' => MapLocation::where('is_active', true)->get(),
            'ticketTypes' => TicketType::where('is_active', true)->orderBy('price')->get(),
        ]);
    }

    public function profile(AnimalProfile $profile): View
    {
        abort_unless($profile->is_visible, 404);
        $profile->load('animal');

        return view('visitor.animal-profile', [
            'profile' => $profile,
            'settings' => VisitSetting::current(),
        ]);
    }
}
