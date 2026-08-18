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
     * Kirim link login tanpa password ke email user secara aman.
     * Route: POST /magic-link/request  (guest + throttle)
     */
    public function requestLink(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Masukkan alamat email Anda.',
            'email.email'    => 'Format alamat email tidak valid.',
        ]);

        $email = strtolower(trim($data['email']));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Alamat email ini tidak terdaftar dalam sistem.'])->withInput();
        }

        // Buat token sekali pakai (TTL 15 menit)
        $token = Str::random(40);
        Cache::put("magic:$token", $user->id, now()->addMinutes(15));

        // Buat signed URL aman dengan TTL 15 menit
        $signedUrl = URL::temporarySignedRoute(
            'magic.login',
            now()->addMinutes(15),
            [
                'email' => $user->email,
                'token' => $token,
            ]
        );

        // Kirim link login ke email pemilik akun
        try {
            Mail::to($user->email)->send(new MagicLinkMail($signedUrl));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim email login tanpa password: ' . $e->getMessage());
        }

        return back()
            ->with('status', 'Tautan login tanpa password telah dikirim ke alamat email Anda. Silakan periksa kotak masuk atau folder Spam.');
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
