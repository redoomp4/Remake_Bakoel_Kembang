<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Handle the email verification link.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Jika sudah verified, langsung arahkan ke login dengan pesan sukses
        if ($request->user()->hasVerifiedEmail()) {
            // Logout agar user masuk ulang sesuai permintaan flow kamu
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('status', 'Email sudah terverifikasi. Silakan login.');
        }

        // Tandai verified
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Logout agar user login manual lagi (sesuai keinginan kamu)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke login + flash message
        return redirect()->route('login')
            ->with('status', 'Email terverifikasi! Silakan login.');
    }
}
