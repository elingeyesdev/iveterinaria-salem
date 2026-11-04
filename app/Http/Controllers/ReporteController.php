<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReporteFiltroRequest;
use App\Models\Veterinario;
use App\Services\ReporteService;
use App\Support\CsvExport;
use App\Support\ReporteTipo;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controlador delgado: valida (FormRequest), autoriza (Gate) y delega la
 * lógica de datos en ReporteService.
 */
class ReporteController extends Controller
{
    private const POR_PAGINA = 25;

    public function __construct(private readonly ReporteService $reportes) {}

    public function index(): View
    {
        $disponibles = collect(ReporteTipo::catalogo())
            ->filter(fn (array $def) => Gate::allows($def['permiso']));

        abort_if($disponibles->isEmpty(), 403, 'No tienes permisos para acceder a los reportes.');

        return view('reportes.index', ['reportes' => $disponibles]);
    }

    public function show(string $tipo, ReporteFiltroRequest $request): View
    {
        $def = $this->definicion($tipo);
        $filtros = $request->filtros();
        $datos = $this->reportes->generar($tipo, $filtros);

        $pagina = max(1, (int) $request->query('page', 1));
        $filas = new LengthAwarePaginator(
            array_slice($datos['filas'], ($pagina - 1) * self::POR_PAGINA, self::POR_PAGINA),
            count($datos['filas']),
            self::POR_PAGINA,
            $pagina,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('reportes.show', [
            'tipo' => $tipo,
            'def' => $def,
            'filtros' => $filtros,
            'datos' => $datos,
            'filas' => $filas,
            'veterinarios' => in_array('veterinario', $def['filtros'], true) ? $this->veterinarios() : collect(),
            'atajos' => $this->atajos(),
        ]);
    }

    public function csv(string $tipo, ReporteFiltroRequest $request): StreamedResponse
    {
        $this->definicion($tipo);
        $filtros = $request->filtros();
        $datos = $this->reportes->generar($tipo, $filtros);

        $filas = $datos['filas'];
        if (! empty($datos['totales'])) {
            $filas[] = $datos['totales'];
        }

        $this->auditar('csv', $tipo, $filtros->toQuery());

        return CsvExport::descargar(
            sprintf('reporte-%s_%s_%s.csv', $tipo, $filtros->desde->format('Ymd'), $filtros->hasta->format('Ymd')),
            $datos['columnas'],
            $filas,
        );
    }

    public function imprimir(string $tipo, ReporteFiltroRequest $request): View
    {
        $def = $this->definicion($tipo);
        $filtros = $request->filtros();

        $this->auditar('imprimir', $tipo, $filtros->toQuery());

        return view('reportes.imprimir', [
            'def' => $def,
            'filtros' => $filtros,
            'datos' => $this->reportes->generar($tipo, $filtros),
            'veterinario' => $filtros->veterinarioId
                ? Veterinario::find($filtros->veterinarioId, ['id', 'nombre', 'apellido'])
                : null,
            'generadoPor' => $request->user()->name,
            'generadoEn' => CarbonImmutable::now(),
        ]);
    }

    /** @return array{titulo:string, descripcion:string, icono:string, permiso:string, filtros:array<int,string>} */
    private function definicion(string $tipo): array
    {
        $def = ReporteTipo::catalogo()[$tipo] ?? abort(404);

        Gate::authorize($def['permiso']);

        return $def;
    }

    private function veterinarios()
    {
        return Veterinario::orderBy('nombre')->orderBy('apellido')->get(['id', 'nombre', 'apellido']);
    }

    /** Rangos rápidos para la barra de filtros. */
    private function atajos(): array
    {
        $hoy = CarbonImmutable::today();
        $rango = fn (CarbonImmutable $d, CarbonImmutable $h) => ['desde' => $d->toDateString(), 'hasta' => $h->toDateString()];

        return [
            'Hoy' => $rango($hoy, $hoy),
            'Últimos 7 días' => $rango($hoy->subDays(6), $hoy),
            'Este mes' => $rango($hoy->startOfMonth(), $hoy),
            'Mes anterior' => $rango($hoy->subMonthNoOverflow()->startOfMonth(), $hoy->subMonthNoOverflow()->endOfMonth()),
            'Este año' => $rango($hoy->startOfYear(), $hoy),
        ];
    }

    /** Traza de quién extrae información financiera/operativa del sistema. */
    private function auditar(string $accion, string $tipo, array $filtros): void
    {
        Log::info('Reporte generado', [
            'accion' => $accion,
            'reporte' => $tipo,
            'filtros' => $filtros,
            'user_id' => request()->user()?->getAuthIdentifier(),
            'ip' => request()->ip(),
        ]);
    }
}
