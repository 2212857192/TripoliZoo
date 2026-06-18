<?php

namespace App\Http\Controllers\Care;

use App\Http\Controllers\Controller;
use App\Services\TreatmentReferralService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TreatmentReferralController extends Controller
{
    public function index(Request $request, TreatmentReferralService $service): View
    {
        return view('treatment-referrals.index', array_merge(
            $service->indexViewData($request, '/care', readOnly: true),
            ['__layout' => 'care.layout'],
        ));
    }

    public function directorIndex(Request $request, TreatmentReferralService $service): View
    {
        return directorPage('treatment-referrals.index', array_merge(
            $service->indexViewData($request, '/director/care', readOnly: true),
            ['__layout' => 'director.layout'],
        ));
    }
}
