<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    /**
     * Buat instance baru.
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Build pesan email.
     */
    public function build()
    {
        return $this->subject('Link Login Instan di bakoelkembang.com')
                    ->view('emails.magic-link');
    }
}
