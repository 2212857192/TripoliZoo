<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.tickets.index', [
            'ticketTypes' => TicketType::orderBy('target_description')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $ticket = TicketType::create($data);

        AdminActivityLogger::log('ticket_type', $ticket->id, 'created', "إضافة فئة تذكرة: {$ticket->displayLabel()}");

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'تم حفظ فئة التذكرة.');
    }

    public function edit(TicketType $ticket): View
    {
        return view('admin.tickets.edit', ['ticket' => $ticket]);
    }

    public function update(Request $request, TicketType $ticket): RedirectResponse
    {
        $ticket->update($this->validated($request));

        AdminActivityLogger::log('ticket_type', $ticket->id, 'updated', "تعديل فئة تذكرة: {$ticket->displayLabel()}");

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'تم تحديث فئة التذكرة.');
    }

    public function toggle(TicketType $ticket): RedirectResponse
    {
        $ticket->update(['is_active' => ! $ticket->is_active]);

        $action = $ticket->is_active ? 'تفعيل' : 'تعطيل';
        AdminActivityLogger::log('ticket_type', $ticket->id, 'status', "{$action} فئة: {$ticket->displayLabel()}");

        $message = $ticket->is_active
            ? 'تم تفعيل التذكرة.'
            : 'تم إيقاف التذكرة.';

        return back()->with('success', $message);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'target_description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'visitor_nationality' => ['required', Rule::in(['مواطن', 'أجنبي'])],
            'visitor_age_group' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ], [
            'target_description.required' => 'يرجى إدخال الفئة.',
            'visitor_age_group.required' => 'يرجى إدخال العمر.',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
