<?php

use App\Models\Consulta;
use App\Models\Pago;
use App\Models\Permiso;
use App\Models\Role;
use App\Models\User;
use App\Services\ConsultaService;
use App\Services\ConsultaServicioService;
use App\Support\PermisoCatalogo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Esquema mínimo en SQLite en memoria, independiente de las migraciones
 * del proyecto (mismo enfoque que ReporteServiceTest).
 */
function crearEsquemaPagos(): void
{
    if (DB::connection()->getDriverName() !== 'sqlite' || DB::connection()->getDatabaseName() !== ':memory:') {
        throw new RuntimeException('Las pruebas de pagos requieren SQLite en memoria.');
    }

    foreach (['pago', 'consulta_servicio', 'consulta', 'reserva', 'mascota', 'cliente', 'servicio', 'permiso_user', 'permisos', 'role_user', 'roles', 'users'] as $tabla) {
        Schema::dropIfExists($tabla);
    }

    Schema::create('users', function (Blueprint $t) {
        $t->id();
        $t->string('name');
        $t->string('email');
        $t->string('password');
        $t->tinyInteger('estado')->default(1);
        $t->rememberToken();
        $t->timestamps();
    });
    Schema::create('roles', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
        $t->tinyInteger('estado')->default(1);
        $t->timestamps();
    });
    Schema::create('role_user', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('user_id');
        $t->unsignedBigInteger('role_id');
        $t->timestamps();
    });
    Schema::create('permisos', function (Blueprint $t) {
        $t->id();
        $t->string('clave', 80)->unique();
        $t->string('nombre', 100);
        $t->string('modulo', 50);
        $t->string('descripcion')->nullable();
        $t->unsignedSmallInteger('orden')->default(0);
        $t->timestamps();
    });
    Schema::create('permiso_user', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('user_id');
        $t->unsignedBigInteger('permiso_id');
        $t->timestamps();
    });
    Schema::create('servicio', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
    });
    Schema::create('cliente', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
        $t->string('apellido');
    });
    Schema::create('mascota', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
        $t->unsignedBigInteger('cliente_id');
    });
    Schema::create('reserva', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('mascota_id');
    });
    Schema::create('consulta', function (Blueprint $t) {
        $t->id();
        $t->date('fecha');
        $t->string('estado_clinico')->default(Consulta::EN_ATENCION);
        $t->timestamp('finalizada_at')->nullable();
        $t->string('tipo')->default('Reserva');
        $t->double('peso');
        $t->unsignedBigInteger('veterinario_id');
        $t->unsignedBigInteger('reserva_id')->nullable();
        $t->timestamps();
    });
    Schema::create('consulta_servicio', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('consulta_id');
        $t->unsignedBigInteger('servicio_id');
        $t->date('fecha_actual')->nullable();
        $t->date('fecha_prox')->nullable();
        $t->double('peso')->nullable();
        $t->decimal('precio', 10, 2);
        $t->timestamps();
    });
    Schema::create('pago', function (Blueprint $t) {
        $t->id();
        $t->decimal('monto', 10, 2);
        $t->enum('estado', ['Pendiente', 'Pagado', 'Anulado'])->default('Pendiente');
        $t->unsignedBigInteger('consulta_id');
        $t->timestamps();
    });
}

/**
 * Crea una consulta con un único servicio del precio indicado.
 */
