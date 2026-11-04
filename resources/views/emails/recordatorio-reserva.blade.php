<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de cita</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f9; padding:30px; color:#343a40;">
    <div style="max-width:620px; margin:auto; background:#fff; border-radius:8px; padding:30px;">
        <h2 style="margin-top:0;">🐾 Recordatorio de cita veterinaria</h2>

        @php
            $cliente = $recordatorio->mascota?->cliente;
            $reserva = $recordatorio->reserva;
            $servicio = $recordatorio->servicio;
        @endphp

        <p>Hola <strong>{{ trim(($cliente?->nombre ?? '') . ' ' . ($cliente?->apellido ?? '')) ?: 'cliente' }}</strong>,</p>

        <p>
            @if($servicio)
                Te recordamos que tu mascota tiene programado el siguiente servicio en nuestra veterinaria:
            @else
                Te recordamos que tu mascota tiene una cita programada en nuestra veterinaria:
            @endif
        </p>

        <table cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <tr><td><strong>Mascota</strong></td><td>{{ $recordatorio->mascota?->nombre ?? '—' }}</td></tr>
            <tr><td><strong>Servicio</strong></td><td>{{ $servicio?->nombre ?? 'Cita veterinaria' }}</td></tr>
            <tr><td><strong>Fecha</strong></td><td>{{ $reserva?->fecha ? \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') : $recordatorio->fecha->format('d/m/Y') }}</td></tr>
            @if($reserva)
                <tr><td><strong>Hora</strong></td><td>{{ $reserva->hora_inicio ?? '—' }}</td></tr>
            @endif
        </table>

        <p style="margin-top:24px;">Por favor, procura llegar unos minutos antes de la hora indicada.</p>
        <p>¡Te esperamos! 🐶🐱</p>
    </div>
</body>
</html>
