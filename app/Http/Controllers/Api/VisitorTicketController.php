<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketPaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketSaleResource;
use App\Http\Resources\TicketTypeResource;
use App\Models\TicketSale;
use App\Models\TicketType;
use App\Services\PlutuTicketPaymentService;
use App\Services\TicketSaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class VisitorTicketController extends Controller
{
    /** @return array<string, mixed> */
    private function validatedItems(Request $request): array
    {
        return $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.ticket_type_id' => ['required', 'integer', 'exists:ticket_types,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);
    }

    public function types(): JsonResponse
    {
        $types = TicketType::query()
            ->where('is_active', true)
            ->orderBy('target_description')
            ->get();

        return response()->json([
            'data' => TicketTypeResource::collection($types),
        ]);
    }

    public function mine(Request $request): JsonResponse
    {
        $sales = TicketSale::query()
            ->with('ticketType')
            ->where('sold_by', $request->user()->id)
            ->latest('sold_at')
            ->get();

        return response()->json([
            'data' => TicketSaleResource::collection($sales),
        ]);
    }

    public function purchaseCash(Request $request, TicketSaleService $service): JsonResponse
    {
        $data = $this->validatedItems($request);

        $sales = $service->purchaseFromApp(
            $request->user(),
            $data['items'],
            TicketPaymentMethod::Cash,
        );

        return response()->json([
            'data' => TicketSaleResource::collection($sales),
            'message' => 'تم شراء التذاكر نقدًا بنجاح.',
        ], 201);
    }

    public function verifyElectronicPayment(
        Request $request,
        TicketSaleService $saleService,
        PlutuTicketPaymentService $plutu,
    ): JsonResponse {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.ticket_type_id' => ['required', 'integer', 'exists:ticket_types,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'mobile' => ['required', 'string', 'max:20'],
        ]);

        $amount = $saleService->calculateTotal($data['items']);
        $mobile = $plutu->normalizeMobile($data['mobile']);
        $invoiceNo = $saleService->makeInvoiceNumber();
        $otp = $plutu->sendOtp($mobile, $amount);

        $processId = $otp['process_id'];
        $cacheKey = $this->checkoutCacheKey($request->user()->id, $processId);

        Cache::put($cacheKey, [
            'user_id' => $request->user()->id,
            'items' => $data['items'],
            'amount' => $amount,
            'invoice_no' => $invoiceNo,
            'mobile' => $mobile,
        ], now()->addMinutes(15));

        return response()->json([
            'data' => [
                'process_id' => $processId,
                'amount' => $amount,
                'invoice_no' => $invoiceNo,
            ],
            'message' => 'تم إرسال رمز التحقق إلى هاتفك.',
        ]);
    }

    public function confirmElectronicPayment(
        Request $request,
        TicketSaleService $saleService,
        PlutuTicketPaymentService $plutu,
    ): JsonResponse {
        $data = $request->validate([
            'process_id' => ['required', 'string', 'max:255'],
            'otp' => ['required', 'string', 'size:4'],
        ]);

        $cacheKey = $this->checkoutCacheKey($request->user()->id, $data['process_id']);
        $checkout = Cache::get($cacheKey);

        if (! is_array($checkout) || ($checkout['user_id'] ?? null) !== $request->user()->id) {
            return response()->json([
                'message' => 'انتهت جلسة الدفع. أعد المحاولة من البداية.',
            ], 422);
        }

        $plutu->confirmPayment(
            $data['process_id'],
            $data['otp'],
            (float) $checkout['amount'],
            (string) $checkout['invoice_no'],
            $request->ip(),
        );

        $sales = $saleService->purchaseFromApp(
            $request->user(),
            $checkout['items'],
            TicketPaymentMethod::Electronic,
        );

        Cache::forget($cacheKey);

        return response()->json([
            'data' => TicketSaleResource::collection($sales),
            'message' => 'تم الدفع الإلكتروني وإصدار التذاكر بنجاح.',
        ], 201);
    }

    /** @deprecated Use purchaseCash or confirmElectronicPayment */
    public function purchase(Request $request, TicketSaleService $service): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.ticket_type_id' => ['required', 'integer', 'exists:ticket_types,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'payment_method' => ['required', 'string', Rule::in(TicketPaymentMethod::values())],
        ]);

        if ($data['payment_method'] === TicketPaymentMethod::Electronic->value) {
            return response()->json([
                'message' => 'الدفع الإلكتروني يتطلب خطوتي التحقق عبر /tickets/purchase/electronic/verify ثم /confirm.',
            ], 422);
        }

        $sales = $service->purchaseFromApp(
            $request->user(),
            $data['items'],
            TicketPaymentMethod::Cash,
        );

        return response()->json([
            'data' => TicketSaleResource::collection($sales),
            'message' => 'تم شراء التذاكر بنجاح.',
        ], 201);
    }

    public function show(Request $request, string $ticketNumber): JsonResponse
    {
        $sale = TicketSale::query()
            ->with('ticketType')
            ->where('ticket_number', $ticketNumber)
            ->where('sold_by', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'data' => new TicketSaleResource($sale),
        ]);
    }

    private function checkoutCacheKey(int $userId, string $processId): string
    {
        return "ticket_checkout:{$userId}:{$processId}";
    }
}