function consultaConTotal(float $precio): Consulta
{
    $consultaId = DB::table('consulta')->insertGetId([
        'fecha' => now()->toDateString(),
        'peso' => 10,
        'veterinario_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $servicioId = DB::table('servicio')->insertGetId(['nombre' => 'Consulta general']);
    DB::table('consulta_servicio')->insert([
        'consulta_id' => $consultaId,
        'servicio_id' => $servicioId,
        'precio' => $precio,
    ]);

    return Consulta::findOrFail($consultaId);
}

/**
 * Crea un usuario con el rol y los permisos (claves del catálogo) indicados.
 *
 * @param  array<int, string>  $permisos
 */
function usuarioDeCaja(string $rol, array $permisos = []): User
{
    $usuario = User::forceCreate([
        'name' => ucfirst($rol),
        'email' => $rol.'@example.com',
        'password' => bcrypt('secret'),
    ]);
    $usuario->roles()->attach(Role::firstOrCreate(['nombre' => $rol], ['estado' => 1])->id);
    $usuario->permisos()->attach(Permiso::whereIn('clave', $permisos)->pluck('id'));

    return $usuario->fresh();
}

beforeEach(function () {
    crearEsquemaPagos();
    Permiso::sincronizarCatalogo();

    $this->actingAs(usuarioDeCaja('user', [PermisoCatalogo::CONSULTAS, PermisoCatalogo::PAGOS]));
});

it('rechaza abonos y conserva el saldo completo', function () {
    $consulta = consultaConTotal(150);

    $this->from(route('consulta.show', $consulta))
        ->post(route('consulta.pagos.store', $consulta), ['monto' => 100])
        ->assertRedirect(route('consulta.show', $consulta))
        ->assertSessionMissing('success')
        ->assertSessionHas('error');
    expect(Pago::count())->toBe(0);
    expect($consulta->fresh()->saldo_pendiente)->toBe(150.0);
});

it('rechaza un segundo cobro de una consulta ya pagada', function () {
    $consulta = consultaConTotal(150);
    $this->post(route('consulta.pagos.store', $consulta), ['monto' => 150])->assertSessionHas('success');
    $this->post(route('consulta.pagos.store', $consulta), ['monto' => 150])->assertSessionHas('error');
    expect(Pago::count())->toBe(1);
    expect($consulta->fresh()->saldo_pendiente)->toBe(0.0);
});

it('registra el total en efectivo con estado pagado', function () {
    $consulta = consultaConTotal(80.25);
    $this->post(route('consulta.pagos.store', $consulta), ['monto' => '80.25'])
        ->assertSessionHas('success');
    expect(Pago::firstOrFail()->estado)->toBe(Pago::PAGADO)
        ->and(Pago::firstOrFail()->monto)->toBe('80.25')
        ->and($consulta->fresh()->saldo_pendiente)->toBe(0.0);
});

it('no cuenta los pagos anulados al calcular el saldo', function () {
    $consulta = consultaConTotal(100);
    Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => Pago::ANULADO]);

    expect($consulta->fresh()->saldo_pendiente)->toBe(100.0);

    $this->post(route('consulta.pagos.store', $consulta), ['monto' => 100])
        ->assertSessionHas('success');
    expect($consulta->fresh()->saldo_pendiente)->toBe(0.0);
});

it('rechaza un pago que excede el saldo pendiente', function () {
    $consulta = consultaConTotal(50);

    $this->post(route('consulta.pagos.store', $consulta), ['monto' => 60])
        ->assertSessionHas('error', 'El pago en efectivo debe ser exactamente Bs 50.00.');

    expect(Pago::count())->toBe(0);
});

it('al anular un pago la consulta vuelve a tener saldo pendiente', function () {
    $consulta = consultaConTotal(150);
    $this->post(route('consulta.pagos.store', $consulta), ['monto' => 150])->assertSessionHas('success');
    $pago = Pago::firstOrFail();

    $this->actingAs(usuarioDeCaja('admin'))
        ->patch(route('consulta.pagos.anular', [$consulta, $pago]))
        ->assertSessionHas('warning', 'Anulación registrada. Los movimientos se conservan en el historial. Saldo pendiente: Bs 150.00.');

    expect($pago->fresh()->estado)->toBe(Pago::ANULADO)
        ->and($consulta->fresh()->saldo_pendiente)->toBe(150.0);
});

it('solo el administrador puede anular un pago', function () {
    $consulta = consultaConTotal(100);
    $this->post(route('consulta.pagos.store', $consulta), ['monto' => 100]);
    $pago = Pago::firstOrFail();

    $this->patch(route('consulta.pagos.anular', [$consulta, $pago]))->assertForbidden();

    expect($pago->fresh()->estado)->toBe(Pago::PAGADO);
});

