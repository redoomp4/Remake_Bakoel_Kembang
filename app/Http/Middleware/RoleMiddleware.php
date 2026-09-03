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
        if (!auth()->check()) {
            abort(403, 'Akses ditolak: Anda harus login terlebih dahulu.');
        }

        $userRole = strtolower(auth()->user()->role ?? '');

        // Role admin dan kios (serta superadmin/gudang/penjual/viewer) diizinkan mengakses semua halaman dan form
        if (in_array($userRole, ['admin', 'kios', 'superadmin', 'gudang', 'penjual', 'viewer'])) {
            return $next($request);
        }

        if (!empty($roles) && !in_array($userRole, array_map('strtolower', $roles))) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}