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

        // HANYA admin dan kios (serta superadmin) yang diizinkan mengakses semua halaman dan form
        if (in_array($userRole, ['admin', 'kios', 'superadmin'])) {
            return $next($request);
        }

        // Role viewer secara tegas DITOLAK dari akses pengelolaan/form/CRUD
        if ($userRole === 'viewer') {
            abort(403, 'Akses ditolak: Akun Viewer tidak memiliki izin untuk mengelola atau mengakses halaman ini.');
        }

        // Cek jika rute mengizinkan role tertentu
        if (!empty($roles) && in_array($userRole, array_map('strtolower', $roles))) {
            return $next($request);
        }

        abort(403, 'Akses ditolak: Anda tidak memiliki izin.');
    }
}