it('sin el permiso «Cobrar consultas» no se puede registrar un pago', function () {
    $consulta = consultaConTotal(100);

    $this->actingAs(usuarioDeCaja('veterinario', [PermisoCatalogo::CONSULTAS]))
        ->post(route('consulta.pagos.store', $consulta), ['monto' => 100])
        ->assertForbidden();

    expect(Pago::count())->toBe(0);
});

it('sin el permiso «Consultas y emergencias» no se entra al módulo', function () {
    $consulta = consultaConTotal(100);

    $this->actingAs(usuarioDeCaja('veterinario', []))
        ->post(route('consulta.pagos.store', $consulta), ['monto' => 100])
        ->assertForbidden();
});

it('abre el listado independiente de pagos', function () {
    $this->get(route('pagos.index'))->assertOk()->assertSee('Administrar Pagos');
});

it('permite seleccionar una consulta y abrir su cuenta en pagos', function () {
    $consulta = consultaConTotal(150);
    $this->get(route('pagos.create'))->assertOk()->assertSee('Selecciona la consulta');
    $this->get(route('pagos.create', ['consulta_id' => $consulta->id]))
        ->assertOk()->assertSee('Cobrar consulta completa en efectivo')->assertSee('150.00');
});

it('registra cobros completos desde las rutas independientes de pagos', function () {
    $consulta = consultaConTotal(150);
    $this->from(route('pagos.create', ['consulta_id' => $consulta->id]))
        ->post(route('pagos.store', $consulta), ['monto' => 150])
        ->assertRedirect(route('pagos.create', ['consulta_id' => $consulta->id]))
        ->assertSessionHas('success');
    expect($consulta->fresh()->saldo_pendiente)->toBe(0.0);
});

it('lista todos los estados cuando no se selecciona un filtro', function () {
    $consulta = consultaConTotal(150);
    foreach ([Pago::PENDIENTE, Pago::PAGADO, Pago::ANULADO] as $estado) {
        Pago::create(['consulta_id' => $consulta->id, 'monto' => 150, 'estado' => $estado]);
    }
    $this->get(route('pagos.index'))->assertOk()
        ->assertViewHas('pagos', fn ($pagos) => $pagos->total() === 3)
        ->assertSee('name="estado"', false)->assertDontSee('name="search"', false);
});

it('protege el modulo de pagos con su permiso propio', function () {
    $consulta = consultaConTotal(100);
    $this->actingAs(usuarioDeCaja('veterinario', [PermisoCatalogo::CONSULTAS]));
    $this->get(route('pagos.index'))->assertForbidden();
    $this->get(route('pagos.create'))->assertForbidden();
    $this->post(route('pagos.store', $consulta), ['monto' => 50])->assertForbidden();
});

it('rechaza sobrepagos y anulaciones no autorizadas en el modulo independiente', function () {
    $consulta = consultaConTotal(100);
    $this->post(route('pagos.store', $consulta), ['monto' => 110])->assertSessionHas('error');
    expect(Pago::count())->toBe(0);
    $pago = Pago::create(['consulta_id' => $consulta->id, 'monto' => 50, 'estado' => Pago::PAGADO]);
    $this->patch(route('pagos.anular', [$consulta, $pago]))->assertForbidden();
    $this->actingAs(usuarioDeCaja('admin'))->patch(route('pagos.anular', [$consulta, $pago]))
        ->assertSessionHas('warning');
    expect($pago->fresh()->estado)->toBe(Pago::ANULADO)->and(Pago::count())->toBe(1);
});

it('no permite anular un pago de otra consulta', function () {
    $consulta = consultaConTotal(100);
    $otraConsulta = consultaConTotal(200);
    $pago = Pago::create(['consulta_id' => $consulta->id, 'monto' => 50, 'estado' => Pago::PAGADO]);
    $this->actingAs(usuarioDeCaja('admin'))->patch(route('pagos.anular', [$otraConsulta, $pago]))->assertNotFound();
    expect($pago->fresh()->estado)->toBe(Pago::PAGADO);
});

