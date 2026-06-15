<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request, PasswordResetService $passwordReset): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
        ]);

        $email = strtolower($data['email']);
        $userExists = User::where('email', $email)->exists();

        if ($userExists) {
            $passwordReset->sendCode($email);
        }

        return redirect()
            ->route('password.verify', ['email' => $email])
            ->with('status', 'إذا كان البريد مسجّلاً في النظام، فسيصلك رمز التحقق خلال دقائق.');
    }

    public function verifyForm(Request $request): View|RedirectResponse
    {
        $email = strtolower(trim((string) $request->query('email', '')));

        if ($email === '') {
            return redirect()->route('password.request');
        }

        return view('auth.verify-code', ['email' => $email]);
    }

    public function verify(Request $request, PasswordResetService $passwordReset): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'رمز التحقق مطلوب.',
            'code.size' => 'رمز التحقق يجب أن يكون 6 أرقام.',
        ]);

        $resetToken = $passwordReset->verifyCode($data['email'], $data['code']);

        if (! $resetToken) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['code' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.']);
        }

        return redirect()
            ->route('password.reset', ['token' => $resetToken])
            ->with('status', 'تم التحقق من الرمز. أدخل كلمة المرور الجديدة.');
    }

    public function resetForm(Request $request): View|RedirectResponse
    {
        $token = (string) $request->query('token', '');

        if ($token === '') {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request, PasswordResetService $passwordReset): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
        ]);

        if (! $passwordReset->resetPassword($data['token'], $data['password'])) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['password' => 'تعذّر إعادة التعيين. قد يكون الرمز منتهياً — اطلب رمزاً جديداً.']);
        }

        return redirect()
            ->route('login')
            ->with('status', 'تم تغيير كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.');
    }

    public function resend(Request $request, PasswordResetService $passwordReset): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($data['email']);

        if (User::where('email', $email)->exists()) {
            $passwordReset->sendCode($email);
        }

        return back()->with('status', 'إذا كان البريد مسجّلاً، فسيصلك رمز جديد.');
    }
}
