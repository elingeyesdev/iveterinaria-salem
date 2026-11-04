<?php

use App\Models\Role;
use App\Models\User;
use App\Support\ReservaEstado;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    if (DB::connection()->getDriverName() !== 'sqlite' || DB::connection()->getDatabaseName() !== ':memory:') {
        throw new RuntimeException('Estas pruebas requieren SQLite en memoria.');
    }

    foreach (['consulta', 'reserva', 'mascota', 'cliente', 'especie', 'raza'] as $tabla) {
        Schema::dropIfExists($tabla);
    }

    Schema::create('cliente', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('apellido')->nullable();
    });
    Schema::create('especie', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
    });
    Schema::create('raza', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
    });
    Schema::create('mascota', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->unsignedBigInteger('cliente_id');
        $table->unsignedBigInteger('especie_id')->nullable();
        $table->unsignedBigInteger('raza_id')->nullable();
    });
    Schema::create('reserva', function (Blueprint $table) {
        $table->id();
        $table->date('fecha');
        $table->time('hora_inicio');
        $table->time('hora_fin');
        $table->unsignedBigInteger('mascota_id');
        $table->string('estado');
    });
    Schema::create('consulta', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('reserva_id');
    });

    DB::table('cliente')->insert(['id' => 1, 'nombre' => 'Ana', 'apellido' => 'Perez']);
    DB::table('mascota')->insert(['id' => 1, 'nombre' => 'Luna', 'cliente_id' => 1]);
    DB::table('reserva')->insert([
        ['id' => 1, 'fecha' => now()->subDay()->toDateString(), 'hora_inicio' => '09:00', 'hora_fin' => '09:20', 'mascota_id' => 1, 'estado' => ReservaEstado::PENDIENTE],
        ['id' => 2, 'fecha' => now()->subDay()->toDateString(), 'hora_inicio' => '09:20', 'hora_fin' => '09:40', 'mascota_id' => 1, 'estado' => ReservaEstado::CANCELADA],
        ['id' => 3, 'fecha' => now()->subDay()->toDateString(), 'hora_inicio' => '09:40', 'hora_fin' => '10:00', 'mascota_id' => 1, 'estado' => ReservaEstado::ATENDIDA],
    ]);

    $administrador = new User(['name' => 'Admin', 'estado' => 1]);
    $administrador->setRelation('roles', collect([new Role(['nombre' => 'admin'])]));
    $administrador->setRelation('permisos', collect());
    $this->actingAs($administrador);

});

it('busca reservas por estado desde el campo de búsqueda existente', function () {
    $this->withoutVite()
        ->get(route('reserva.index', ['search' => ReservaEstado::CANCELADA]))
        ->assertOk()
        ->assertViewHas('reservas', fn ($reservas) => $reservas->total() === 1
            && $reservas->first()->estado === ReservaEstado::CANCELADA);
});

it('mantiene un solo campo de búsqueda en el listado', function () {
    $this->withoutVite()
        ->get(route('reserva.index'))
        ->assertOk()
        ->assertSee('name="search"', false)
        ->assertDontSee('<select class="form-control" name="estado"', false);
});

it('mantiene pendientes vencidas sin cambiar su estado al listar reservas', function () {
    $this->withoutVite()
        ->get(route('reserva.index'))
        ->assertOk();

    expect(DB::table('reserva')->where('id', 1)->value('estado'))
        ->toBe(ReservaEstado::PENDIENTE);
});

it('muestra las cuatro acciones y deshabilita las que no aplican segun estado', function () {
    $this->withoutVite()
        ->get(route('reserva.index'))
        ->assertOk()
        ->assertSee(route('reserva.show', 1), false)
        ->assertSee(route('reserva.show', 2), false)
        ->assertSee(route('reserva.show', 3), false)
        ->assertSee(route('reserva.edit', 1), false)
        ->assertDontSee(route('reserva.edit', 2), false)
        ->assertDontSee(route('reserva.edit', 3), false)
        ->assertSee(route('consulta.create', ['reserva_id' => 1]), false)
        ->assertSee(route('reserva.estado', 1), false)
        ->assertDontSee(route('reserva.estado', 2), false)
        ->assertDontSee(route('reserva.estado', 3), false)
        ->assertSee('btn-group-sm', false)
        ->assertSee('fa-eye', false)
        ->assertSee('fa-edit', false)
        ->assertSee('fa-ban', false)
        ->assertSee('aria-label="Atender reserva 3 no disponible"', false)
        ->assertSee('aria-label="Editar reserva 3 no disponible"', false)
        ->assertSee('aria-label="Cancelar reserva 3 no disponible"', false)
        ->assertSee('aria-label="Atender reserva 2 no disponible"', false)
        ->assertSee('aria-label="Editar reserva 2 no disponible"', false)
        ->assertSee('aria-label="Cancelar reserva 2 no disponible"', false);
});

it('rechaza la edicion directa de reservas canceladas o atendidas', function (int $reservaId) {
    $this->get(route('reserva.edit', $reservaId))->assertForbidden();
})->with([2, 3]);
