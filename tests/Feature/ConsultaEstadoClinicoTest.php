<?php

use App\Models\Consulta;
use App\Models\Pago;
use App\Models\Role;
use App\Models\User;
use App\Services\ConsultaService;
use App\Services\PagoService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    if (DB::connection()->getDriverName() !== 'sqlite' || DB::connection()->getDatabaseName() !== ':memory:') {
        throw new RuntimeException('Estas pruebas requieren SQLite en memoria.');
    }

    foreach (['pago', 'consulta_servicio', 'servicio', 'consulta', 'reserva', 'veterinario'] as $tabla) {
        Schema::dropIfExists($tabla);
    }
    Schema::create('reserva', function (Blueprint $table) {
        $table->id();
    });
    Schema::create('veterinario', function (Blueprint $table) {
        $table->id();
        $table->integer('estado')->default(1);
    });
    DB::table('veterinario')->insert(['id' => 1]);
    $this->migracion = require database_path('migrations/2026_10_17_182506_create_consulta_table.php');
    $this->migracion->up();
    Schema::create('servicio', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->integer('estado')->default(1);
        $table->decimal('precio', 10, 2)->default(100);
    });
    Schema::create('consulta_servicio', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('consulta_id');
        $table->unsignedBigInteger('servicio_id');
        $table->date('fecha_actual')->nullable();
        $table->date('fecha_prox')->nullable();
        $table->double('peso')->nullable();
        $table->decimal('precio', 10, 2);
        $table->timestamps();
    });
    Schema::create('pago', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('consulta_id');
        $table->decimal('monto', 10, 2);
        $table->enum('estado', [Pago::PENDIENTE, Pago::PAGADO, Pago::ANULADO])->default(Pago::PENDIENTE);
        $table->timestamps();
    });
    DB::table('consulta')->insert(['id' => 1, 'tipo' => 'Reserva', 'fecha' => now()->toDateString(), 'veterinario_id' => 1]);
});

it('la migracion actual inicia las consultas en atencion sin fecha de finalizacion', function () {
    $consulta = Consulta::findOrFail(1);
    expect($consulta->estado_clinico)->toBe(Consulta::EN_ATENCION)
        ->and($consulta->finalizada_at)->toBeNull()
        ->and($consulta->estado_clave)->toBe('pendiente')
        ->and($consulta->estado_badge)->toContain('En atención')
        ->and(Consulta::count())->toBe(1);
});

it('crea las nuevas consultas en atencion', function () {
    $consulta = Consulta::create(['fecha' => now()->toDateString(), 'veterinario_id' => 1, 'tipo' => 'Reserva'])->fresh();
    expect($consulta->estado_clinico)->toBe(Consulta::EN_ATENCION)
        ->and($consulta->finalizada_at)->toBeNull()
        ->and($consulta->estado_clave)->toBe('pendiente');
});

it('finaliza la atencion sin exigir pagos y conserva la primera fecha', function () {
    $consulta = Consulta::create(['fecha' => now()->toDateString(), 'veterinario_id' => 1, 'tipo' => 'Emergencia']);
    $finalizada = app(ConsultaService::class)->finalizar($consulta);
    $fecha = $finalizada->finalizada_at->toDateTimeString();
    expect($finalizada->estado_clinico)->toBe(Consulta::FINALIZADA)
        ->and($finalizada->estado_clave)->toBe('completada')
        ->and($fecha)->toBe(now()->toDateTimeString());
    $this->travel(5)->minutes();
    expect(app(ConsultaService::class)->finalizar($consulta)->finalizada_at->toDateTimeString())->toBe($fecha);
});

it('conserva la consulta finalizada al anular un pago parcial historico', function () {
    $consulta = Consulta::create(['fecha' => now()->toDateString(), 'veterinario_id' => 1, 'tipo' => 'Reserva']);
    $servicio = DB::table('servicio')->insertGetId(['nombre' => 'AtenciÃ³n']);
    $consulta->servicios()->attach($servicio, ['precio' => 270]);
    $pagos = app(PagoService::class);
    $consulta->pagos()->create(['monto' => 150, 'estado' => Pago::PAGADO]);
    $historico = $consulta->pagos()->create(['monto' => 120, 'estado' => Pago::PAGADO]);
    expect($consulta->fresh()->estado_clinico)->toBe(Consulta::EN_ATENCION);
    $finalizada = app(ConsultaService::class)->finalizar($consulta);
    $fecha = $finalizada->finalizada_at->toDateTimeString();
    $pagos->anular($consulta, $historico);
    $actual = $consulta->fresh();
    expect($actual->saldo_pendiente)->toBe(120.0)
        ->and($actual->total_pagado)->toBe(150.0)
        ->and($actual->estado_clinico)->toBe(Consulta::FINALIZADA)
        ->and($actual->estado_clave)->toBe('completada')
        ->and($actual->finalizada_at->toDateTimeString())->toBe($fecha)
        ->and($historico->fresh()->estado)->toBe(Pago::ANULADO);
});

