<?php

use App\Models\Pago;
use App\Services\ReporteService;
use App\Support\CsvExport;
use App\Support\Reportes\Filtros;
use App\Support\ReporteTipo;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

/**
 * Los tests crean su propio esquema mínimo en SQLite en memoria, así son
 * independientes del estado de las migraciones del proyecto.
 */
function crearEsquemaReportes(): void
{
    if (DB::connection()->getDriverName() !== 'sqlite' || DB::connection()->getDatabaseName() !== ':memory:') {
        throw new RuntimeException('Las pruebas de reportes requieren SQLite en memoria.');
    }

    foreach (['pago', 'consulta_servicio', 'consulta', 'reserva', 'mascota', 'cliente', 'servicio', 'sub_servicio', 'veterinario'] as $tabla) {
        Schema::dropIfExists($tabla);
    }

    Schema::create('veterinario', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
        $t->string('apellido')->nullable();
    });
    Schema::create('cliente', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
        $t->string('apellido')->nullable();
        $t->string('telefono')->nullable();
    });
    Schema::create('mascota', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('cliente_id');
        $t->string('nombre');
    });
    Schema::create('reserva', function (Blueprint $t) {
        $t->id();
        $t->date('fecha');
        $t->string('hora_inicio');
        $t->string('hora_fin');
        $t->unsignedBigInteger('mascota_id');
        $t->string('estado');
    });
    Schema::create('consulta', function (Blueprint $t) {
        $t->id();
        $t->date('fecha');
        $t->string('tipo');
        $t->unsignedBigInteger('reserva_id')->nullable();
        $t->unsignedBigInteger('veterinario_id');
    });
    Schema::create('sub_servicio', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
    });
    Schema::create('servicio', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
        $t->unsignedBigInteger('sub_servicio_id');
    });
    Schema::create('consulta_servicio', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('consulta_id');
        $t->unsignedBigInteger('servicio_id');
        $t->double('precio');
    });
    Schema::create('pago', function (Blueprint $t) {
        $t->id();
        $t->decimal('monto', 10, 2);
        $t->enum('estado', ['Pendiente', 'Pagado', 'Anulado'])->default('Pendiente');
        $t->unsignedBigInteger('consulta_id');
        $t->timestamps();
    });
}

function sembrarReportes(): void
{
    DB::table('veterinario')->insert(['id' => 1, 'nombre' => 'Ana', 'apellido' => 'Ruiz']);
    DB::table('cliente')->insert(['id' => 1, 'nombre' => 'Luis', 'apellido' => 'Paz', 'telefono' => '70000000']);
    DB::table('mascota')->insert(['id' => 1, 'cliente_id' => 1, 'nombre' => 'Firulais']);

    foreach ([
        [1, '2026-09-10', 'Atendida'],
        [2, '2026-09-11', 'Atendida'],
        [3, '2026-09-12', 'No asistio'],
        [4, '2026-09-13', 'No asistido'],
        [5, '2026-09-14', 'Cancelado'],
    ] as [$id, $fecha, $estado]) {
        DB::table('reserva')->insert([
            'id' => $id, 'fecha' => $fecha, 'hora_inicio' => '09:00', 'hora_fin' => '09:30',
            'mascota_id' => 1, 'estado' => $estado,
        ]);
    }

    DB::table('consulta')->insert([
        ['id' => 1, 'fecha' => '2026-09-10', 'tipo' => 'Reserva', 'reserva_id' => 1, 'veterinario_id' => 1],
        ['id' => 2, 'fecha' => '2026-09-11', 'tipo' => 'Emergencia', 'reserva_id' => 2, 'veterinario_id' => 1],
    ]);

    DB::table('sub_servicio')->insert(['id' => 1, 'nombre' => 'Vacunas']);
    DB::table('servicio')->insert([
        ['id' => 1, 'nombre' => 'Vacuna rabia', 'sub_servicio_id' => 1],
        ['id' => 2, 'nombre' => 'Consulta general', 'sub_servicio_id' => 1],
    ]);
    DB::table('consulta_servicio')->insert([
        ['consulta_id' => 1, 'servicio_id' => 1, 'precio' => 100],
        ['consulta_id' => 1, 'servicio_id' => 2, 'precio' => 50],
        ['consulta_id' => 2, 'servicio_id' => 2, 'precio' => 80],
    ]);

    DB::table('pago')->insert([
        ['monto' => 100, 'estado' => Pago::PAGADO, 'consulta_id' => 1, 'created_at' => '2026-09-10 12:00:00', 'updated_at' => '2026-09-10 12:00:00'],
        ['monto' => 30, 'estado' => Pago::PAGADO, 'consulta_id' => 1, 'created_at' => '2026-09-11 10:00:00', 'updated_at' => '2026-09-11 10:00:00'],
        ['monto' => 20, 'estado' => Pago::ANULADO, 'consulta_id' => 1, 'created_at' => '2026-09-11 11:00:00', 'updated_at' => '2026-09-11 11:00:00'],
        ['monto' => 80, 'estado' => Pago::PAGADO, 'consulta_id' => 2, 'created_at' => '2026-09-11 15:00:00', 'updated_at' => '2026-09-11 15:00:00'],
    ]);
}

