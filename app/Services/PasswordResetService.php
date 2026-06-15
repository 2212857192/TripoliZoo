<?php

namespace App\Services;

use App\Mail\PasswordResetCodeMail;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetService
{
    public function sendCode(string $email): bool
    {
        $email = strtolower(trim($email));

        PasswordResetCode::query()
            ->where('email', $email)
            ->delete();

        $code = $this->generateCode();

        PasswordResetCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => now()->addMinutes($this->ttlMinutes()),
        ]);

        return $this->mailCode($email, $code);
    }

    public function verifyCode(string $email, string $code): ?string
    {
        $email = strtolower(trim($email));
        $code = trim($code);

        $record = PasswordResetCode::query()
            ->where('email', $email)
            ->where('code', $code)
            ->latest('id')
            ->first();

        if (! $record || $record->isExpired()) {
            return null;
        }

        $resetToken = Str::random(64);

        $record->update([
            'verified_at' => now(),
            'reset_token' => $resetToken,
        ]);

        return $resetToken;
    }

    public function resetPassword(string $resetToken, string $password): bool
    {
        $record = PasswordResetCode::query()
            ->where('reset_token', $resetToken)
            ->whereNotNull('verified_at')
            ->latest('id')
            ->first();

        if (! $record || $record->isExpired()) {
            return false;
        }

        $user = User::where('email', $record->email)->first();

        if (! $user) {
            return false;
        }

        $user->update(['password' => $password]);

        PasswordResetCode::query()
            ->where('email', $record->email)
            ->delete();

        return true;
    }

    private function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function ttlMinutes(): int
    {
        return (int) config('tripolizoo.password_reset_code_ttl', 15);
    }

    private function mailCode(string $email, string $code): bool
    {
        try {
            Mail::to($email)->send(new PasswordResetCodeMail($code));

            return true;
        } catch (\Throwable $e) {
            Log::error('Password reset code email failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