it('conserva un pago anulado en el listado y el historial', function () {
    $this->actingAs(usuarioDeCaja('admin'));
    $consulta = consultaConTotal(150);
    $pago = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => Pago::PAGADO]);
    $this->get(route('pagos.index'))
        ->assertViewHas('pagos', fn ($pagos) => $pagos->contains('id', $pago->id));
    $this->from(route('pagos.index'))
        ->patch(route('pagos.anular', [$consulta, $pago]))
        ->assertRedirect(route('pagos.index'))->assertSessionHas('warning');
    $this->get(route('pagos.index'))
        ->assertViewHas('pagos', fn ($pagos) => $pagos->contains('id', $pago->id));
    expect($pago->fresh()->estado)->toBe(Pago::ANULADO);
    $this->get(route('pagos.index', ['estado' => Pago::ANULADO]))
        ->assertViewHas('pagos', fn ($pagos) => $pagos->contains('id', $pago->id));
});

it('rechaza montos invalidos sin registrar movimientos', function (mixed $monto) {
    $consulta = consultaConTotal(100);
    $this->post(route('pagos.store', $consulta), ['monto' => $monto])
        ->assertSessionHasErrors('monto');
    expect(Pago::count())->toBe(0);
})->with([null, 0, -1, 'abc', '100.001', '100000000']);

it('rechaza el cobro de consultas sin servicios cobrables', function () {
    $consulta = consultaConTotal(0);
    $this->post(route('pagos.store', $consulta), ['monto' => 1])->assertSessionHas('error');
    expect(Pago::count())->toBe(0);
});

it('permite volver a cobrar tras anular sin borrar el historial', function () {
    $this->actingAs(usuarioDeCaja('admin'));
    $consulta = consultaConTotal(100);
    $this->post(route('pagos.store', $consulta), ['monto' => 100])->assertSessionHas('success');
    $pago = Pago::firstOrFail();
    $this->patch(route('pagos.anular', [$consulta, $pago]))->assertSessionHas('warning');
    $this->patch(route('pagos.anular', [$consulta, $pago]))->assertSessionHas('error');
    $this->post(route('pagos.store', $consulta), ['monto' => 100])->assertSessionHas('success');
    expect(Pago::count())->toBe(2)
        ->and($pago->fresh()->estado)->toBe(Pago::ANULADO)
        ->and($consulta->fresh()->saldo_pendiente)->toBe(0.0);
});

it('ignora parametros antiguos sin conservarlos en la paginacion', function () {
    $consulta = consultaConTotal(100);
    foreach (range(1, 6) as $numero) {
        Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    }
    $this->get(route('pagos.index', ['search' => 'antiguo', 'consulta_id' => 'invalido', 'perPage' => 5]))
        ->assertOk()->assertViewHas('pagos', fn ($pagos) => $pagos->total() === 6
            && ! str_contains($pagos->nextPageUrl(), 'search')
            && ! str_contains($pagos->nextPageUrl(), 'consulta_id'));
});

it('confirma un pendiente sin duplicarlo y solo entonces reduce el saldo', function () {
    $consulta = consultaConTotal(100);
    $pago = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    expect($pago->estado)->toBe(Pago::PENDIENTE)
        ->and($consulta->fresh()->saldo_pendiente)->toBe(100.0);
    $this->get(route('pagos.index', ['estado' => Pago::PENDIENTE]))
        ->assertOk()->assertSee('Pendiente')
        ->assertViewHas('pagos', fn ($pagos) => $pagos->total() === 1);
    $this->get(route('pagos.create', ['consulta_id' => $consulta->id]))
        ->assertRedirect(route('pagos.show', $pago));
    $this->get(route('pagos.show', $pago))->assertOk()->assertSee('Pendiente')->assertSee('Confirmar cobro completo');
    $this->post(route('pagos.store', $consulta), ['monto' => 100])->assertSessionHas('success');
    expect(Pago::count())->toBe(1)
        ->and($pago->fresh()->estado)->toBe(Pago::PAGADO)
        ->and($consulta->fresh()->saldo_pendiente)->toBe(0.0);
});

