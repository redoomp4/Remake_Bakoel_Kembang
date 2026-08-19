<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Format nomor HP Indonesia ke format internasional 628xxx
     */
    public static function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            return '62' . substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '8')) {
            return '62' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Buat URL WhatsApp Direct Link (wa.me) dengan pesan terenkode
     */
    public static function generateWaUrl(string $phone, string $message): string
    {
        $formattedPhone = self::formatPhoneNumber($phone);
        $encodedMessage = urlencode($message);

        return "https://api.whatsapp.com/send?phone={$formattedPhone}&text={$encodedMessage}";
    }

    /**
     * Kirim pesan WhatsApp melalui API Gateway (jika dikonfigurasi di .env)
     */
    public static function sendMessage(string $phone, string $message): bool
    {
        $apiUrl = config('services.whatsapp.api_url');
        $token  = config('services.whatsapp.api_token');

        $formattedPhone = self::formatPhoneNumber($phone);

        if ($apiUrl && $token) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])->post($apiUrl, [
                    'target'  => $formattedPhone,
                    'message' => $message,
                ]);

                return $response->successful();
            } catch (\Throwable $e) {
                Log::error('WhatsApp API sending failed: ' . $e->getMessage());
            }
        }

        return false;
    }
}
