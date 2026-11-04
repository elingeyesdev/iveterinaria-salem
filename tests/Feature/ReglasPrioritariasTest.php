<?php

use App\Models\Reserva;
use App\Models\Role;
use App\Models\User;
use App\Services\ConsultaValidacionService;
use App\Services\ReservaService;
use App\Support\ReservaEstado;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::dropIfExists('consulta');
    Schema::dropIfExists('reserva');
    Schema::create('reserva', function (Blueprint $table) {
        $table->id();
        $table->string('estado');
        $table->timestamps();
    });
    Schema::create('consulta', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('reserva_id');
        $table->string('estado_clinico');
    });
});

it('impide cambiar una reserva con consulta incluso en atencion', function (string $clinico, string $destino) {
    $reserva = Reserva::create(['estado' => ReservaEstado::ATENDIDA]);
    DB::table('consulta')->insert(['reserva_id' => $reserva->id, 'estado_clinico' => $clinico]);
    expect(fn () => app(ReservaService::class)->cambiarEstado($reserva, $destino))->toThrow(DomainException::class);
    expect($reserva->fresh()->estado)->toBe(ReservaEstado::ATENDIDA);
})->with(['En atenci?n', 'Finalizada'])->with(['Pendiente', 'Cancelado', 'No asistio']);

it('permite cerrar una reserva pendiente sin consulta', function (string $estado) {
    $reserva = Reserva::create(['estado' => ReservaEstado::PENDIENTE]);
    app(ReservaService::class)->cambiarEstado($reserva, $estado);
    expect($reserva->fresh()->estado)->toBe($estado);
})->with(['Cancelado', 'No asistio']);

it('no permite reabrir reservas cerradas ni marcar atendida sin consulta', function (string $origen, string $destino) {
    $reserva = Reserva::create(['estado' => $origen]);
    expect(fn () => app(ReservaService::class)->cambiarEstado($reserva, $destino))->toThrow(DomainException::class);
    expect($reserva->fresh()->estado)->toBe($origen);
})->with([['Cancelado', 'Pendiente'], ['No asistio', 'Pendiente'], ['Pendiente', 'Atendida']]);

it('expulsa a un usuario desactivado durante una sesion abierta', function () {
    Route::middleware(['web', 'auth'])->get('/prueba-cuenta-activa', fn () => response('Permitido'));
    $usuario = new User(['name' => 'Usuario', 'estado' => 1]);
    $usuario->id = 1;
    $this->actingAs($usuario)->get('/prueba-cuenta-activa')->assertOk();
    $usuario->estado = 0;
    $this->withSession(['dato_privado' => 'valor'])->get('/prueba-cuenta-activa')
        ->assertRedirect(route('login'))->assertSessionMissing('dato_privado');
    $this->assertGuest();
});

it('rechaza solicitudes json de cuentas inactivas', function () {
    Route::middleware(['web', 'auth'])->get('/prueba-cuenta-json', fn () => response('Permitido'));
    $usuario = new User(['name' => 'Usuario', 'estado' => 0]);
    $usuario->id = 1;
    $this->actingAs($usuario)->getJson('/prueba-cuenta-json')->assertForbidden();
    $this->assertGuest();
});

it('bloquea el cambio de estado desde la ruta incluso para administrador', function () {
    $usuario = new User(['name' => 'Admin', 'estado' => 1]);
    $usuario->id = 1;
    $usuario->setRelation('roles', collect([new Role(['nombre' => 'admin'])]));
    $reserva = Reserva::create(['estado' => ReservaEstado::ATENDIDA]);
    DB::table('consulta')->insert(['reserva_id' => $reserva->id, 'estado_clinico' => 'Finalizada']);
    $this->actingAs($usuario)->patch(route('reserva.estado', $reserva), ['estado' => ReservaEstado::CANCELADA])
        ->assertRedirect()->assertSessionHas('error');
    expect($reserva->fresh()->estado)->toBe(ReservaEstado::ATENDIDA);
});

it('no permite crear atencion sobre una reserva cerrada', function (string $estado) {
    $reserva = Reserva::create(['estado' => $estado]);
    expect(fn () => app(ConsultaValidacionService::class)->validarReserva($reserva->id))
        ->toThrow(InvalidArgumentException::class);
})->with(['Cancelado', 'No asistio']);
