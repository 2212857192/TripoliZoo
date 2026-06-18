<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\AutopsyReferral;
use App\Services\AutopsyReferralNotificationService;
use App\Services\AutopsyReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AutopsyReferralController extends Controller
{
    public function index(Request $request, AutopsyReferralService $service): View
    {
        return view('autopsy-referrals.index', array_merge(
            $service->indexViewData($request, '/vet'),
            ['__layout' => 'vet.layout'],
        ));
    }

    public function directorIndex(Request $request, AutopsyReferralService $service): View
    {
        return directorPage('autopsy-referrals.index', array_merge(
            $service->indexViewData($request, '/director/vet', readOnly: true),
            ['__layout' => 'director.layout'],
        ));
    }

    public function show(AutopsyReferral $autopsyReferral): View
    {
        $autopsyReferral->load(['animal', 'mortalityCase.supervisor', 'referrer', 'documenter']);

        return view('vet.referrals.autopsy.show', [
            'referral' => $autopsyReferral,
            'readOnly' => false,
            'canDocument' => $autopsyReferral->canBeDocumented(),
        ]);
    }

    public function directorShow(AutopsyReferral $autopsyReferral): View
    {
        $autopsyReferral->load(['animal', 'mortalityCase.supervisor', 'referrer', 'documenter']);

        return directorPage('vet.referrals.autopsy.show', [
            'referral' => $autopsyReferral,
            'readOnly' => true,
            'canDocument' => false,
        ]);
    }

    public function document(Request $request, AutopsyReferral $autopsyReferral, AutopsyReferralService $service): RedirectResponse
    {
        $user = $service->vetHeadUser();

        $data = $request->validate([
            'final_death_cause' => ['required', 'string', 'max:2000'],
            'autopsy_notes' => ['nullable', 'string', 'max:5000'],
            'documented_at' => ['nullable', 'date'],
            'report' => ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,pdf', 'max:10240'],
        ]);

        $reportPath = $request->hasFile('report')
            ? $request->file('report')->store('autopsy-reports', 'public')
            : null;

        $service->document(
            $autopsyReferral,
            $user,
            $data['final_death_cause'],
            $data['autopsy_notes'] ?? null,
            $reportPath,
            $data['documented_at'] ?? null,
        );

        return redirect()
            ->route('vet.referrals.autopsy.show', $autopsyReferral->referral_number)
            ->with('success', "تم توثيق نتيجة التشريح للإحالة {$autopsyReferral->referral_number}.");
    }

    public function report(AutopsyReferral $autopsyReferral): Response
    {
        if (! $autopsyReferral->report_path) {
            abort(404, 'لا يوجد تقرير تشريح لهذه الإحالة.');
        }

        if (! Storage::disk('public')->exists($autopsyReferral->report_path)) {
            abort(404, 'ملف التقرير غير موجود على الخادم.');
        }

        return Storage::disk('public')->response($autopsyReferral->report_path);
    }

    public function markNotificationRead(
        Request $request,
        AutopsyReferral $autopsyReferral,
        AutopsyReferralNotificationService $notifier,
    ): RedirectResponse {
        $user = app(AutopsyReferralService::class)->vetHeadUser();
        $notifier->markAsReadForUser($autopsyReferral, $user);

        return redirect()->route('vet.referrals.autopsy.index', ['referral' => $autopsyReferral->referral_number]);
    }
}
