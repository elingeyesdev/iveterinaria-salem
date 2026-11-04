<?php

namespace App\Console\Commands;

use App\Models\Consulta;
use Illuminate\Console\Command;

class PruebaInsercionConsulta extends Command
{
    protected $signature = 'prueba:insercion-consulta';

    public function handle()
    {
        \DB::beginTransaction();
        try {
            $c = Consulta::create([
                'fecha' => '2026-08-31',
                'tipo' => 'Emergencia',
                'motivo' => 'Prueba insercion',
                'diagnostico' => 'OK',
                'peso' => 4.5,
                'mascota_id' => 1,
                'veterinario_id' => 1,
                'reserva_id' => null,
            ]);
            dump([
                'id' => $c->id,
                'estado_clave' => $c->estado_clave,
                'tipo' => $c->tipo,
                'reserva_id' => $c->reserva_id,
            ]);
        } catch (\Throwable $e) {
            echo 'ERROR: '.$e->getMessage()."\n";
        } finally {
            \DB::rollBack();
            echo "Rollback OK\n";
        }
    }
}
