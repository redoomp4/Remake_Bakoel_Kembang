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

        // Role hanya 2: admin dan kios. Viewer atau role lainnya secara tegas DITOLAK.
        if (!in_array($userRole, ['admin', 'kios'])) {
            abort(403, 'Akses ditolak: Role Anda (' . ($userRole ?: 'Tanpa Role') . ') tidak memiliki izin untuk mengakses sistem ini.');
        }

        // Cek jika rute mengizinkan role tertentu secara spesifik (misal: role:admin)
        if (!empty($roles)) {
            $allowedRoles = array_map('strtolower', $roles);
            if (!in_array($userRole, $allowedRoles)) {
                abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengakses fitur ini.');
            }
        }

        return $next($request);
    }
}