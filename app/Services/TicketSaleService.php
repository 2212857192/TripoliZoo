<?php

namespace App\Services;

use App\Enums\TicketPaymentMethod;
use App\Models\TicketSale;
use App\Models\TicketType;
use App\Models\User;
use App\Support\PublicUrl;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class TicketSaleService
{
    public function nextTicketNumber(): string
    {
        $last = TicketSale::query()->orderByDesc('id')->value('ticket_number');
        $num = $last ? (int) str_replace('TK-', '', $last) + 1 : 1;

        return 'TK-'.str_pad((string) $num, 6, '0', STR_PAD_LEFT);
    }

    public function qrPayload(TicketSale $sale): string
    {
        return PublicUrl::absolute('/api/tickets/'.$sale->ticket_number);
    }

    /** @param  array<int, array{ticket_type_id:int, quantity:int}>  $items */
    public function calculateTotal(array $items): float
    {
        $total = 0.0;

        foreach ($items as $item) {
            $type = TicketType::query()->find($item['ticket_type_id'] ?? null);

            if (! $type || ! $type->is_active) {
                throw ValidationException::withMessages([
                    'items' => 'إحدى فئات التذاكر غير متاحة للبيع.',
                ]);
            }

            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity < 1) {
                continue;
            }

            $total += (float) $type->price * $quantity;
        }

        if ($total <= 0) {
            throw ValidationException::withMessages([
                'items' => 'يرجى اختيار تذكرة واحدة على الأقل.',
            ]);
        }

        return $total;
    }

    public function makeInvoiceNumber(): string
    {
        return 'TZ-'.now()->format('YmdHis').'-'.strtoupper(substr(uniqid(), -5));
    }

    /** @param  array<int, array{ticket_type_id:int, quantity:int}>  $items */
    public function purchaseFromApp(User $purchaser, array $items, TicketPaymentMethod|string $paymentMethod): Collection
    {
        $method = $paymentMethod instanceof TicketPaymentMethod
            ? $paymentMethod->value
            : $paymentMethod;

        $sales = collect();

        foreach ($items as $item) {
            $type = TicketType::query()->find($item['ticket_type_id'] ?? null);

            if (! $type || ! $type->is_active) {
                throw ValidationException::withMessages([
                    'items' => 'إحدى فئات التذاكر غير متاحة للبيع.',
                ]);
            }

            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity < 1) {
                continue;
            }

            for ($i = 0; $i < $quantity; $i++) {
                $unitPrice = (float) $type->price;
                $sales->push(TicketSale::create([
                    'ticket_number' => $this->nextTicketNumber(),
                    'ticket_type_id' => $type->id,
                    'customer_name' => $purchaser->name,
                    'quantity' => 1,
                    'unit_price' => $unitPrice,
                    'total_amount' => $unitPrice,
                    'payment_method' => $method,
                    'sold_by' => $purchaser->id,
                    'sold_at' => now(),
                ]));
            }
        }

        if ($sales->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'يرجى اختيار تذكرة واحدة على الأقل.',
            ]);
        }

        return $sales;
    }
}