it('anula un pendiente sin anular el cobro confirmado', function () {
    $this->actingAs(usuarioDeCaja('admin'));
    $consulta = consultaConTotal(100);
    $pendiente = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    $pagado = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => Pago::PAGADO]);
    $this->patch(route('pagos.anular', [$consulta, $pendiente]))->assertSessionHas('warning');
    expect($pendiente->fresh()->estado)->toBe(Pago::ANULADO)
        ->and($pagado->fresh()->estado)->toBe(Pago::PAGADO)
        ->and($consulta->fresh()->saldo_pendiente)->toBe(0.0);
});

it('no confirma pendientes duplicados ni modifica sus montos', function () {
    $consulta = consultaConTotal(100);
    Pago::create(['consulta_id' => $consulta->id, 'monto' => 40]);
    Pago::create(['consulta_id' => $consulta->id, 'monto' => 60]);
    $this->post(route('pagos.store', $consulta), ['monto' => 100])->assertSessionHas('error');
    expect(Pago::where('estado', Pago::PENDIENTE)->count())->toBe(2)
        ->and(Pago::where('estado', Pago::PAGADO)->count())->toBe(0);
});

it('protege los servicios de una consulta pagada', function () {
    $consulta = consultaConTotal(100);
    Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => Pago::PAGADO]);
    expect(fn () => DB::transaction(fn () => app(ConsultaServicioService::class)
        ->sincronizar($consulta, [], true)))->toThrow(DomainException::class);
    expect($consulta->fresh()->total_servicios)->toBe(100.0);
});

it('un pendiente no bloquea los servicios antes del cobro', function () {
    $consulta = consultaConTotal(100);
    Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    DB::transaction(fn () => app(ConsultaServicioService::class)
        ->sincronizar($consulta, [], true));
    expect($consulta->fresh()->total_servicios)->toBe(0.0);
});

it('filtra pagos exclusivamente por el estado validado', function (string $estado) {
    $consulta = consultaConTotal(100);
    foreach ([Pago::PENDIENTE, Pago::PAGADO, Pago::ANULADO] as $valor) {
        Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => $valor]);
    }
    $this->get(route('pagos.index', ['estado' => $estado, 'search' => 'ignorar']))->assertOk()
        ->assertViewHas('pagos', fn ($pagos) => $pagos->total() === 1 && $pagos->first()->estado === $estado);
    $this->get(route('pagos.index', ['estado' => $estado]), ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()->assertViewIs('pagos._table')
        ->assertViewHas('pagos', fn ($pagos) => $pagos->total() === 1 && $pagos->first()->estado === $estado);
})->with([Pago::PENDIENTE, Pago::PAGADO, Pago::ANULADO]);

it('pagina pagos y devuelve solo la tabla en busquedas dinamicas', function () {
    $consulta = consultaConTotal(100);
    foreach (range(1, 12) as $numero) {
        Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => Pago::PAGADO]);
    }
    $this->get(route('pagos.index'))->assertOk()
        ->assertViewHas('pagos', fn ($pagos) => $pagos->count() === 12);
    $this->get(route('pagos.index', ['perPage' => 'all', 'page' => 2]))->assertOk()
        ->assertViewHas('pagos', fn ($pagos) => $pagos->count() === 12 && ! $pagos->hasPages());
    $this->get(route('pagos.index', ['perPage' => 5]))->assertOk()
        ->assertViewHas('pagos', fn ($pagos) => $pagos->count() === 5);
    $this->get(route('pagos.index', ['perPage' => 10, 'page' => 2]), ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()->assertViewIs('pagos._table')
        ->assertViewHas('pagos', fn ($pagos) => $pagos->count() === 2 && $pagos->firstItem() === 11)
        ->assertDontSee('pagos-search-form');
});

it('valida los parametros del listado de pagos', function () {
    $this->get(route('pagos.index', ['perPage' => 1000, 'estado' => 'Incorrecto']))
        ->assertSessionHasErrors(['perPage', 'estado']);
});

