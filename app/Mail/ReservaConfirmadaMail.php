<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservaConfirmadaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reserva;

    public function __construct(Reserva $reserva)
    {
        // Cargamos las relaciones necesarias para optimizar consultas
        $this->reserva = $reserva->load('mascota.cliente');
    }

    public function envelope(): Envelope
    {
        $nombreMascota = $this->reserva->mascota?->nombre ?? 'tu mascota';

        return new Envelope(
            subject: "🐾 ¡Cita confirmada para {$nombreMascota} en Veterinaria Salem!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reserva-confirmada',
        );
    }
}
