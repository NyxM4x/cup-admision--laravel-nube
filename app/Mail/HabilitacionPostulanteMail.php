<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HabilitacionPostulanteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombreUsuario,
        public string $email,
        public string $passwordTemporal,
        public string $carrera1,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎓 ¡Habilitado para pagar! - Sistema CUP UAGRM',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.habilitacion-postulante',
        );
    }
}
