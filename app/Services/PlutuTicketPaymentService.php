<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;
use PlutuLaravel\Facades\PlutuAdfali;

class PlutuTicketPaymentService
{
    public function isConfigured(): bool
    {
        return filled(config('plutu.api_key')) && filled(config('plutu.access_token'));
    }

    /** @return array{process_id:string} */
    public function sendOtp(string $mobileNumber, float $amount): array
    {
        $this->ensureConfigured();

        $mobile = $this->normalizeMobile($mobileNumber);
        $response = PlutuAdfali::verify($mobile, $amount);
        $original = $response->getOriginalResponse();

        if ($original->isSuccessful()) {
            return [
                'process_id' => (string) $response->getProcessId(),
            ];
        }

        throw ValidationException::withMessages([
            'mobile' => $this->plutuErrorMessage($original),
        ]);
    }

    public function confirmPayment(
        string $processId,
        string $otp,
        float $amount,
        string $invoiceNo,
        ?string $customerIp = null,
    ): string {
        $this->ensureConfigured();

        $response = PlutuAdfali::confirm(
            $processId,
            $otp,
            $amount,
            $invoiceNo,
            $customerIp,
        );

        $original = $response->getOriginalResponse();

        if ($original->isSuccessful()) {
            return (string) $response->getTransactionId();
        }

        throw ValidationException::withMessages([
            'otp' => $this->plutuErrorMessage($original),
        ]);
    }

    public function normalizeMobile(string $mobileNumber): string
    {
        $digits = preg_replace('/\D+/', '', $mobileNumber) ?? '';

        if (str_starts_with($digits, '218') && strlen($digits) >= 12) {
            $digits = '0'.substr($digits, 3);
        }

        if (strlen($digits) === 9) {
            $digits = '0'.$digits;
        }

        if (! preg_match('/^09\d{8}$/', $digits)) {
            throw ValidationException::withMessages([
                'mobile' => 'أدخل رقم هاتف ليبي صحيحًا يبدأ بـ 09.',
            ]);
        }

        return $digits;
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw ValidationException::withMessages([
                'payment' => 'بوابة Plutu غير مهيأة. أضف PLUTU_API_KEY و PLUTU_ACCESS_TOKEN في ملف .env.',
            ]);
        }
    }

    private function plutuErrorMessage(object $original): string
    {
        $message = method_exists($original, 'getErrorMessage')
            ? (string) $original->getErrorMessage()
            : '';

        if (strcasecmp($message, 'Unauthorized') === 0) {
            return 'رفضت Plutu المصادقة. تأكد من PLUTU_API_KEY و Access Token (JWT) من لوحة Plutu وليس المفتاح السري فقط.';
        }

        return $message !== '' ? $message : 'تعذّر إتمام العملية عبر Plutu.';
    }
}
