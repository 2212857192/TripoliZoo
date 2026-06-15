<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthPasswordController extends Controller
{
    public function sendCode(Request $request, PasswordResetService $passwordReset): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($data['email']);

        if (User::where('email', $email)->exists()) {
            $passwordReset->sendCode($email);
        }

        return response()->json([
            'message' => 'إذا كان البريد مسجّلاً، فسيصلك رمز التحقق.',
        ]);
    }

    public function verifyCode(Request $request, PasswordResetService $passwordReset): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $resetToken = $passwordReset->verifyCode($data['email'], $data['code']);

        if (! $resetToken) {
            return response()->json([
                'message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.',
            ], 422);
        }

        return response()->json([
            'reset_token' => $resetToken,
        ]);
    }

    public function resetPassword(Request $request, PasswordResetService $passwordReset): JsonResponse
    {
        $data = $request->validate([
            'reset_token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if (! $passwordReset->resetPassword($data['reset_token'], $data['password'])) {
            return response()->json([
                'message' => 'تعذّر إعادة التعيين. قد يكون الرمز منتهياً.',
            ], 422);
        }

        return response()->json([
            'message' => 'تم تغيير كلمة المرور بنجاح.',
        ]);
    }
}
