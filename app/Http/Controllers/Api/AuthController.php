<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'هذا البريد مسجّل مسبقاً.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
        ]);

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            $first = trim($data['first_name'] ?? '');
            $last = trim($data['last_name'] ?? '');
            $name = trim("{$first} {$last}");
        }

        if ($name === '') {
            throw ValidationException::withMessages([
                'name' => ['الاسم مطلوب.'],
            ]);
        }

        $user = User::create([
            'name' => $name,
            'email' => strtolower($data['email']),
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => UserRole::Visitor->value,
            'status' => 'active',
            'joined_at' => now()->toDateString(),
        ]);

        return $this->tokenResponse($user, 'تم إنشاء الحساب بنجاح.', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        $user = User::where('email', strtolower($credentials['email']))->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        if (! $user->isActive()) {
            return response()->json([
                'message' => 'هذا الحساب غير نشط. تواصل مع إدارة الحديقة.',
            ], 403);
        }

        if (! $user->canUseMobileApp()) {
            return response()->json([
                'message' => 'هذا الحساب مخصص للوحة الويب. استخدم موقع الإدارة لتسجيل الدخول.',
            ], 403);
        }

        return $this->tokenResponse($user);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->isActive() || ! $user->canUseMobileApp()) {
            $user->currentAccessToken()?->delete();

            return response()->json([
                'message' => 'انتهت صلاحية الجلسة أو الحساب غير مسموح له بالدخول.',
            ], 403);
        }

        return response()->json([
            'user' => new ApiUserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح.',
        ]);
    }

    private function tokenResponse(User $user, ?string $message = null, int $status = 200): JsonResponse
    {
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        $payload = [
            'token' => $token,
            'user' => new ApiUserResource($user),
        ];

        if ($message) {
            $payload['message'] = $message;
        }

        return response()->json($payload, $status);
    }
}
