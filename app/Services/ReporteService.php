<?php

namespace App\Services;

use App\Models\Pago;
use App\Support\Reportes\Filtros;
use App\Support\ReporteTipo;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Genera los datos de todos los reportes.
 *
 * Principios:
 *  - Todo se agrega en SQL (SUM/COUNT/GROUP BY); nunca se cargan modelos
 *    completos para sumar en PHP.
 *  - Se usa el query builder y solo las columnas necesarias: así los datos
 *    clínicos cifrados de "consulta" (motivo, diagnóstico, tratamiento)
 *    jamás pasan por un reporte.
 *  - Solo SQL portable (MySQL en producción, SQLite en tests).
 *  - Todos los reportes devuelven la misma estructura, de modo que pantalla,
 *    CSV e impresión comparten una única fuente de verdad:
 *
 *    [ 'kpis' => [], 'grafico' => ?[], 'grafico2' => ?[],
 *      'columnas' => [['key','label','tipo']], 'filas' => [], 'totales' => ?[], 'notas' => [] ]
 */
class ReporteService
{
    /** Por encima de estos días se agrupa por mes en lugar de por día. */
    private const UMBRAL_MENSUAL_DIAS = 62;

    /** La migración usa "No asistio" y parte del código "No asistido". */
    private const ESTADOS_NO_ASISTIO = ['No asistio', 'No asistido'];


    public function generar(string $tipo, Filtros $f): array
    {
        return match ($tipo) {
            ReporteTipo::INGRESOS => $this->ingresos($f),
            ReporteTipo::POR_COBRAR => $this->cuentasPorCobrar($f),
            ReporteTipo::SERVICIOS => $this->servicios($f),
            ReporteTipo::VETERINARIOS => $this->veterinarios($f),
            ReporteTipo::RESERVAS => $this->reservas($f),
            ReporteTipo::CLIENTES_MASCOTAS => $this->clientesMascotas($f),
            default => throw new InvalidArgumentException("Reporte desconocido: {$tipo}"),
        };
    }

    // =====================================================================
    // INGRESOS
    // =====================================================================

    public function ingresos(Filtros $f): array
    {
        [$mensual, $periodos] = $this->periodos($f);

        $lineas = $this->pagosBase($f, Pago::PAGADO)
            ->selectRaw('DATE(pago.updated_at) as dia, ? as metodo, SUM(pago.monto) as total, COUNT(*) as pagos', ['Efectivo'])
            ->groupByRaw('DATE(pago.updated_at)')
            ->get();

        $tabla = [];
        foreach ($periodos as $clave) {
            $tabla[$clave] = ['clave' => $clave, 'pagos' => 0, 'Efectivo' => 0.0, 'total' => 0.0];
        }

        foreach ($lineas as $linea) {
            $clave = $this->clavePeriodo((string) $linea->dia, $mensual);
            $metodo = (string) $linea->metodo;

            $tabla[$clave]['pagos'] += (int) $linea->pagos;
            $tabla[$clave][$metodo] = ($tabla[$clave][$metodo] ?? 0.0) + (float) $linea->total;
            $tabla[$clave]['total'] += (float) $linea->total;
        }

        $totalCobrado = array_sum(array_column($tabla, 'total'));
        $totalPagos = (int) array_sum(array_column($tabla, 'pagos'));

        $totalEfectivo = round(array_sum(array_column($tabla, 'Efectivo')), 2);

        $anulados = $this->pagosBase($f, Pago::ANULADO)
            ->selectRaw('COUNT(*) as n, COALESCE(SUM(pago.monto), 0) as total')
            ->first();

        $facturado = (float) $this->consultaServicioBase($f)->sum('cs.precio');

        $filas = [];
        foreach ($tabla as $clave => $fila) {
            if ($fila['pagos'] === 0) {
                continue;
            }
            $filas[] = [
                'periodo' => $this->etiquetaPeriodo($clave, $mensual),
                'pagos' => $fila['pagos'],
                'efectivo' => round($fila['Efectivo'], 2),
                'total' => round($fila['total'], 2),
            ];
        }

        return [
            'kpis' => [
                $this->kpi('Total cobrado', $totalCobrado, 'moneda', 'fas fa-cash-register', 'success'),
                $this->kpi('Pagos registrados', $totalPagos, 'entero', 'fas fa-receipt', 'info'),
                $this->kpi('Ticket promedio', $totalPagos > 0 ? $totalCobrado / $totalPagos : 0, 'moneda', 'fas fa-chart-line', 'primary'),
                $this->kpi('Facturado en el período', $facturado, 'moneda', 'fas fa-file-invoice', 'secondary', 'Servicios de consultas con fecha en el rango'),
                $this->kpi('Pagos anulados', (float) $anulados->total, 'moneda', 'fas fa-ban', 'danger', ((int) $anulados->n).' pago(s)'),
            ],
            'grafico' => [
                'tipo' => 'area',
                'titulo' => 'Cobrado por '.($mensual ? 'mes' : 'día'),
                'formato' => 'moneda',
                'labels' => array_map(fn ($c) => $this->etiquetaPeriodo($c, $mensual), array_keys($tabla)),
                'series' => [['name' => 'Cobrado', 'data' => array_map(fn ($f) => round($f['total'], 2), array_values($tabla))]],
            ],
            'grafico2' => [
                'tipo' => 'donut',
                'titulo' => 'Modalidad de cobro',
                'formato' => 'moneda',
                'labels' => ['Efectivo'],
                'series' => [['name' => 'Cobrado', 'data' => [$totalEfectivo]]],
            ],
            'columnas' => [
                ['key' => 'periodo', 'label' => $mensual ? 'Mes' : 'Fecha', 'tipo' => 'texto'],
                ['key' => 'pagos', 'label' => 'Pagos', 'tipo' => 'entero'],
                ['key' => 'efectivo', 'label' => 'Efectivo', 'tipo' => 'moneda'],
                ['key' => 'total', 'label' => 'Total', 'tipo' => 'moneda'],
            ],
            'filas' => $filas,
            'totales' => [
                'periodo' => 'Total',
                'pagos' => $totalPagos,
                'efectivo' => $totalEfectivo,
                'total' => round($totalCobrado, 2),
            ],
            'notas' => [
                'Cobrado: pagos vigentes agrupados por fecha de última actualización (updated_at). Este campo no equivale necesariamente a fecha de cobro si el registro se edita después. Facturado: precios de servicios de consultas del período.',
                'Los pagos anulados no se suman a los ingresos. Su período también se determina mediante updated_at. Único medio de cobro habilitado: efectivo.',
            ],
        ];
    }

