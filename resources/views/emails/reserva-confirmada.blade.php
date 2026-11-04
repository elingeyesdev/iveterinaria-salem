<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Cita</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: Arial, sans-serif; color: #333333;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <!-- Cabecera con Branding -->
        <tr>
            <td align="center" style="background-color: #2f3542; padding: 25px 0; color: #ffffff;">
                <h1 style="margin: 0; font-size: 24px; font-weight: bold;">Veterinaria Salem</h1>
                <p style="margin: 5px 0 0 0; font-size: 14px; color: #ced4da;">Cuidado experto para tu mejor amigo</p>
            </td>
        </tr>
        
        <!-- Cuerpo del Mensaje -->
        <tr>
            <td style="padding: 30px;">
                @php
                    $cliente = $reserva->mascota?->cliente;
                    $nombreCliente = trim(($cliente?->nombre ?? '') . ' ' . ($cliente?->apellido ?? '')) ?: 'Estimado cliente';
                @endphp

                <h2 style="color: #2d6a4f; margin-top: 0; font-size: 20px;">¡Reserva Registrada con Éxito!</h2>
                
                <p style="font-size: 16px; line-height: 1.5;">Hola <strong>{{ $nombreCliente }}</strong>,</p>
                <p style="font-size: 15px; line-height: 1.5; color: #555555;">Te confirmamos que hemos agendado la cita médica de manera exitosa en nuestro sistema.</p>

                <!-- Tarjeta de Resumen (Patrón UX para datos clave) -->
                <table border="0" cellpadding="12" cellspacing="0" width="100%" style="background-color: #f8f9fa; border-radius: 6px; margin: 20px 0; border: 1px solid #e9ecef;">
                    <tr>
                        <td style="font-size: 14px; color: #6c757d; width: 35%;"><strong>Mascota:</strong></td>
                        <td style="font-size: 14px; color: #212529;">{{ $reserva->mascota?->nombre ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #6c757d;"><strong>Fecha:</strong></td>
                        <td style="font-size: 14px; color: #212529;">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #6c757d;"><strong>Horario:</strong></td>
                        <td style="font-size: 14px; color: #212529; font-weight: bold; color: #d97706;">{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #6c757d;"><strong>Estado:</strong></td>
                        <td style="font-size: 14px;"><span style="background-color: #fff3cd; color: #856404; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">{{ $reserva->estado }}</span></td>
                    </tr>
                </table>

                <p style="font-size: 14px; line-height: 1.5; color: #555555;">Te sugerimos presentarte con <strong>10 minutos de anticipación</strong> a la hora programada en nuestras instalaciones (Av. Paragua entre 2do y 3er Anillo).</p>

                <p style="font-size: 14px; margin-top: 30px; color: #333333;">Atentamente,<br><strong>Equipo de Veterinaria Salem</strong></p>
            </td>
        </tr>

        <!-- Pie de página -->
        <tr>
            <td align="center" style="background-color: #f1f2f6; padding: 15px; font-size: 12px; color: #718096;">
                Este es un mensaje automático, por favor no respondas a este correo. Si necesitas reprogramar, comunícate al +591 78588886.
            </td>
        </tr>
    </table>
</body>
</html>