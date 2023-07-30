<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorAuthPassword extends Mailable
{
    use Queueable, SerializesModels;

    private $tfa_token = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tfa_token)
    {
        $this->tfa_token = $tfa_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('test@example.com', 'サイト名')
                    ->subject('2段階認証のパスワード')
                    ->view('emails.two_factor_auth.password')
                    ->with('tfa_token', $this->tfa_token);
    }
}
