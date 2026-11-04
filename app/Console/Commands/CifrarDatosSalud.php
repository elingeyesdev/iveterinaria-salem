<?php

namespace App\Console\Commands;

use App\Models\Consulta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CifrarDatosSalud extends Command
{
    protected $signature = 'consultas:cifrar-datos-salud {--dry-run : Solo verifica los registros sin modificarlos}';

    protected $description = 'Cifra los datos clínicos existentes de las consultas para OWASP ASVS V6.1.2';

    public function handle(): int
    {
        if (! config('app.key')) {
            $this->error('APP_KEY no está configurada.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $pendientes = 0;
        $yaCifrados = 0;
        $problemas = [];

        Consulta::query()->orderBy('id')->chunkById(100, function ($consultas) use (&$pendientes, &$yaCifrados, &$problemas) {
            foreach ($consultas as $consulta) {
                $campos = ['motivo', 'diagnostico', 'tratamiento'];
                $necesita = false;

                foreach ($campos as $campo) {
                    $raw = $consulta->getRawOriginal($campo);
                    if ($raw === null || $raw === '') {
                        continue;
                    }

                    if (! str_starts_with($raw, 'v1:')) {
                        $necesita = true;
                        if (mb_strlen($raw) > 160) {
                            $problemas[] = "Consulta #{$consulta->id}, campo {$campo}: supera 160 caracteres.";
                        }
                    }
                }

                if ($necesita) {
                    $pendientes++;
                } else {
                    $yaCifrados++;
                }
            }
        });

        if ($problemas !== []) {
            $this->error('No se puede completar el cifrado sin modificar la estructura de la BD.');
            foreach ($problemas as $problema) {
                $this->line("- {$problema}");
            }
            $this->warn('Acorte esos datos mediante el formulario y vuelva a ejecutar el comando.');

            return self::FAILURE;
        }

        if ($dryRun) {
            $this->info("Registros que requieren cifrado: {$pendientes}. Ya cifrados: {$yaCifrados}.");

            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use (&$pendientes) {
                Consulta::query()->orderBy('id')->chunkById(100, function ($consultas) use (&$pendientes) {
                    foreach ($consultas as $consulta) {
                        $cambio = false;
                        foreach (['motivo', 'diagnostico', 'tratamiento'] as $campo) {
                            $raw = $consulta->getRawOriginal($campo);
                            if ($raw !== null && $raw !== '' && ! str_starts_with($raw, 'v1:')) {
                                $consulta->{$campo} = $raw;
                                $cambio = true;
                            }
                        }
                        if ($cambio) {
                            $consulta->saveQuietly();
                        }
                    }
                });
            });
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Cifrado completado. Registros procesados: {$pendientes}.");

        return self::SUCCESS;
    }
}
