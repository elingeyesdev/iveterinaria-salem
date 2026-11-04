<?php

namespace App\Services;

use App\Models\Consulta;
use App\Models\Pago;
use App\Models\Servicio;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use RuntimeException;

/**
 * Gestiona los servicios aplicados a una consulta.
 *
 * Responsabilidades:
 * - Validar servicios.
 * - Verificar que estén activos.
 * - Obtener precios desde el catálogo.
 * - Conservar precios históricos.
 * - Gestionar fecha_actual.
 * - Gestionar fecha_prox.
 * - Gestionar peso en el pivot.
 */
class ConsultaServicioService
{
    /**
     * Sincroniza los servicios de una consulta.
     */
    public function sincronizar(
        Consulta $consulta,
        array $servicios,
        bool $reemplazar = false
    ): void {

        // Se bloquea la consulta también aquí para proteger otros llamadores
        // del servicio que no pasan por ConsultaService::actualizar().
        $consultaBloqueada = Consulta::query()->lockForUpdate()->findOrFail($consulta->id);
        $tienePagos = $consultaBloqueada->pagos()->where('estado', Pago::PAGADO)->exists();
        if ($tienePagos && ! $reemplazar) {
            throw new \DomainException('No se pueden añadir servicios a una consulta cobrada.');
        }
        if ($tienePagos && $reemplazar) {
            $actuales = $consultaBloqueada->servicios()->pluck('servicio.id')->map(fn ($id) => (int) $id)->sort()->values()->all();
            $solicitados = collect($servicios)->pluck('servicio_id')->map(fn ($id) => (int) $id)->unique()->sort()->values()->all();
            if ($actuales !== $solicitados) {
                throw new \DomainException('No se pueden agregar o quitar servicios de una consulta con pagos activos. Anule primero el cobro completo.');
            }

            // Ni precios ni datos de pivot deben reescribirse después del cobro.
            return;
        }

        /*
         * Normalizamos los servicios recibidos.
         */
        $serviciosNormalizados = collect($servicios)
            ->filter(
                fn ($servicio) => is_array($servicio)
            )
            ->map(function (array $servicio) use ($consulta) {

                $servicioId =
                    $servicio['servicio_id'] ?? null;

                if (! is_numeric($servicioId)) {
                    return null;
                }

                $servicioId = (int) $servicioId;

                if ($servicioId <= 0) {
                    return null;
                }

                $fechaProxima =
                    ! empty($servicio['fecha_prox'] ?? null)
                        ? $servicio['fecha_prox']
                        : null;

                $this->validarFechaProxima(
                    $fechaProxima,
                    $consulta->fecha
                );

                return [
                    'servicio_id' => $servicioId,
                    'fecha_prox' => $fechaProxima,
                ];
            })
            ->filter()
            ->unique('servicio_id')
            ->values();

        /*
         * Si no existen servicios enviados.
         *
         * En creación simplemente no hacemos nada.
         *
         * En edición eliminamos las relaciones existentes
         * porque el usuario quitó todos los servicios.
         */
        if ($serviciosNormalizados->isEmpty()) {

            if ($reemplazar) {
                $consulta->servicios()->sync([]);
            }

            return;
        }

        /*
         * Obtenemos los IDs.
         */
        $ids = $serviciosNormalizados
            ->pluck('servicio_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        /*
         * Obtenemos únicamente servicios activos.
         */
        $catalogo = Servicio::query()
            ->where('estado', 1)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        /*
         * Detectamos servicios inválidos o inactivos.
         */
        if (
            $catalogo->count() !== $ids->count()
        ) {
            $idsEncontrados = $catalogo
                ->keys()
                ->map(fn ($id) => (int) $id);

            $idsInvalidos = $ids
                ->diff($idsEncontrados);

            throw new InvalidArgumentException(
                'Uno o más servicios seleccionados no existen '
                .'o están inactivos: '
                .$idsInvalidos->implode(', ')
            );
        }

        /*
         * Indexamos los datos enviados.
         */
        $datosEnviados = $serviciosNormalizados
            ->keyBy('servicio_id');

        /*
         * Obtenemos precios históricos cuando se está
         * editando una consulta.
         */
        $preciosHistoricos = [];

        if ($reemplazar) {

            $preciosHistoricos = $consulta
                ->servicios()
                ->get()
                ->mapWithKeys(
                    function ($servicio) {

                        return [
                            (int) $servicio->id => round(
                                (float) (
                                    $servicio
                                        ->pivot
                                        ->precio ?? 0
                                ),
                                2
                            ),
                        ];
                    }
                )
                ->all();
        }

        /*
         * Construimos los datos del pivot.
         */
        $datosPivot = [];

        foreach ($ids as $servicioId) {

            $servicio =
                $catalogo->get($servicioId);

            if (! $servicio) {
                throw new RuntimeException(
                    "No se pudo cargar el servicio #{$servicioId}."
                );
            }

            /*
             * Precio histórico:
             *
             * Si el servicio ya estaba asociado,
             * conservamos el precio que tenía en esa consulta.
             *
             * Si es nuevo, usamos el precio actual del catálogo.
             */
            $precio = array_key_exists(
                $servicioId,
                $preciosHistoricos
            )
                ? $preciosHistoricos[$servicioId]
                : round(
                    (float) $servicio->precio,
                    2
                );

            /*
             * Fecha próxima enviada por el formulario.
             */
            $fechaProxima =
                $datosEnviados
                    ->get($servicioId)['fecha_prox']
                    ?? null;

            /*
             * Datos que se almacenarán en consulta_servicio.
             */
            $datosPivot[$servicioId] = [
                'fecha_actual' => $consulta->fecha,
                'fecha_prox' => $fechaProxima,
                'peso' => round(
                    (float) $consulta->peso,
                    2
                ),
                'precio' => $precio,
            ];
        }

        /*
         * EDICIÓN
         *
         * sync() deja exactamente los servicios
         * seleccionados actualmente.
         */
        if ($reemplazar) {

            $consulta
                ->servicios()
                ->sync($datosPivot);

            return;
        }

        /*
         * CREACIÓN
         *
         * No eliminamos servicios existentes.
         */
        $consulta
            ->servicios()
            ->syncWithoutDetaching($datosPivot);
    }

    /**
     * Valida la fecha próxima del servicio.
     */
    private function validarFechaProxima(
        ?string $fechaProxima,
        string $fechaConsulta
    ): void {

        if ($fechaProxima === null) {
            return;
        }

        try {
            $proxima =
                CarbonImmutable::parse(
                    $fechaProxima
                );

            $consulta =
                CarbonImmutable::parse(
                    $fechaConsulta
                );

        } catch (\Throwable $e) {

            throw new InvalidArgumentException(
                'La fecha próxima del servicio no es válida.'
            );
        }

        if ($proxima->lt($consulta)) {

            throw new InvalidArgumentException(
                'La fecha próxima del servicio no puede '
                .'ser anterior a la fecha de la consulta.'
            );
        }
    }
}
