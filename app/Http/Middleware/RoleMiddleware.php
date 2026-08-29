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
        $userRole = strtolower(auth()->user()->role ?? '');

        // Role admin dan superadmin memiliki akses penuh ke seluruh page dan form
        if (in_array($userRole, ['admin', 'superadmin'])) {
            return $next($request);
        }

        // Role kios (dan alias penjual/gudang) memiliki akses penuh ke seluruh page dan form
        if (in_array($userRole, ['kios', 'penjual', 'gudang'])) {
            return $next($request);
        }

        // Normalisasi role yang dicek
        $allowedRoles = array_map('strtolower', $roles);

        if (!empty($allowedRoles) && !in_array($userRole, $allowedRoles)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}
