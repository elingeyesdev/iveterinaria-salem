<?php

namespace App\Console\Commands;

use App\Models\Recordatorio;
use App\Services\RecordatorioCorreoService;
use Illuminate\Console\Command;

class EnviarRecordatoriosCorreo extends Command
{
    protected $signature = 'recordatorios:enviar {--forzar : Enviar el recordatorio aunque su fecha de envío todavía no haya llegado}';

    protected $description = 'Envía por correo los recordatorios programados de reservas y fechas próximas de servicios';

    public function handle(RecordatorioCorreoService $service): int
    {
        $query = Recordatorio::with(['reserva', 'consulta', 'servicio', 'mascota.cliente'])
            ->where('canal', 'correo')
            ->where('estado', 'Pendiente')
            ->whereNull('enviado_at')
            ->where(function ($q) {
                $q->whereHas('reserva', fn ($sub) => $sub->where('estado', 'Pendiente'))
                    ->orWhereHas('consulta');
            });

        if (! $this->option('forzar')) {
            $query->where(function ($q) {
                $q->whereNull('fecha_envio')->orWhere('fecha_envio', '<=', now());
            });
        }

        $recordatorios = $query->get();
        $enviados = 0;

        foreach ($recordatorios as $recordatorio) {
            if ($service->enviar($recordatorio, (bool) $this->option('forzar'))) {
                $enviados++;
                $this->info("Recordatorio #{$recordatorio->id} enviado.");
            }
        }

        $this->info("Proceso terminado. Enviados: {$enviados}.");

        return self::SUCCESS;
    }
}
