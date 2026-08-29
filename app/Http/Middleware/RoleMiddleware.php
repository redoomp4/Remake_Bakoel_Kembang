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
        // Semua role (admin, kios, viewer, gudang, penjual, superadmin) diizinkan mengakses semua halaman, form, tambah, edit, dan hapus
        if (auth()->check()) {
            return $next($request);
        }

        abort(403, 'Akses ditolak: Anda harus login terlebih dahulu.');
    }
}