it('permite ver la cuenta a usuarios con permiso de pagos', function () {
    $consulta = consultaConTotal(100);
    $pago = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => Pago::PAGADO]);
    $this->get(route('pagos.show', $pago))->assertOk()->assertSee('Cuenta del pago #'.$pago->id);
    $this->get(route('pagos.index'))->assertOk()
        ->assertSee('disabled aria-disabled="true"', false)
        ->assertSee('Pagar')
        ->assertDontSee(route('pagos.show', $pago), false)
        ->assertDontSee('form-anular-pago', false);
    $this->actingAs(usuarioDeCaja('veterinario', []));
    $this->get(route('pagos.show', $pago))->assertForbidden();
});

it('no muestra la accion anular en el listado de pagos', function () {
    $this->actingAs(usuarioDeCaja('admin'));
    $consulta = consultaConTotal(100);
    Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => Pago::PAGADO]);
    $this->get(route('pagos.index'))
        ->assertDontSee('form-anular-pago', false)
        ->assertDontSee('Anular');
});

it('anula solo el pago seleccionado y conserva otros movimientos', function () {
    $this->actingAs(usuarioDeCaja('admin'));
    $consulta = consultaConTotal(100);
    $pago = Pago::create(['consulta_id' => $consulta->id, 'monto' => 40, 'estado' => Pago::PAGADO]);
    $otro = Pago::create(['consulta_id' => $consulta->id, 'monto' => 60, 'estado' => Pago::PAGADO]);
    $this->patch(route('pagos.anular', [$consulta, $pago]))->assertSessionHas('warning');
    expect($pago->fresh()->estado)->toBe(Pago::ANULADO)
        ->and($otro->fresh()->estado)->toBe(Pago::PAGADO)
        ->and($consulta->fresh()->saldo_pendiente)->toBe(40.0);
});

it('impide eliminar pagos incluso al administrador', function () {
    $this->actingAs(usuarioDeCaja('admin'));
    $consulta = consultaConTotal(100);
    $pago = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    expect(auth()->user()->can('delete', $pago))->toBeFalse()
        ->and(auth()->user()->can('forceDelete', $pago))->toBeFalse();
    $this->delete(route('pagos.show', $pago))->assertStatus(405);
    expect(Pago::count())->toBe(1);
});

it('al finalizar la atencion genera un pago pendiente visible en caja', function () {
    $consulta = consultaConTotal(150.25);
    $this->actingAs(usuarioDeCaja('admin'));
    $this->post(route('consulta.completar', $consulta))->assertSessionHas('success');
    $pago = Pago::firstOrFail();
    expect($pago->estado)->toBe(Pago::PENDIENTE)
        ->and($pago->monto)->toBe('150.25')
        ->and($pago->consulta_id)->toBe($consulta->id)
        ->and($consulta->fresh()->estado_clinico)->toBe(Consulta::FINALIZADA)
        ->and($consulta->fresh()->total_pagado)->toBe(0.0);
    $this->get(route('pagos.index'))->assertOk()->assertSee('Pendiente')
        ->assertSee(route('pagos.show', $pago), false);
    $this->post(route('pagos.store', $consulta), ['monto' => '150.25'])->assertSessionHas('success');
    expect(Pago::count())->toBe(1)->and($pago->fresh()->estado)->toBe(Pago::PAGADO);
});

it('reutiliza el pendiente y no duplica pagos al repetir la finalizacion', function () {
    $consulta = consultaConTotal(150);
    $pago = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    $service = app(ConsultaService::class);
    $service->finalizar($consulta);
    $fecha = $consulta->fresh()->finalizada_at;
    $service->finalizar($consulta);
    expect(Pago::count())->toBe(1)->and($pago->fresh()->monto)->toBe('150.00')
        ->and($consulta->fresh()->finalizada_at->equalTo($fecha))->toBeTrue();
});

it('no genera pendientes para consultas ya pagadas o sin cargos', function (float $total) {
    $consulta = consultaConTotal($total);
    if ($total > 0) {
        Pago::create(['consulta_id' => $consulta->id, 'monto' => $total, 'estado' => Pago::PAGADO]);
    }
    app(ConsultaService::class)->finalizar($consulta);
    expect(Pago::where('estado', Pago::PENDIENTE)->count())->toBe(0)
        ->and($consulta->fresh()->estado_clinico)->toBe(Consulta::FINALIZADA);
})->with([0.0, 100.0]);

