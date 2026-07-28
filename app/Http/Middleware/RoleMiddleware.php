<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $userRole = auth()->user()->role;

        // Superadmin selalu punya akses penuh ke semua fitur
        if ($userRole === 'superadmin') {
            return $next($request);
        }

        if (!in_array($userRole, $roles)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin.');
        }
        return $next($request);
    }
}