function filtrosSeptiembre(?int $veterinarioId = null, ?string $tipoPago = null): Filtros
{
    return new Filtros(
        CarbonImmutable::parse('2026-09-01'),
        CarbonImmutable::parse('2026-09-30'),
        $veterinarioId,
        $tipoPago,
    );
}

function kpiPorEtiqueta(array $datos, string $etiqueta): mixed
{
    foreach ($datos['kpis'] as $kpi) {
        if ($kpi['label'] === $etiqueta) {
            return $kpi['valor'];
        }
    }

    return null;
}

beforeEach(function () {
    crearEsquemaReportes();
    sembrarReportes();
    $this->reportes = new ReporteService;
});

it('calcula ingresos cobrados, facturados y anulados', function () {
    $datos = $this->reportes->ingresos(filtrosSeptiembre());

    expect(kpiPorEtiqueta($datos, 'Total cobrado'))->toBe(210.0)
        ->and(kpiPorEtiqueta($datos, 'Pagos registrados'))->toBe(3)
        ->and(kpiPorEtiqueta($datos, 'Facturado en el período'))->toBe(230.0)
        ->and(kpiPorEtiqueta($datos, 'Pagos anulados'))->toBe(20.0)
        ->and($datos['totales']['efectivo'])->toBe(210.0)
        ->and($datos['totales']['qr'])->toBe(0.0)
        ->and($datos['totales']['tarjeta'])->toBe(0.0);
});

it('filtra ingresos por método de pago', function () {
    $datos = $this->reportes->ingresos(filtrosSeptiembre(tipoPago: 'QR'));

    expect(kpiPorEtiqueta($datos, 'Total cobrado'))->toBe(0.0);
    $efectivo = $this->reportes->ingresos(filtrosSeptiembre(tipoPago: 'Efectivo'));
    expect(kpiPorEtiqueta($efectivo, 'Total cobrado'))->toBe(210.0);
});

it('agrupa por mes cuando el rango supera los 62 días', function () {
    $filtros = new Filtros(CarbonImmutable::parse('2026-07-01'), CarbonImmutable::parse('2026-09-30'));
    $datos = $this->reportes->ingresos($filtros);

    expect($datos['columnas'][0]['label'])->toBe('Mes')
        ->and($datos['filas'])->toHaveCount(1)
        ->and($datos['totales']['total'])->toBe(210.0);
});

it('lista solo consultas con saldo pendiente', function () {
    $datos = $this->reportes->cuentasPorCobrar(filtrosSeptiembre());

    expect($datos['filas'])->toHaveCount(1)
        ->and($datos['filas'][0]['consulta'])->toBe(1)
        ->and($datos['filas'][0]['cliente'])->toBe('Luis Paz')
        ->and($datos['filas'][0]['saldo'])->toBe(20.0)
        ->and(kpiPorEtiqueta($datos, 'Total por cobrar'))->toBe(20.0);
});

it('ordena los servicios por ingresos y calcula participación', function () {
    $datos = $this->reportes->servicios(filtrosSeptiembre());

    expect($datos['filas'][0]['servicio'])->toBe('Consulta general')
        ->and($datos['filas'][0]['cantidad'])->toBe(2)
        ->and($datos['filas'][0]['ingresos'])->toBe(130.0)
        ->and($datos['totales']['ingresos'])->toBe(230.0);
});

it('mide la productividad por veterinario sin duplicar consultas', function () {
    $datos = $this->reportes->veterinarios(filtrosSeptiembre());

    expect($datos['filas'])->toHaveCount(1)
        ->and($datos['filas'][0]['consultas'])->toBe(2)
        ->and($datos['filas'][0]['emergencias'])->toBe(1)
        ->and($datos['filas'][0]['facturado'])->toBe(230.0);
});

it('oculta las columnas de emergencias cuando el rango no tiene emergencias', function () {
    DB::table('consulta')->where('tipo', 'Emergencia')->update(['tipo' => 'Reserva']);

    $datos = $this->reportes->veterinarios(filtrosSeptiembre());
    $claves = array_column($datos['columnas'], 'key');

    expect($claves)->not->toContain('emergencias')
        ->and($claves)->not->toContain('pct_emergencias')
        ->and(array_column($datos['kpis'], 'label'))->not->toContain('Emergencias')
        ->and($datos['grafico']['series'])->toHaveCount(1)
        ->and($datos['grafico']['apilado'])->toBeFalse();
});

