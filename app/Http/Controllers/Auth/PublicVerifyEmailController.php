<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicVerifyEmailController extends Controller
{
    /**
     * Verifikasi email TANPA auth (pakai signed URL + cek hash).
     * Route: GET /verify-email/{id}/{hash}
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        // Cari user
        $user = User::findOrFail($id);

        // Validasi hash email (harus sama dengan sha1(email))
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Tautan verifikasi tidak valid.');
        }

        // Kalau sudah verified, langsung ke login dengan pesan.
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Email sudah terverifikasi. Silakan login.');
        }

        // Tandai sebagai terverifikasi
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Redirect ke halaman login (tidak auto-login)
        return redirect()
            ->route('login')
            ->with('status', 'Email berhasil diverifikasi. Silakan login.');
    }
}
