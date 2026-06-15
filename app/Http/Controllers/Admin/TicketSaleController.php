<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketSale;
use App\Models\TicketType;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketSaleController extends Controller
{
    public function create(): View
    {
        return view('admin.tickets.buy', [
            'ticketTypes' => TicketType::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ticket_type_id' => ['required', 'exists:ticket_types,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'payment_method' => ['required', Rule::in(['نقداً', 'بطاقة'])],
        ]);

        $type = TicketType::findOrFail($data['ticket_type_id']);

        if (! $type->is_active) {
            return back()->withErrors(['ticket_type_id' => 'هذه الفئة غير مفعّلة للبيع.']);
        }

        $unitPrice = $type->price;
        $total = $unitPrice * $data['quantity'];

        $sale = TicketSale::create([
            'ticket_number' => $this->nextTicketNumber(),
            'ticket_type_id' => $type->id,
            'customer_name' => $data['customer_name'] ?: null,
            'quantity' => $data['quantity'],
            'unit_price' => $unitPrice,
            'total_amount' => $total,
            'payment_method' => $data['payment_method'],
            'sold_by' => $request->user()->id,
            'sold_at' => now(),
        ]);

        AdminActivityLogger::log('ticket_sale', $sale->id, 'created', "بيع تذكرة {$sale->ticket_number}");

        return redirect()
            ->route('admin.tickets.show', $sale)
            ->with('success', 'تم إصدار التذكرة بنجاح.');
    }

    public function show(TicketSale $ticket): View
    {
        $ticket->load(['ticketType', 'seller']);

        return view('admin.tickets.show', ['sale' => $ticket]);
    }

    private function nextTicketNumber(): string
    {
        $last = TicketSale::query()->orderByDesc('id')->value('ticket_number');
        $num = $last ? (int) str_replace('TK-', '', $last) + 1 : 1;

        return 'TK-'.str_pad((string) $num, 6, '0', STR_PAD_LEFT);
    }
}