    // =====================================================================
    // CUENTAS POR COBRAR
    // =====================================================================

    public function cuentasPorCobrar(Filtros $f): array
    {
        $total = '(SELECT COALESCE(SUM(cs.precio), 0) FROM consulta_servicio cs WHERE cs.consulta_id = c.id)';
        $pagado = "(SELECT COALESCE(SUM(p.monto), 0) FROM pago p WHERE p.consulta_id = c.id AND p.estado = '".Pago::PAGADO."')";
        $saldo = "({$total} - {$pagado})";

        $registros = DB::table('consulta as c')
            ->join('veterinario as v', 'v.id', '=', 'c.veterinario_id')
            ->leftJoin('reserva as r', 'r.id', '=', 'c.reserva_id')
            ->leftJoin('mascota as m', 'm.id', '=', 'r.mascota_id')
            ->leftJoin('cliente as cl', 'cl.id', '=', 'm.cliente_id')
            ->whereBetween('c.fecha', [$f->desde->toDateString(), $f->hasta->toDateString()])
            ->when($f->veterinarioId, fn (Builder $q, int $id) => $q->where('c.veterinario_id', $id))
            ->whereRaw("{$saldo} > 0.004")
            ->selectRaw("c.id, c.fecha, c.tipo, v.nombre as vet_nombre, v.apellido as vet_apellido,
                m.nombre as mascota, cl.nombre as cli_nombre, cl.apellido as cli_apellido, cl.telefono,
                {$total} as total, {$pagado} as pagado, {$saldo} as saldo")
            ->orderBy('c.fecha')
            ->orderBy('c.id')
            ->get();

        $hoy = CarbonImmutable::today();
        $tramos = ['0-7 días' => 0.0, '8-30 días' => 0.0, '31-60 días' => 0.0, 'Más de 60 días' => 0.0];
        $sumaDias = 0;
        $vencido = 0.0;

        $filas = [];
        foreach ($registros as $r) {
            $dias = (int) floor(CarbonImmutable::parse($r->fecha)->startOfDay()->diffInDays($hoy, true));
            $monto = round((float) $r->saldo, 2);

            $tramo = match (true) {
                $dias <= 7 => '0-7 días',
                $dias <= 30 => '8-30 días',
                $dias <= 60 => '31-60 días',
                default => 'Más de 60 días',
            };
            $tramos[$tramo] += $monto;
            $sumaDias += $dias;
            if ($dias > 30) {
                $vencido += $monto;
            }

            $filas[] = [
                'consulta' => (int) $r->id,
                'fecha' => CarbonImmutable::parse($r->fecha)->format('d/m/Y'),
                'tipo' => (string) $r->tipo,
                'cliente' => $this->nombre($r->cli_nombre, $r->cli_apellido),
                'mascota' => $r->mascota ?: '—',
                'telefono' => $r->telefono ?: '—',
                'veterinario' => $this->nombre($r->vet_nombre, $r->vet_apellido),
                'total' => round((float) $r->total, 2),
                'pagado' => round((float) $r->pagado, 2),
                'saldo' => $monto,
                'dias' => $dias,
            ];
        }

        $n = count($filas);
        $totalSaldo = round(array_sum(array_column($filas, 'saldo')), 2);

        return [
            'kpis' => [
                $this->kpi('Total por cobrar', $totalSaldo, 'moneda', 'fas fa-hand-holding-usd', 'danger'),
                $this->kpi('Consultas con saldo', $n, 'entero', 'fas fa-stethoscope', 'info'),
                $this->kpi('Vencido (más de 30 días)', round($vencido, 2), 'moneda', 'fas fa-exclamation-triangle', 'warning'),
                $this->kpi('Antigüedad promedio', $n > 0 ? $sumaDias / $n : 0, 'entero', 'fas fa-hourglass-half', 'secondary', 'días desde la consulta'),
            ],
            'grafico' => [
                'tipo' => 'bar',
                'titulo' => 'Saldo pendiente por antigüedad',
                'formato' => 'moneda',
                'labels' => array_keys($tramos),
                'series' => [['name' => 'Saldo', 'data' => array_map(fn ($v) => round($v, 2), array_values($tramos))]],
            ],
            'grafico2' => null,
            'columnas' => [
                ['key' => 'consulta', 'label' => 'N° consulta', 'tipo' => 'entero'],
                ['key' => 'fecha', 'label' => 'Fecha', 'tipo' => 'texto'],
                ['key' => 'tipo', 'label' => 'Tipo', 'tipo' => 'texto'],
                ['key' => 'cliente', 'label' => 'Cliente', 'tipo' => 'texto'],
                ['key' => 'mascota', 'label' => 'Mascota', 'tipo' => 'texto'],
                ['key' => 'telefono', 'label' => 'Teléfono', 'tipo' => 'texto'],
                ['key' => 'veterinario', 'label' => 'Veterinario', 'tipo' => 'texto'],
                ['key' => 'total', 'label' => 'Total', 'tipo' => 'moneda'],
                ['key' => 'pagado', 'label' => 'Pagado', 'tipo' => 'moneda'],
                ['key' => 'saldo', 'label' => 'Saldo', 'tipo' => 'moneda'],
                ['key' => 'dias', 'label' => 'Días', 'tipo' => 'entero'],
            ],
            'filas' => $filas,
            'totales' => [
                'consulta' => null, 'fecha' => 'Total', 'tipo' => null, 'cliente' => null, 'mascota' => null,
                'telefono' => null, 'veterinario' => null,
                'total' => round(array_sum(array_column($filas, 'total')), 2),
                'pagado' => round(array_sum(array_column($filas, 'pagado')), 2),
                'saldo' => $totalSaldo,
                'dias' => null,
            ],
            'notas' => [
                'Incluye consultas cuya fecha cae en el rango y que aún tienen saldo. Los pagos anulados no se consideran pagados. La antigüedad se cuenta desde la fecha de la consulta hasta hoy.',
            ],
        ];
    }

    // =====================================================================
    // SERVICIOS
    // =====================================================================

    public function servicios(Filtros $f): array
    {
        $filas = $this->consultaServicioBase($f)
            ->join('servicio as s', 's.id', '=', 'cs.servicio_id')
            ->join('sub_servicio as ss', 'ss.id', '=', 's.sub_servicio_id')
            ->selectRaw('s.id, s.nombre as servicio, ss.nombre as categoria, COUNT(*) as cantidad, SUM(cs.precio) as ingresos')
            ->groupBy('s.id', 's.nombre', 'ss.nombre')
            ->orderByDesc('ingresos')
            ->orderBy('s.nombre')
            ->get();

        $totalIngresos = (float) $filas->sum('ingresos');
        $totalCantidad = (int) $filas->sum('cantidad');

        $tabla = $filas->map(fn ($r) => [
            'servicio' => $r->servicio,
            'categoria' => $r->categoria,
            'cantidad' => (int) $r->cantidad,
            'precio_promedio' => $r->cantidad > 0 ? round((float) $r->ingresos / (int) $r->cantidad, 2) : 0,
            'ingresos' => round((float) $r->ingresos, 2),
            'participacion' => $totalIngresos > 0 ? round((float) $r->ingresos / $totalIngresos * 100, 1) : 0,
        ])->values();

        $porCategoria = $filas->groupBy('categoria')
            ->map(fn ($grupo) => round((float) $grupo->sum('ingresos'), 2))
            ->sortDesc();

        $masSolicitado = $filas->sortByDesc('cantidad')->first();
        $top = $tabla->take(10);

        /* servicios prestados, facturado */
        return [
            'kpis' => [
                $this->kpi('Servicios prestados', $totalCantidad, 'entero', 'fas fa-syringe', 'info'),
                $this->kpi('Facturado por servicios', $totalIngresos, 'moneda', 'fas fa-coins', 'success'),
                $this->kpi('Más solicitado', $masSolicitado?->servicio ?? '—', 'texto', 'fas fa-star', 'warning', $masSolicitado ? ((int) $masSolicitado->cantidad).' veces' : null),
                $this->kpi('Categoría líder', $porCategoria->keys()->first() ?? '—', 'texto', 'fas fa-layer-group', 'primary', $porCategoria->isNotEmpty() ? 'Bs '.number_format($porCategoria->first(), 2, '.', ',') : null),
            ],
            'grafico' => [
                'tipo' => 'bar',
                'horizontal' => true,
                'titulo' => 'Top 10 servicios por ingresos',
                'formato' => 'moneda',
                'labels' => $top->pluck('servicio')->all(),
                'series' => [['name' => 'Ingresos', 'data' => $top->pluck('ingresos')->all()]],
            ],
            'grafico2' => [
                'tipo' => 'donut',
                'titulo' => 'Ingresos por categoría',
                'formato' => 'moneda',
                'labels' => $porCategoria->keys()->all(),
                'series' => [['name' => 'Ingresos', 'data' => $porCategoria->values()->all()]],
            ],
            'columnas' => [
                ['key' => 'servicio', 'label' => 'Servicio', 'tipo' => 'texto'],
                ['key' => 'categoria', 'label' => 'Categoría', 'tipo' => 'texto'],
                ['key' => 'cantidad', 'label' => 'Cantidad', 'tipo' => 'entero'],
                ['key' => 'precio_promedio', 'label' => 'Precio promedio', 'tipo' => 'moneda'],
                ['key' => 'ingresos', 'label' => 'Ingresos', 'tipo' => 'moneda'],
                ['key' => 'participacion', 'label' => '% del total', 'tipo' => 'porcentaje'],
            ],
            'filas' => $tabla->all(),
            'totales' => [
                'servicio' => 'Total', 'categoria' => null,
                'cantidad' => $totalCantidad,
                'precio_promedio' => $totalCantidad > 0 ? round($totalIngresos / $totalCantidad, 2) : 0,
                'ingresos' => round($totalIngresos, 2),
                'participacion' => $totalIngresos > 0 ? 100 : 0,
            ],
            'notas' => [
                'Los importes son los precios registrados al momento de cada consulta (no el precio actual del catálogo). El rango se aplica a la fecha de la consulta.',
            ],
        ];
    }

    // =====================================================================
    // VETERINARIOS
    // =====================================================================

    public function veterinarios(Filtros $f): array
    {
        $registros = DB::table('consulta as c')
            ->join('veterinario as v', 'v.id', '=', 'c.veterinario_id')
            ->leftJoin('consulta_servicio as cs', 'cs.consulta_id', '=', 'c.id')
            ->whereBetween('c.fecha', [$f->desde->toDateString(), $f->hasta->toDateString()])
            ->when($f->veterinarioId, fn (Builder $q, int $id) => $q->where('c.veterinario_id', $id))
            ->selectRaw("v.id, v.nombre, v.apellido,
                COUNT(DISTINCT c.id) as consultas,
                COUNT(DISTINCT CASE WHEN c.tipo = 'Emergencia' THEN c.id END) as emergencias,
                COALESCE(SUM(cs.precio), 0) as facturado")
            ->groupBy('v.id', 'v.nombre', 'v.apellido')
            ->orderByDesc('consultas')
            ->orderBy('v.nombre')
            ->get();

        $tabla = $registros->map(fn ($r) => [
            'veterinario' => $this->nombre($r->nombre, $r->apellido),
            'consultas' => (int) $r->consultas,
            'programadas' => (int) $r->consultas - (int) $r->emergencias,
            'emergencias' => (int) $r->emergencias,
            'pct_emergencias' => $r->consultas > 0 ? round((int) $r->emergencias / (int) $r->consultas * 100, 1) : 0,
            'facturado' => round((float) $r->facturado, 2),
            'ticket' => $r->consultas > 0 ? round((float) $r->facturado / (int) $r->consultas, 2) : 0,
        ])->values();

        $totalConsultas = (int) $tabla->sum('consultas');
        $totalEmergencias = (int) $tabla->sum('emergencias');
        $totalFacturado = round((float) $tabla->sum('facturado'), 2);
        $lider = $tabla->first();

        // Desde que se retiró el registro de emergencias, todas las consultas
        // nuevas son programadas. Las columnas de emergencias solo se muestran
        // si el rango incluye consultas de emergencia históricas.
        $hayEmergencias = $totalEmergencias > 0;

        $kpiSecundario = $hayEmergencias
            ? $this->kpi('Emergencias', $totalEmergencias, 'entero', 'fas fa-ambulance', 'danger', number_format($totalEmergencias / $totalConsultas * 100, 1).'% del total')
            : $this->kpi('Facturado por consulta', $totalConsultas > 0 ? round($totalFacturado / $totalConsultas, 2) : 0, 'moneda', 'fas fa-receipt', 'warning');

        $series = $hayEmergencias
            ? [
                ['name' => 'Programadas', 'data' => $tabla->pluck('programadas')->all()],
                ['name' => 'Emergencias', 'data' => $tabla->pluck('emergencias')->all()],
            ]
            : [['name' => 'Consultas', 'data' => $tabla->pluck('consultas')->all()]];

        $columnasEmergencia = $hayEmergencias
            ? [
                ['key' => 'programadas', 'label' => 'Programadas', 'tipo' => 'entero'],
                ['key' => 'emergencias', 'label' => 'Emergencias', 'tipo' => 'entero'],
                ['key' => 'pct_emergencias', 'label' => '% emergencias', 'tipo' => 'porcentaje'],
            ]
            : [];

        return [
            'kpis' => [
                $this->kpi('Consultas', $totalConsultas, 'entero', 'fas fa-stethoscope', 'primary'),
                $kpiSecundario,
                $this->kpi('Facturado', $totalFacturado, 'moneda', 'fas fa-coins', 'success'),
                $this->kpi('Más consultas', $lider['veterinario'] ?? '—', 'texto', 'fas fa-user-md', 'info', $lider ? $lider['consultas'].' consultas' : null),
            ],
            'grafico' => [
                'tipo' => 'bar',
                'apilado' => $hayEmergencias,
                'titulo' => 'Consultas por veterinario',
                'formato' => 'entero',
                'labels' => $tabla->pluck('veterinario')->all(),
                'series' => $series,
            ],
            'grafico2' => [
                'tipo' => 'donut',
                'titulo' => 'Facturado por veterinario',
                'formato' => 'moneda',
                'labels' => $tabla->pluck('veterinario')->all(),
                'series' => [['name' => 'Facturado', 'data' => $tabla->pluck('facturado')->all()]],
            ],
            'columnas' => [
                ['key' => 'veterinario', 'label' => 'Veterinario', 'tipo' => 'texto'],
                ['key' => 'consultas', 'label' => 'Consultas', 'tipo' => 'entero'],
                ...$columnasEmergencia,
                ['key' => 'facturado', 'label' => 'Facturado', 'tipo' => 'moneda'],
                ['key' => 'ticket', 'label' => 'Facturado por consulta', 'tipo' => 'moneda'],
            ],
            'filas' => $tabla->all(),
            'totales' => [
                'veterinario' => 'Total',
                'consultas' => $totalConsultas,
                'programadas' => $totalConsultas - $totalEmergencias,
                'emergencias' => $totalEmergencias,
                'pct_emergencias' => $totalConsultas > 0 ? round($totalEmergencias / $totalConsultas * 100, 1) : 0,
                'facturado' => $totalFacturado,
                'ticket' => $totalConsultas > 0 ? round($totalFacturado / $totalConsultas, 2) : 0,
            ],
            'notas' => [
                'Facturado = suma de los servicios registrados en sus consultas dentro del rango (no implica que estén cobrados).',
            ],
        ];
    }

    // =====================================================================
    // RESERVAS
    // =====================================================================

    public function reservas(Filtros $f): array
    {
        [$mensual, $periodos] = $this->periodos($f);
        $rango = [$f->desde->toDateString(), $f->hasta->toDateString()];

        $lineas = DB::table('reserva')
            ->whereBetween('fecha', $rango)
            ->selectRaw('fecha as dia, estado, COUNT(*) as total')
            ->groupBy('fecha', 'estado')
            ->get();

        $tabla = [];
        foreach ($periodos as $clave) {
            $tabla[$clave] = ['total' => 0, 'atendidas' => 0, 'pendientes' => 0, 'canceladas' => 0, 'no_asistio' => 0];
        }

        foreach ($lineas as $linea) {
            $clave = $this->clavePeriodo((string) $linea->dia, $mensual);
            $columna = match (true) {
                $linea->estado === 'Atendida' => 'atendidas',
                $linea->estado === 'Cancelado' => 'canceladas',
                in_array($linea->estado, self::ESTADOS_NO_ASISTIO, true) => 'no_asistio',
                default => 'pendientes',
            };
            $tabla[$clave][$columna] += (int) $linea->total;
            $tabla[$clave]['total'] += (int) $linea->total;
        }

        $sum = fn (string $col): int => (int) array_sum(array_column($tabla, $col));
        $total = $sum('total');
        $atendidas = $sum('atendidas');
        $canceladas = $sum('canceladas');
        $noAsistio = $sum('no_asistio');
        $pendientes = $sum('pendientes');
        $tasa = fn (int $a, int $b): ?float => ($a + $b) > 0 ? round($a / ($a + $b) * 100, 1) : null;
        $pct = fn (int $parte): float => $total > 0 ? round($parte / $total * 100, 1) : 0.0;

        $filas = [];
        foreach ($tabla as $clave => $t) {
            if ($t['total'] === 0) {
                continue;
            }
            $filas[] = [
                'periodo' => $this->etiquetaPeriodo($clave, $mensual),
                'total' => $t['total'],
                'atendidas' => $t['atendidas'],
                'pendientes' => $t['pendientes'],
                'canceladas' => $t['canceladas'],
                'no_asistio' => $t['no_asistio'],
                'asistencia' => $tasa($t['atendidas'], $t['no_asistio']),
            ];
        }

        $horas = DB::table('reserva')
            ->whereBetween('fecha', $rango)
            ->selectRaw('SUBSTR(hora_inicio, 1, 2) as hora, COUNT(*) as total')
            ->groupByRaw('SUBSTR(hora_inicio, 1, 2)')
            ->orderBy('hora')
            ->get();

        $serie = fn (string $col): array => array_map(fn ($t) => $t[$col], array_values($tabla));

        return [
            'kpis' => [
                $this->kpi('Reservas', $total, 'entero', 'fas fa-calendar-alt', 'primary'),
                $this->kpi('Atendidas', $atendidas, 'entero', 'fas fa-check-circle', 'success', $pct($atendidas).'% del total'),
                $this->kpi('Tasa de asistencia', $tasa($atendidas, $noAsistio) ?? 0, 'porcentaje', 'fas fa-user-check', 'info', 'atendidas / (atendidas + no asistió)'),
                $this->kpi('Canceladas', $canceladas, 'entero', 'fas fa-times-circle', 'warning', $pct($canceladas).'% del total'),
                $this->kpi('No asistió', $noAsistio, 'entero', 'fas fa-user-slash', 'danger', $pendientes.' pendiente(s)'),
            ],
            'grafico' => [
                'tipo' => 'bar',
                'apilado' => true,
                'titulo' => 'Reservas por '.($mensual ? 'mes' : 'día').' y estado',
                'formato' => 'entero',
                'labels' => array_map(fn ($c) => $this->etiquetaPeriodo($c, $mensual), array_keys($tabla)),
                'series' => [
                    ['name' => 'Atendidas', 'data' => $serie('atendidas')],
                    ['name' => 'Pendientes', 'data' => $serie('pendientes')],
                    ['name' => 'Canceladas', 'data' => $serie('canceladas')],
                    ['name' => 'No asistió', 'data' => $serie('no_asistio')],
                ],
            ],
            'grafico2' => [
                'tipo' => 'bar',
                'titulo' => 'Demanda por hora de inicio',
                'formato' => 'entero',
                'labels' => $horas->map(fn ($h) => $h->hora.':00')->all(),
                'series' => [['name' => 'Reservas', 'data' => $horas->map(fn ($h) => (int) $h->total)->all()]],
            ],
            'columnas' => [
                ['key' => 'periodo', 'label' => $mensual ? 'Mes' : 'Fecha', 'tipo' => 'texto'],
                ['key' => 'total', 'label' => 'Reservas', 'tipo' => 'entero'],
                ['key' => 'atendidas', 'label' => 'Atendidas', 'tipo' => 'entero'],
                ['key' => 'pendientes', 'label' => 'Pendientes', 'tipo' => 'entero'],
                ['key' => 'canceladas', 'label' => 'Canceladas', 'tipo' => 'entero'],
                ['key' => 'no_asistio', 'label' => 'No asistió', 'tipo' => 'entero'],
                ['key' => 'asistencia', 'label' => '% asistencia', 'tipo' => 'porcentaje'],
            ],
            'filas' => $filas,
            'totales' => [
                'periodo' => 'Total', 'total' => $total, 'atendidas' => $atendidas, 'pendientes' => $pendientes,
                'canceladas' => $canceladas, 'no_asistio' => $noAsistio, 'asistencia' => $tasa($atendidas, $noAsistio),
            ],
            'notas' => [
                'Fuente: reserva.fecha y reserva.estado. La tasa de asistencia excluye pendientes y canceladas: atendidas / (atendidas + no asistió). No representa el porcentaje sobre todas las reservas.',
                'La actualización de reservas vencidas depende del proceso operativo que marca las inasistencias; este reporte no modifica estados.',
            ],
        ];
    }

    /** Reporte de altas de clientes y mascotas; no incluye datos clínicos. */
    public function clientesMascotas(Filtros $f): array
    {
        [$mensual, $periodos] = $this->periodos($f);
        $tabla = [];
        foreach ($periodos as $clave) {
            $tabla[$clave] = ['clientes' => 0, 'mascotas' => 0];
        }

        foreach (['cliente' => 'clientes', 'mascota' => 'mascotas'] as $tablaOrigen => $campo) {
            $altas = DB::table($tablaOrigen)
                ->whereBetween('created_at', [$f->inicio(), $f->fin()])
                ->selectRaw('DATE(created_at) as dia, COUNT(*) as total')
                ->groupByRaw('DATE(created_at)')
                ->get();
            foreach ($altas as $alta) {
                $clave = $this->clavePeriodo((string) $alta->dia, $mensual);
                if (isset($tabla[$clave])) {
                    $tabla[$clave][$campo] += (int) $alta->total;
                }
            }
        }

        $totalClientes = array_sum(array_column($tabla, 'clientes'));
        $totalMascotas = array_sum(array_column($tabla, 'mascotas'));
        $filas = [];
        foreach ($tabla as $clave => $valores) {
            if ($valores['clientes'] + $valores['mascotas'] === 0) continue;
            $filas[] = ['periodo' => $this->etiquetaPeriodo($clave, $mensual)] + $valores;
        }

        $especies = DB::table('mascota as m')
            ->join('especie as e', 'e.id', '=', 'm.especie_id')
            ->whereBetween('m.created_at', [$f->inicio(), $f->fin()])
            ->selectRaw('e.nombre as especie, COUNT(*) as total')
            ->groupBy('e.id', 'e.nombre')
            ->orderByDesc('total')
            ->get();

        return [
            'kpis' => [
                $this->kpi('Clientes nuevos', $totalClientes, 'entero', 'fas fa-user-plus', 'primary', 'Altas registradas durante el período'),
                $this->kpi('Mascotas nuevas', $totalMascotas, 'entero', 'fas fa-paw', 'success', 'Altas registradas durante el período'),
            ],
            'grafico' => [
                'tipo' => 'bar', 'titulo' => 'Evolución de registros', 'formato' => 'entero',
                'labels' => array_map(fn ($c) => $this->etiquetaPeriodo($c, $mensual), array_keys($tabla)),
                'series' => [
                    ['name' => 'Clientes', 'data' => array_column(array_values($tabla), 'clientes')],
                    ['name' => 'Mascotas', 'data' => array_column(array_values($tabla), 'mascotas')],
                ],
            ],
            'grafico2' => [
                'tipo' => 'donut', 'titulo' => 'Mascotas nuevas por especie', 'formato' => 'entero',
                'labels' => $especies->pluck('especie')->all(),
                'series' => [['name' => 'Mascotas', 'data' => $especies->pluck('total')->map(fn ($v) => (int) $v)->all()]],
            ],
            'columnas' => [
                ['key' => 'periodo', 'label' => $mensual ? 'Mes' : 'Fecha', 'tipo' => 'texto'],
                ['key' => 'clientes', 'label' => 'Clientes nuevos', 'tipo' => 'entero'],
                ['key' => 'mascotas', 'label' => 'Mascotas nuevas', 'tipo' => 'entero'],
            ],
            'filas' => $filas,
            'totales' => ['periodo' => 'Total', 'clientes' => $totalClientes, 'mascotas' => $totalMascotas],
            'notas' => [
                'Fuente: fechas de creación (created_at) de las tablas cliente y mascota; no se incluyen registros creados fuera del rango.',
                'La distribución por especie corresponde a mascotas registradas en el período, no al total histórico.',
                'La razón mascotas/clientes nuevos es descriptiva y no demuestra que las mascotas pertenezcan a los clientes nuevos.',
            ],
        ];
    }

    // =====================================================================
    // Consultas base y utilidades
    // =====================================================================

    /** Pagos del rango (por fecha de registro), filtrados por método y veterinario. */
    private function pagosBase(Filtros $f, string $estado): Builder
    {
        return DB::table('pago')
            ->whereBetween('pago.updated_at', [$f->inicio(), $f->fin()])
            ->where('pago.estado', $estado)
            ->when($f->veterinarioId, fn (Builder $q, int $id) => $q
                ->join('consulta', 'consulta.id', '=', 'pago.consulta_id')
                ->where('consulta.veterinario_id', $id));
    }

    /** Servicios de consultas dentro del rango (alias: cs = consulta_servicio, c = consulta). */
    private function consultaServicioBase(Filtros $f): Builder
    {
        return DB::table('consulta_servicio as cs')
            ->join('consulta as c', 'c.id', '=', 'cs.consulta_id')
            ->whereBetween('c.fecha', [$f->desde->toDateString(), $f->hasta->toDateString()])
            ->when($f->veterinarioId, fn (Builder $q, int $id) => $q->where('c.veterinario_id', $id));
    }

    /**
     * @return array{0: bool, 1: array<int, string>} [¿agrupar por mes?, claves de período]
     */
    private function periodos(Filtros $f): array
    {
        $mensual = $f->dias() > self::UMBRAL_MENSUAL_DIAS;
        $cursor = $mensual ? $f->desde->startOfMonth() : $f->desde;
        $claves = [];

        while ($cursor->lte($f->hasta)) {
            $claves[] = $mensual ? $cursor->format('Y-m') : $cursor->format('Y-m-d');
            $cursor = $mensual ? $cursor->addMonth() : $cursor->addDay();
        }

        return [$mensual, $claves];
    }

    private function clavePeriodo(string $fecha, bool $mensual): string
    {
        return substr($fecha, 0, $mensual ? 7 : 10);
    }

    private function etiquetaPeriodo(string $clave, bool $mensual): string
    {
        return $mensual
            ? CarbonImmutable::parse($clave.'-01')->translatedFormat('M Y')
            : CarbonImmutable::parse($clave)->format('d/m/Y');
    }

    private function nombre(?string $nombre, ?string $apellido): string
    {
        return trim(($nombre ?? '').' '.($apellido ?? '')) ?: '—';
    }

    /** @return array{label:string, valor:mixed, formato:string, icono:string, color:string, detalle:?string} */
    private function kpi(string $label, mixed $valor, string $formato, string $icono, string $color, ?string $detalle = null): array
    {
        return compact('label', 'valor', 'formato', 'icono', 'color', 'detalle');
    }
}