it('el pago completo no finaliza la atencion clinica', function () {
    $consulta = Consulta::findOrFail(1);
    $servicio = DB::table('servicio')->insertGetId(['nombre' => 'AtenciÃ³n']);
    $consulta->servicios()->attach($servicio, ['precio' => 270]);
    app(PagoService::class)->registrar($consulta, ['monto' => 270]);
    expect($consulta->fresh()->saldo_pendiente)->toBe(0.0)
        ->and($consulta->fresh()->estado_clave)->toBe('pendiente');
});

it('ignora intentos de finalizar mediante asignacion masiva', function () {
    $consulta = Consulta::create(['fecha' => now()->toDateString(), 'veterinario_id' => 1, 'tipo' => 'Reserva', 'estado_clinico' => Consulta::FINALIZADA, 'finalizada_at' => now()]);
    expect($consulta->fresh()->estado_clinico)->toBe(Consulta::EN_ATENCION)
        ->and($consulta->fresh()->finalizada_at)->toBeNull();
});

it('la migracion actual incluye ambos campos sin una migracion adicional', function () {
    expect(Schema::hasColumn('consulta', 'estado_clinico'))->toBeTrue()
        ->and(Schema::hasColumn('consulta', 'finalizada_at'))->toBeTrue()
        ->and(DB::table('consulta')->count())->toBe(1);
});

it('finaliza desde el boton para un usuario autorizado', function () {
    $usuario = new User(['estado' => 1, 'name' => 'Administrador']);
    $usuario->id = 1;
    $usuario->setRelation('roles', collect([new Role(['nombre' => 'admin'])]));
    $consulta = Consulta::create(['fecha' => now()->toDateString(), 'veterinario_id' => 1, 'tipo' => 'Reserva']);
    $this->actingAs($usuario)->post(route('consulta.completar', $consulta))
        ->assertRedirect()->assertSessionHas('success');
    expect($consulta->fresh()->estado_clinico)->toBe(Consulta::FINALIZADA)
        ->and($consulta->fresh()->finalizada_at)->not->toBeNull();
});

it('impide finalizar a un usuario sin permiso clinico', function () {
    $usuario = new User(['estado' => 1, 'name' => 'Caja']);
    $usuario->id = 2;
    $usuario->setRelation('roles', collect());
    $usuario->setRelation('permisos', collect());
    $consulta = Consulta::create(['fecha' => now()->toDateString(), 'veterinario_id' => 1, 'tipo' => 'Reserva']);
    $this->actingAs($usuario)->post(route('consulta.completar', $consulta))->assertForbidden();
    expect($consulta->fresh()->estado_clinico)->toBe(Consulta::EN_ATENCION);
});

it('bloquea editar y borrar una consulta finalizada incluso para admin', function (bool $pagada) {
    $usuario = new User(['estado' => 1, 'name' => 'Administrador']);
    $usuario->id = 1;
    $usuario->setRelation('roles', collect([new Role(['nombre' => 'admin'])]));
    $consulta = Consulta::findOrFail(1);
    $servicio = DB::table('servicio')->insertGetId(['nombre' => 'Vacuna']);
    $consulta->servicios()->attach($servicio, ['precio' => 300]);
    if ($pagada) {
        app(PagoService::class)->registrar($consulta, ['monto' => 300]);
    }
    app(ConsultaService::class)->finalizar($consulta);
    $this->actingAs($usuario)->get(route('consulta.edit', $consulta))->assertForbidden();
    $this->put(route('consulta.update', $consulta), ['servicios' => []])->assertForbidden();
    $this->delete(route('consulta.destroy', $consulta))->assertForbidden();
    expect($consulta->fresh()->servicios()->count())->toBe(1)
        ->and($consulta->fresh()->total_servicios)->toBe(300.0)
        ->and($consulta->fresh()->estado_clinico)->toBe(Consulta::FINALIZADA);
})->with([true, false]);