it('revierte la finalizacion si no puede preparar un unico pendiente', function () {
    $consulta = consultaConTotal(150);
    Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    Pago::create(['consulta_id' => $consulta->id, 'monto' => 50]);
    expect(fn () => app(ConsultaService::class)->finalizar($consulta))->toThrow(DomainException::class);
    expect($consulta->fresh()->estado_clinico)->toBe(Consulta::EN_ATENCION)
        ->and($consulta->fresh()->finalizada_at)->toBeNull()
        ->and(Pago::count())->toBe(2);
});

it('registrar pago abre la misma cuenta que pagar priorizando el pendiente', function () {
    $consulta = consultaConTotal(100);
    $pendiente = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => Pago::ANULADO]);
    $this->get(route('pagos.create', ['consulta_id' => $consulta->id]))
        ->assertRedirect(route('pagos.show', $pendiente));
    $this->get(route('pagos.show', $pendiente))->assertOk()->assertSee('Confirmar cobro completo');
    expect(Pago::count())->toBe(2);
});

it('el listado enlaza a la cuenta del pago pendiente', function () {
    $consulta = consultaConTotal(100);
    $pago = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    $this->get(route('pagos.index'))->assertOk()
        ->assertSee('href="'.route('pagos.show', $pago).'"', false)
        ->assertSee('Pagar')
        ->assertDontSee('disabled aria-disabled="true"', false)
        ->assertDontSee('Anular');
});

it('el filtro permite encontrar pendientes fuera de la primera pagina', function () {
    $consulta = consultaConTotal(100);
    $uno = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    $dos = Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    foreach (range(1, 5) as $numero) {
        Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => Pago::PAGADO]);
    }
    $this->get(route('pagos.index', ['perPage' => 5, 'estado' => Pago::PENDIENTE]))->assertOk()
        ->assertSee(route('pagos.show', $uno), false)->assertSee(route('pagos.show', $dos), false)
        ->assertViewHas('pagos', fn ($pagos) => $pagos->count() === 2);
});

it('conserva el estado al paginar y todos devuelve todos los resultados filtrados', function () {
    $consulta = consultaConTotal(100);
    foreach (range(1, 6) as $numero) {
        Pago::create(['consulta_id' => $consulta->id, 'monto' => 100]);
    }
    Pago::create(['consulta_id' => $consulta->id, 'monto' => 100, 'estado' => Pago::PAGADO]);
    $this->get(route('pagos.index', ['estado' => Pago::PENDIENTE, 'perPage' => 5]))->assertOk()
        ->assertViewHas('pagos', fn ($pagos) => $pagos->total() === 6 && $pagos->count() === 5
            && str_contains($pagos->nextPageUrl(), 'estado=Pendiente'));
    $this->get(route('pagos.index', ['estado' => '', 'perPage' => 'all']))->assertOk()
        ->assertViewHas('pagos', fn ($pagos) => $pagos->count() === 7);
});

it('rechaza confirmar un formulario cuyo pendiente fue anulado', function () {
    $consulta = consultaConTotal(100);
    $pago = $consulta->pagos()->create(['monto' => 100]);
    $this->get(route('pagos.show', $pago))->assertOk()->assertSee('name="pago_id" value="'.$pago->id.'"', false);
    $pago->update(['estado' => Pago::ANULADO]);
    $this->post(route('pagos.store', $consulta), ['monto' => 100, 'pago_id' => $pago->id])
        ->assertRedirect()->assertSessionHas('error');
    expect(Pago::count())->toBe(1)->and($pago->fresh()->estado)->toBe(Pago::ANULADO);
});

it('rechaza confirmar el pendiente de otra consulta', function () {
    $consulta = consultaConTotal(100);
    $otra = consultaConTotal(100);
    $pago = $otra->pagos()->create(['monto' => 100]);
    $this->post(route('pagos.store', $consulta), ['monto' => 100, 'pago_id' => $pago->id])
        ->assertSessionHas('error');
    expect($consulta->pagos()->count())->toBe(0)->and($pago->fresh()->estado)->toBe(Pago::PENDIENTE);
});

