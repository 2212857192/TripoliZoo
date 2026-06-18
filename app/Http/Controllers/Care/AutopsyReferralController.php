<?php

namespace App\Http\Controllers\Care;

use App\Http\Controllers\Controller;
use App\Services\AutopsyReferralService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutopsyReferralController extends Controller
{
    public function index(Request $request, AutopsyReferralService $service): View
    {
        return view('autopsy-referrals.index', array_merge(
            $service->indexViewData($request, '/care', readOnly: true),
            ['__layout' => 'care.layout'],
        ));
    }

    public function directorIndex(Request $request, AutopsyReferralService $service): View
    {
        return directorPage('autopsy-referrals.index', array_merge(
            $service->indexViewData($request, '/director/care', readOnly: true),
            ['__layout' => 'director.layout'],
        ));
    }
}
