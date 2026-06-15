<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($user = Auth::user()) {
            if (! $user->isActive()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->withErrors([
                        'email' => 'لم يتمكن من تسجيل الدخول — هذا الحساب غير نشط. تواصل مع مدير النظام.',
                    ]);
            }

            return redirect()->intended($user->homePath());
        }

        return view('login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && ! $user->isActive()) {
            return back()->withErrors([
                'email' => 'لم يتمكن من تسجيل الدخول — هذا الحساب غير نشط. تواصل مع مدير النظام.',
            ])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'بيانات الدخول غير صحيحة.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'لم يتمكن من تسجيل الدخول — هذا الحساب غير نشط. تواصل مع مدير النظام.',
            ])->onlyInput('email');
        }

        if (! $user->roleEnum() || ! $user->portal()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'دور هذا الحساب غير معرّف في النظام. تواصل مع مدير النظام.',
            ])->onlyInput('email');
        }

        return redirect()->intended($user->homePath());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'تم تسجيل الخروج بنجاح.');
    }
}
