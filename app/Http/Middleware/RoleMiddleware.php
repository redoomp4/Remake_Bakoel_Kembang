<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $userRole = auth()->user()->role;

        // Superadmin selalu punya akses penuh ke semua fitur
        if ($userRole === 'superadmin') {
            return $next($request);
        }

        if (! in_array($userRole, $roles)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}
