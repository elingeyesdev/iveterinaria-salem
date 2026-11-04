<?php

namespace App\Mail;

use App\Models\Recordatorio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecordatorioReservaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Recordatorio $recordatorio)
    {
        $this->recordatorio->loadMissing(['mascota.cliente', 'reserva', 'consulta', 'servicio']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->recordatorio->servicio
                ? 'Recordatorio de próxima atención - '.$this->recordatorio->mascota?->nombre
                : 'Recordatorio de cita veterinaria - '.$this->recordatorio->mascota?->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recordatorio-reserva',
        );
    }
}
