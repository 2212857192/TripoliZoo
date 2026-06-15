<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitSetting;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitInfoController extends Controller
{
    public function show(): View
    {
        return view('admin.visit-info.show', [
            'settings' => VisitSetting::current(),
        ]);
    }

    public function edit(): View
    {
        return view('admin.visit-info.edit', [
            'settings' => VisitSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'status_text' => ['nullable', 'string', 'max:500'],
            'status_visible' => ['sometimes', 'boolean'],
            'urgent_alert' => ['nullable', 'string'],
            'ambulance_phone' => ['nullable', 'string', 'max:50'],
            'security_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'entry_instructions' => ['nullable', 'string'],
            'open_time' => ['nullable', 'date_format:H:i'],
            'close_time' => ['nullable', 'date_format:H:i'],
            'last_ticket_time_note' => ['nullable', 'string', 'max:255'],
            'working_days' => ['nullable', 'array'],
            'working_days.*' => ['boolean'],
        ]);

        $settings = VisitSetting::current();
        $data['status_visible'] = $request->boolean('status_visible');
        $data['updated_by'] = $request->user()->id;

        $settings->update($data);

        AdminActivityLogger::log('visit_settings', $settings->id, 'updated', 'تحديث معلومات الزيارة');

        return redirect()
            ->route('admin.visit-info.show')
            ->with('success', 'تم حفظ ونشر معلومات الزيارة.');
    }
}