it('cuenta ambas grafías de "no asistió" en el reporte de reservas', function () {
    $datos = $this->reportes->reservas(filtrosSeptiembre());

    expect($datos['totales']['total'])->toBe(5)
        ->and($datos['totales']['atendidas'])->toBe(2)
        ->and($datos['totales']['no_asistio'])->toBe(2)
        ->and($datos['totales']['canceladas'])->toBe(1)
        ->and($datos['totales']['asistencia'])->toBe(50.0);
});

it('exporta CSV con BOM, coma decimal y sin inyección de fórmulas', function () {
    $respuesta = CsvExport::descargar('t.csv', [
        ['key' => 'a', 'label' => 'A', 'tipo' => 'texto'],
        ['key' => 'm', 'label' => 'M', 'tipo' => 'moneda'],
    ], [['a' => '=HYPERLINK("x")', 'm' => 1234.5]]);

    ob_start();
    $respuesta->sendContent();
    $csv = ob_get_clean();

    expect($csv)->toStartWith("\xEF\xBB\xBF")
        ->and($csv)->toContain("'=HYPERLINK")
        ->and($csv)->toContain('1234,50');
});

it('cada reporte exige su propio permiso asignado por el administrador', function () {
    $usuario = fn (bool $esAdmin, array $permisos) => new class($esAdmin, $permisos)
    {
        public function __construct(private bool $esAdmin, private array $permisos) {}

        public function esAdministrador(): bool
        {
            return $this->esAdmin;
        }

        public function tienePermiso(string $clave): bool
        {
            return $this->esAdmin || in_array($clave, $this->permisos, true);
        }
    };

    $catalogo = ReporteTipo::catalogo();
    $admin = $usuario(true, []);
    $operativo = $usuario(false, ['reportes.servicios']);

    expect(Gate::forUser($admin)->allows($catalogo[ReporteTipo::INGRESOS]['permiso']))->toBeTrue()
        ->and(Gate::forUser($admin)->allows($catalogo[ReporteTipo::POR_COBRAR]['permiso']))->toBeTrue()
        ->and(Gate::forUser($operativo)->allows($catalogo[ReporteTipo::SERVICIOS]['permiso']))->toBeTrue()
        ->and(Gate::forUser($operativo)->allows($catalogo[ReporteTipo::INGRESOS]['permiso']))->toBeFalse()
        ->and(Gate::forUser($operativo)->allows($catalogo[ReporteTipo::POR_COBRAR]['permiso']))->toBeFalse()
        ->and(Gate::forUser($usuario(false, []))->allows($catalogo[ReporteTipo::RESERVAS]['permiso']))->toBeFalse();
});

it('exige iniciar sesión para ver reportes', function () {
    $this->get('/reportes')->assertRedirect(route('login'));
    $this->get('/reportes/ingresos')->assertRedirect(route('login'));
    $this->get('/reportes/ingresos/csv')->assertRedirect(route('login'));
});

it('excluye pendientes de ingresos y mantiene el saldo por cobrar', function () {
    DB::table('pago')->insert([
        'consulta_id' => 1, 'monto' => 20, 'estado' => Pago::PENDIENTE,
        'created_at' => '2026-09-11 12:00:00', 'updated_at' => '2026-09-11 12:00:00',
    ]);
    $ingresos = $this->reportes->ingresos(filtrosSeptiembre());
    $cuentas = $this->reportes->cuentasPorCobrar(filtrosSeptiembre());
    expect(kpiPorEtiqueta($ingresos, 'Total cobrado'))->toBe(210.0)
        ->and(kpiPorEtiqueta($ingresos, 'Pagos anulados'))->toBe(20.0)
        ->and(kpiPorEtiqueta($cuentas, 'Total por cobrar'))->toBe(20.0);
});

it('contabiliza el cobro en la fecha de confirmacion del pendiente', function () {
    DB::table('pago')->insert([
        'consulta_id' => 1, 'monto' => 20, 'estado' => Pago::PAGADO,
        'created_at' => '2026-08-31 12:00:00', 'updated_at' => '2026-09-01 12:00:00',
    ]);
    $datos = $this->reportes->ingresos(filtrosSeptiembre());
    expect(kpiPorEtiqueta($datos, 'Total cobrado'))->toBe(230.0)
        ->and(kpiPorEtiqueta($datos, 'Pagos registrados'))->toBe(4);
});

it('contabiliza las anulaciones en su fecha y no en la creacion del pago', function () {
    DB::table('pago')->insert([
        ['consulta_id' => 1, 'monto' => 30, 'estado' => Pago::ANULADO,
            'created_at' => '2026-08-31 12:00:00', 'updated_at' => '2026-09-01 12:00:00'],
        ['consulta_id' => 1, 'monto' => 40, 'estado' => Pago::ANULADO,
            'created_at' => '2026-09-30 12:00:00', 'updated_at' => '2026-10-01 12:00:00'],
    ]);
    $datos = $this->reportes->ingresos(filtrosSeptiembre());
    expect(kpiPorEtiqueta($datos, 'Pagos anulados'))->toBe(50.0);
});
