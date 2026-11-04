<?php

use App\Models\Reserva;
use App\Services\ReservaService;
use App\Support\ReservaEstado;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Mail::fake();

    foreach (['consulta', 'reserva', 'mascota', 'cliente'] as $tabla) {
        Schema::dropIfExists($tabla);
    }

    Schema::create('cliente', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
        $t->string('apellido')->nullable();
        $t->string('correo')->nullable();
        $t->integer('estado')->default(1);
        $t->timestamps();
    });
    Schema::create('mascota', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
        $t->unsignedBigInteger('cliente_id');
        $t->integer('estado')->default(1);
        $t->timestamps();
    });
    // Misma restricción que la migración real de reserva. Las horas se guardan
    // como H:i, igual que la aplicación (en MySQL, TIME las normaliza a H:i:s).
    Schema::create('reserva', function (Blueprint $t) {
        $t->id();
        $t->date('fecha');
        $t->time('hora_inicio');
        $t->time('hora_fin');
        $t->unique(['fecha', 'hora_inicio']);
        $t->unsignedBigInteger('mascota_id');
        $t->string('estado')->default('Pendiente');
        $t->timestamps();
    });
    Schema::create('consulta', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('reserva_id')->nullable();
    });

    DB::table('cliente')->insert(['id' => 1, 'nombre' => 'Ana', 'correo' => 'ana@example.com']);
    DB::table('mascota')->insert([
        ['id' => 1, 'nombre' => 'Firulais', 'cliente_id' => 1],
        ['id' => 2, 'nombre' => 'Michi', 'cliente_id' => 1],
    ]);
    $this->fecha = now()->addDays(3)->toDateString();
});

function reservaCancelada(string $fecha, string $hora = '09:00'): int
{
    return DB::table('reserva')->insertGetId([
        'fecha' => $fecha, 'hora_inicio' => $hora, 'hora_fin' => '09:20',
        'mascota_id' => 1, 'estado' => ReservaEstado::CANCELADA,
        'created_at' => now(), 'updated_at' => '2026-01-01 10:00:00',
    ]);
}

it('permite reservar un turno que tenia una reserva cancelada y conserva la cancelada', function () {
    $canceladaId = reservaCancelada($this->fecha);

    $resultado = app(ReservaService::class)->crearReserva([
        'fecha' => $this->fecha, 'hora_inicio' => '09:00', 'mascota_id' => 2, 'cliente_id' => 1,
    ]);

    expect($resultado['success'])->toBeTrue();

    $cancelada = Reserva::find($canceladaId);
    expect($cancelada->estado)->toBe(ReservaEstado::CANCELADA)
        ->and($cancelada->hora_inicio)->toBe('09:00')
        ->and($cancelada->getRawOriginal('hora_inicio'))->toBe('09:00:01')
        ->and((string) $cancelada->getRawOriginal('updated_at'))->toBe('2026-01-01 10:00:00');

    expect(Reserva::where('estado', ReservaEstado::PENDIENTE)->count())->toBe(1);
});

it('permite volver a reservar un turno cancelado varias veces', function () {
    $servicio = app(ReservaService::class);

    for ($i = 0; $i < 3; $i++) {
        $resultado = $servicio->crearReserva([
            'fecha' => $this->fecha, 'hora_inicio' => '09:00', 'mascota_id' => 2, 'cliente_id' => 1,
        ]);
        expect($resultado['success'])->toBeTrue();
        $resultado['reserva']->update(['estado' => ReservaEstado::CANCELADA]);
    }

    expect(Reserva::count())->toBe(3)
        ->and(Reserva::where('estado', ReservaEstado::CANCELADA)->count())->toBe(3);
});

it('sigue rechazando un turno ocupado por una reserva activa', function () {
    DB::table('reserva')->insert([
        'fecha' => $this->fecha, 'hora_inicio' => '09:00', 'hora_fin' => '09:20',
        'mascota_id' => 1, 'estado' => ReservaEstado::PENDIENTE,
    ]);

    $resultado = app(ReservaService::class)->crearReserva([
        'fecha' => $this->fecha, 'hora_inicio' => '09:00', 'mascota_id' => 2, 'cliente_id' => 1,
    ]);

    expect($resultado['success'])->toBeFalse()
        ->and(Reserva::count())->toBe(1);
});

it('muestra como libre el turno cancelado en la disponibilidad', function () {
    reservaCancelada($this->fecha);

    expect(app(ReservaService::class)->obtenerHorasOcupadas($this->fecha))->not->toContain('09:00');
});
