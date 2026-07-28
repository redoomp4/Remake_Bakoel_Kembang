<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('status', 'Email sudah terverifikasi.');
        }

        try {
            $request->user()->sendEmailVerificationNotification();
            // flag standar Laravel → cocok dengan pengecekan di Blade
            return back()->with('status', 'verification-link-sent');
        } catch (TransportExceptionInterface $e) {
            Log::error('SMTP gagal', ['message' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghubungi server email. Coba lagi.');
        } catch (\Throwable $e) {
            Log::error('Mail error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Terjadi kesalahan saat mengirim email.');
        }
    }
}