it('confirma el pendiente seleccionado sin crear otro movimiento', function () {
    $consulta = consultaConTotal(100);
    $pago = $consulta->pagos()->create(['monto' => 100]);
    $this->post(route('pagos.store', $consulta), ['monto' => 100, 'pago_id' => $pago->id])
        ->assertSessionHas('success');
    expect(Pago::count())->toBe(1)->and($pago->fresh()->estado)->toBe(Pago::PAGADO);
});

it('muestra pagar deshabilitado para pagos que no estan pendientes', function (string $estado) {
    $consulta = consultaConTotal(100);
    $consulta->pagos()->create(['monto' => 100, 'estado' => $estado]);
    $this->get(route('pagos.index'))->assertOk()
        ->assertSee('disabled aria-disabled="true"', false)
        ->assertSee('Pagar')
        ->assertDontSee('Ver cuenta')
        ->assertDontSee('Anular');
})->with([Pago::PAGADO, Pago::ANULADO]);

it('impide borrar la consulta con cualquier estado de pago', function (string $estado) {
    $consulta = consultaConTotal(100);
    $pago = $consulta->pagos()->create(['monto' => 100, 'estado' => $estado]);
    $this->actingAs(usuarioDeCaja('admin'));
    $this->delete(route('consulta.destroy', $consulta))->assertRedirect()->assertSessionHas('error');
    expect($consulta->fresh())->not->toBeNull()
        ->and($consulta->servicios()->count())->toBe(1)
        ->and($pago->fresh()->estado)->toBe($estado);
})->with([Pago::PENDIENTE, Pago::PAGADO, Pago::ANULADO]);

it('impide otro cobro aunque exista un pagado historico con monto cero', function () {
    $consulta = consultaConTotal(100);
    $consulta->pagos()->create(['monto' => 0, 'estado' => Pago::PAGADO]);
    $this->post(route('pagos.store', $consulta), ['monto' => 100])->assertSessionHas('error');
    expect(Pago::count())->toBe(1);
});

it('abre la consulta existente desde una reserva sin volver a crearla', function () {
    $consulta = consultaConTotal(100);
    $reservaId = DB::table('reserva')->insertGetId(['mascota_id' => 1]);
    $consulta->update(['reserva_id' => $reservaId]);
    $this->get(route('consulta.create', ['reserva_id' => $reservaId]))->assertRedirect(route('consulta.show', $consulta));
    expect(Consulta::count())->toBe(1);
});

it('al finalizar devuelve al resumen con el pago pendiente preparado', function () {
    $consulta = consultaConTotal(100);
    $this->post(route('consulta.completar', $consulta))->assertRedirect(route('consulta.show', $consulta))->assertSessionHas('success');
    expect($consulta->pagos()->first()->estado)->toBe(Pago::PENDIENTE);
});

it('emite comprobante del pago confirmado con servicios y monto recibido', function () {
    $consulta = consultaConTotal(100);
    $pago = $consulta->pagos()->create(['monto' => 100, 'estado' => Pago::PAGADO]);
    $this->withoutVite();
    $this->get(route('pagos.comprobante', $pago))->assertOk()->assertSee('Consulta general')->assertSeeText('Importe recibido: Bs 100.00')
        ->assertSee(asset('salem-logo.jpg'), false)
        ->assertSeeText('Salem Centro Veterinario')
        ->assertDontSee('Confirmar cobro completo');
});

it('no emite comprobantes de pendientes o anulados', function (string $estado) {
    $consulta = consultaConTotal(100);
    $pago = $consulta->pagos()->create(['monto' => 100, 'estado' => $estado]);
    $this->get(route('pagos.comprobante', $pago))->assertStatus(409);
})->with([Pago::PENDIENTE, Pago::ANULADO]);

it('protege el comprobante con el permiso de pagos', function () {
    $consulta = consultaConTotal(100);
    $pago = $consulta->pagos()->create(['monto' => 100, 'estado' => Pago::PAGADO]);
    $this->actingAs(usuarioDeCaja('sin-caja', [PermisoCatalogo::CONSULTAS]));
    $this->get(route('pagos.comprobante', $pago))->assertForbidden();
});
