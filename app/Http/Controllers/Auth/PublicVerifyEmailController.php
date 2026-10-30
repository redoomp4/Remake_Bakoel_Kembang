<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicVerifyEmailController extends Controller
{
    /**
     * Verifikasi email (pakai signed URL + cek hash).
     * Route: GET /verify-email/{id}/{hash}
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        // Cari user berdasarkan ID
        $user = User::findOrFail($id);

        // Validasi hash email (harus sama dengan sha1(email))
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Tautan verifikasi tidak valid.');
        }

        // Tandai sebagai terverifikasi jika belum
        if (! $user->hasVerifiedEmail()) {
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }
        }

        // Pastikan status akun aktif
        $user->forceFill([
            'is_active' => true,
            'status' => 'Active',
            'last_login' => now(),
        ])->save();

        // Otomatis login-kan user
        Auth::login($user);

        // Tandai flag popup notifikasi
        $request->session()->put('show_notification_popup', true);

        // Redirect langsung ke dashboard sesuai role user
        return redirect(RouteServiceProvider::redirectByRole())
            ->with('success', 'Email berhasil diverifikasi! Selamat datang kembali.');
    }
}
