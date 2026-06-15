<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /** @param  list<string>  $roles */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isActive()) {
            return redirect()->guest(route('login'));
        }

        if ($roles === []) {
            return $next($request);
        }

        if (! in_array($user->role, $roles, true)) {
            return redirect($user->homePath())
                ->with('error', 'ليس لديك صلاحية للوصول إلى هذا القسم.');
        }

        return $next($request);
    }
}