it('rechaza guardar un formulario abierto antes de finalizar y anular un pago', function () {
    $consulta = Consulta::findOrFail(1);
    $servicio = DB::table('servicio')->insertGetId(['nombre' => 'Vacuna']);
    $consulta->servicios()->attach($servicio, ['precio' => 300]);
    app(PagoService::class)->registrar($consulta, ['monto' => 300]);
    $pago = $consulta->pagos()->firstOrFail();
    app(ConsultaService::class)->finalizar($consulta);
    app(PagoService::class)->anular($consulta, $pago);
    expect(fn () => app(ConsultaService::class)->actualizar($consulta, ['servicios' => []]))
        ->toThrow(DomainException::class);
    expect($consulta->fresh()->servicios()->count())->toBe(1)
        ->and($consulta->fresh()->saldo_pendiente)->toBe(300.0)
        ->and($consulta->fresh()->estado_clinico)->toBe(Consulta::FINALIZADA);
});

it('mantiene la edicion autorizada mientras la consulta esta en atencion', function () {
    $usuario = new User(['estado' => 1, 'name' => 'Administrador']);
    $usuario->setRelation('roles', collect([new Role(['nombre' => 'admin'])]));
    expect($usuario->can('update', Consulta::findOrFail(1)))->toBeTrue();
});

it('revierte la edicion si el nuevo total queda por debajo del abono', function () {
    $consulta = Consulta::findOrFail(1);
    $uno = DB::table('servicio')->insertGetId(['nombre' => 'Uno']);
    $dos = DB::table('servicio')->insertGetId(['nombre' => 'Dos']);
    $consulta->servicios()->attach([$uno => ['precio' => 100], $dos => ['precio' => 200]]);
    $consulta->pagos()->create(['monto' => 200, 'estado' => Pago::PAGADO]);
    expect(fn () => app(ConsultaService::class)->actualizar($consulta, [
        'fecha' => now()->toDateString(), 'veterinario_id' => 1, 'motivo' => 'Cambio',
        'servicios' => [['servicio_id' => $uno]],
    ]))->toThrow(DomainException::class, 'No se pueden agregar o quitar servicios');
    expect($consulta->fresh()->total_servicios)->toBe(300.0)
        ->and($consulta->fresh()->motivo)->toBeNull()
        ->and($consulta->fresh()->total_pagado)->toBe(200.0);
});

it('impide reducir servicios aunque el nuevo total coincida con un pago historico', function () {
    $consulta = Consulta::findOrFail(1);
    $uno = DB::table('servicio')->insertGetId(['nombre' => 'Uno']);
    $dos = DB::table('servicio')->insertGetId(['nombre' => 'Dos']);
    $consulta->servicios()->attach([$uno => ['precio' => 100], $dos => ['precio' => 200]]);
    $consulta->pagos()->create(['monto' => 100, 'estado' => Pago::PAGADO]);
    expect(fn () => app(ConsultaService::class)->actualizar($consulta, [
        'fecha' => now()->toDateString(), 'veterinario_id' => 1, 'motivo' => 'Cambio',
        'servicios' => [['servicio_id' => $uno]],
    ]))->toThrow(DomainException::class);
    expect($consulta->fresh()->total_servicios)->toBe(300.0)
        ->and($consulta->fresh()->saldo_pendiente)->toBe(200.0);
});

it('conserva la reserva cuando la consulta tiene movimientos financieros', function (string $estado) {
    $consulta = Consulta::findOrFail(1);
    DB::table('reserva')->insert([['id' => 1], ['id' => 2]]);
    $consulta->update(['reserva_id' => 1]);
    $consulta->pagos()->create(['monto' => 100, 'estado' => $estado]);
    expect(fn () => app(ConsultaService::class)->actualizar($consulta, [
        'fecha' => now()->toDateString(), 'veterinario_id' => 1,
        'reserva_id' => 2, 'motivo' => 'Cambio', 'servicios' => [],
    ]))->toThrow(DomainException::class, 'No se puede cambiar la reserva');
    expect($consulta->fresh()->reserva_id)->toBe(1);
})->with([Pago::PENDIENTE, Pago::PAGADO, Pago::ANULADO]);

it('rechaza un segundo registro de consulta para la misma reserva', function () {
    DB::table('reserva')->insert(['id' => 1]);
    Consulta::findOrFail(1)->update(['reserva_id' => 1]);
    expect(fn () => app(ConsultaService::class)->crear([
        'reserva_id' => 1, 'fecha' => today()->toDateString(), 'veterinario_id' => 1,
        'motivo' => 'Segundo envio', 'peso' => 10, 'servicios' => [],
    ]))->toThrow(DomainException::class);
    expect(Consulta::count())->toBe(1);
});
