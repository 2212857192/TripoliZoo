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
            'ticketTypes' => TicketType::orderBy('name')->get(),
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

        AdminActivityLogger::log('ticket_type', $ticket->id, 'created', "إضافة فئة تذكرة: {$ticket->name}");

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

        AdminActivityLogger::log('ticket_type', $ticket->id, 'updated', "تعديل فئة تذكرة: {$ticket->name}");

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', 'تم تحديث فئة التذكرة.');
    }

    public function toggle(TicketType $ticket): RedirectResponse
    {
        $ticket->update(['is_active' => ! $ticket->is_active]);

        $action = $ticket->is_active ? 'تفعيل' : 'تعطيل';
        AdminActivityLogger::log('ticket_type', $ticket->id, 'status', "{$action} فئة: {$ticket->name}");

        return back()->with('success', 'تم تحديث حالة التذكرة.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'target_description' => ['nullable', 'string', 'max:255'],
            'visitor_nationality' => ['required', Rule::in(['مواطن', 'أجنبي'])],
            'visitor_age_group' => ['required', Rule::in(['بالغ', 'طفل', 'طالب'])],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
