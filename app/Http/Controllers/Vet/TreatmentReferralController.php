<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\TreatmentReferral;
use App\Services\TreatmentReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TreatmentReferralController extends Controller
{
    public function index(Request $request, TreatmentReferralService $service): View
    {
        return view('treatment-referrals.index', array_merge(
            $service->indexViewData($request, '/vet'),
            ['__layout' => 'vet.layout'],
        ));
    }

    public function directorIndex(Request $request, TreatmentReferralService $service): View
    {
        return directorPage('treatment-referrals.index', array_merge(
            $service->indexViewData($request, '/director/vet', readOnly: true),
            ['__layout' => 'director.layout'],
        ));
    }

    public function approve(Request $request, TreatmentReferral $treatmentReferral, TreatmentReferralService $service): RedirectResponse
    {
        $user = $service->vetHeadUser();
        $service->approve($treatmentReferral, $user);

        return redirect()
            ->route('vet.referrals.treatment.index')
            ->with('success', "تم اعتماد الإحالة {$treatmentReferral->referral_number}.");
    }

    public function reject(Request $request, TreatmentReferral $treatmentReferral, TreatmentReferralService $service): RedirectResponse
    {
        $user = $service->vetHeadUser();

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        $service->reject($treatmentReferral, $user, $data['rejection_reason']);

        return redirect()
            ->route('vet.referrals.treatment.index')
            ->with('success', "تم رفض الإحالة {$treatmentReferral->referral_number}.");
    }
}
