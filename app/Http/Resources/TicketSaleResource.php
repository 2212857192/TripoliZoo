<?php

namespace App\Http\Resources;

use App\Services\TicketSaleService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TicketSale */
class TicketSaleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $service = app(TicketSaleService::class);

        return [
            'id' => $this->ticket_number,
            'ticket_number' => $this->ticket_number,
            'qr_data' => $service->qrPayload($this->resource),
            'visit_date' => $this->sold_at?->toIso8601String(),
            'type_id' => (string) $this->ticket_type_id,
            'type_title' => $this->ticketType?->name ?? '',
            'price' => (int) round((float) $this->unit_price),
            'purchased_at' => $this->sold_at?->toIso8601String(),
            'payment_method' => $this->payment_method,
        ];
    }
}
