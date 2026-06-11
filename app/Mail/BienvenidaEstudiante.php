<?php

namespace App\Mail;

use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenidaEstudiante extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $usuario,
        public Inscripcion $inscripcion,
        public string $passwordTemporal
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Pago aprobado — Bienvenido al CUP FICCT UAGRM',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.bienvenida-estudiante',
            with: [
                'usuario'          => $this->usuario,
                'inscripcion'      => $this->inscripcion,
                'passwordTemporal' => $this->passwordTemporal,
                'urlLogin'         => url('/login'),
            ]
        );
    }
}