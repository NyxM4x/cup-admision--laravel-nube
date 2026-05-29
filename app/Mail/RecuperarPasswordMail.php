<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuperarPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $resetUrl,
        public string $nombreUsuario,
        public string $emailUsuario,
        public int $minutos = 60
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Recuperación de Contraseña - Sistema CUP',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recuperar-password',
        );
    }
}
