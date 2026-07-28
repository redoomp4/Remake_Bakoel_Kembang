<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\MagicLinkMail;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class MagicLinkController extends Controller
{
    /**
     * (Opsional) Tampilkan form minta magic link.
     * Kamu sudah pakai route closure ke view('auth.magic-request'), jadi method ini tidak wajib.
     */
    public function form()
    {
        return view('auth.magic-request');
    }

    /**
     * Kirim magic link ke email user (tanpa password).
     * Route: POST /magic-link/request  (guest + throttle)
     */
    public function requestLink(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        // Pilih salah satu:
        // A) Lebih aman (hindari user enumeration): SELALU tampilkan status sukses meski user tidak ada.
        // if (! $user) {
        //     return back()->with('status', 'Jika email terdaftar, tautan login telah dikirim.');
        // }

        // B) Versi eksplisit (seperti contohmu sebelumnya):
        if (! $user) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.'])->onlyInput('email');
        }

        // Buat token sekali pakai, simpan di cache 15 menit
        $token = Str::random(40);
        Cache::put("magic:$token", $user->id, now()->addMinutes(15));

        // Buat signed URL dengan TTL 15 menit
        $url = URL::temporarySignedRoute(
            'magic.login',
            now()->addMinutes(15),
            [
                'email' => $user->email,
                'token' => $token,
            ]
        );

        // Kirim email
        Mail::to($user->email)->send(new MagicLinkMail($url));

        return back()->with('status', 'Magic link sudah dikirim ke email Anda.');
    }

    /**
     * Endpoint yang diklik dari email.
     * Route: GET /magic-login  (signed + throttle)
     */
    public function login(Request $request)
    {
        // Jika sudah login, langsung arahkan
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Middleware 'signed' pada route sudah memeriksa signature,
        // di sini kita cek token sekali pakai.
        $token = $request->query('token');
        $email = $request->query('email');

        // Ambil & hapus token dari cache agar sekali pakai
        $userId = Cache::pull("magic:$token");
        if (! $token || ! $userId) {
            return redirect()->route('magic.form')
                ->withErrors(['email' => 'Link tidak valid atau sudah kedaluwarsa.']);
        }

        $user = User::find($userId);
        if (! $user || $user->email !== $email) {
            return redirect()->route('magic.form')
                ->withErrors(['email' => 'Link tidak valid.']);
        }

        // Login user
        Auth::login($user);

        // (Opsional) Tandai email terverifikasi sebagai bukti kepemilikan
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Update last_login
        $user->forceFill(['last_login' => now()])->save();

        // (Opsional) Flag popup notifikasi seperti di controller login/pass
        $request->session()->put('show_notification_popup', true);

        // Redirect sesuai role
        return redirect()->intended(RouteServiceProvider::redirectByRole());
    }
}
