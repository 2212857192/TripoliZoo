<?php

namespace App\Http\Middleware;

use App\Enums\Portal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPortal
{
    public function handle(Request $request, Closure $next, string $portal): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isActive()) {
            return redirect()->guest(route('login'));
        }

        $expected = Portal::tryFrom($portal);
        $actual = $user->portal();

        if (! $expected || ! $actual || $actual !== $expected) {
            $target = $user->homePath();
            $message = $actual
                ? 'هذا القسم مخصّص لدور آخر. تم توجيهك إلى تطبيقك: '.$actual->label().'.'
                : 'دور حسابك غير معرّف. تواصل مع مدير النظام.';

            return redirect($target)->with('error', $message);
        }

        if (! $user->canUseWebPortal()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'هذا الحساب مخصص لتطبيق الجوال. استخدم تطبيق حديقة طرابلس لتسجيل الدخول.',
                ]);
        }

        return $next($request);
    }
}
