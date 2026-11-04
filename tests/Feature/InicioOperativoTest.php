<?php

use App\Models\Consulta;
use App\Models\Permiso;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    if (DB::connection()->getDriverName() !== 'sqlite' || DB::connection()->getDatabaseName() !== ':memory:') {
        throw new RuntimeException('Estas pruebas requieren SQLite en memoria.');
    }
    $this->withoutVite();
    foreach (['pago', 'consulta_servicio', 'consulta', 'reserva', 'mascota', 'cliente', 'especie', 'raza', 'veterinario', 'servicio'] as $tabla) {
        Schema::dropIfExists($tabla);
    }
    foreach (['cliente', 'especie', 'raza', 'veterinario', 'servicio'] as $tabla) {
        Schema::create($tabla, function (Blueprint $t) {
            $t->id();
            $t->string('nombre');
            $t->string('apellido')->nullable();
        });
    }
    Schema::create('mascota', function (Blueprint $t) {
        $t->id();
        $t->string('nombre');
        $t->unsignedBigInteger('cliente_id')->nullable();
        $t->unsignedBigInteger('especie_id')->nullable();
        $t->unsignedBigInteger('raza_id')->nullable();
        $t->date('fecha_nac')->nullable();
        $t->decimal('peso')->nullable();
        $t->string('sexo')->nullable();
        $t->string('imagen')->nullable();
        $t->integer('estado')->default(1);
    });
    Schema::create('reserva', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('mascota_id');
        $t->date('fecha');
        $t->string('hora_inicio');
        $t->string('hora_fin');
        $t->string('estado');
    });
    Schema::create('consulta', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('reserva_id');
        $t->unsignedBigInteger('veterinario_id');
        $t->date('fecha');
        $t->decimal('peso')->nullable();
        $t->string('motivo')->nullable();
        $t->string('diagnostico')->nullable();
        $t->string('tratamiento')->nullable();
        $t->string('estado_clinico')->default('Finalizada');
    });
    Schema::create('consulta_servicio', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('consulta_id');
        $t->unsignedBigInteger('servicio_id');
        $t->date('fecha_actual')->nullable();
        $t->date('fecha_prox')->nullable();
        $t->decimal('peso')->nullable();
        $t->decimal('precio');
        $t->timestamps();
    });
    Schema::create('pago', function (Blueprint $t) {
        $t->id();
        $t->unsignedBigInteger('consulta_id');
        $t->decimal('monto');
        $t->string('estado');
        $t->timestamps();
    });
    DB::table('cliente')->insert(['id' => 1, 'nombre' => 'Jesus', 'apellido' => 'Cabrera']);
    DB::table('especie')->insert(['id' => 1, 'nombre' => 'Canino']);
    DB::table('raza')->insert(['id' => 1, 'nombre' => 'Mestizo']);
    DB::table('veterinario')->insert(['id' => 1, 'nombre' => 'Ana', 'apellido' => 'Perez']);
    DB::table('servicio')->insert(['id' => 1, 'nombre' => 'Control general']);
    foreach ([1, 2] as $id) {
        DB::table('mascota')->insert(['id' => $id, 'nombre' => 'Mascota '.$id, 'cliente_id' => 1, 'especie_id' => 1, 'raza_id' => 1]);
        DB::table('reserva')->insert(['id' => $id, 'mascota_id' => $id, 'fecha' => today()->toDateString(), 'hora_inicio' => '09:00', 'hora_fin' => '09:30', 'estado' => 'Pendiente']);
        DB::table('consulta')->insert(['id' => $id, 'reserva_id' => $id, 'veterinario_id' => 1, 'fecha' => today()->toDateString(), 'estado_clinico' => Consulta::EN_ATENCION, 'peso' => 10 + $id, 'motivo' => 'Motivo mascota '.$id, 'diagnostico' => '<script>alert(1)</script>']);
        DB::table('consulta_servicio')->insert(['consulta_id' => $id, 'servicio_id' => 1, 'precio' => 100]);
        DB::table('pago')->insert(['id' => $id, 'consulta_id' => $id, 'monto' => 100, 'estado' => 'Pendiente']);
    }
    $this->usuarioInicio = User::factory()->make(['estado' => 1]);
    $this->usuarioInicio->id = 1;
    $this->usuarioInicio->setRelation('roles', collect());
    $this->usuarioInicio->setRelation('permisos', collect());
    $this->actingAs($this->usuarioInicio);
});

it('no consulta ni expone informacion operativa sin permisos', function () {
    DB::enableQueryLog();
    $this->get(route('home'))->assertOk()->assertViewHas('estadisticas', [])
        ->assertDontSee('Jesus Cabrera')->assertDontSee('Mascota 1');
    expect(DB::getQueryLog())->toBeEmpty();
});

it('muestra solo las colecciones autorizadas para cada puesto', function (string $permiso, string $coleccion) {
    $this->usuarioInicio->setRelation('permisos', collect([new Permiso(['clave' => $permiso])]));
    $response = $this->get(route('home'))->assertOk();
    foreach (['upcomingReservations', 'recentConsultas', 'pagosPendientes'] as $nombre) {
        $response->assertViewHas($nombre, fn ($datos) => $nombre === $coleccion ? $datos->count() === 2 : $datos->isEmpty());
    }
})->with([
    ['reservas', 'upcomingReservations'], ['consultas', 'recentConsultas'], ['pagos', 'pagosPendientes'],
]);

it('limita ingresos a usuarios con permiso del reporte', function () {
    DB::table('pago')->where('id', 1)->update(['estado' => 'Pagado', 'updated_at' => now()]);
    $this->usuarioInicio->setRelation('permisos', collect([new Permiso(['clave' => 'reportes.ingresos'])]));
    $this->get(route('home'))->assertOk()->assertViewHas('estadisticas', fn ($datos) => $datos['Ingresos de hoy (Bs)'] === '100.00')
        ->assertViewHas('pagosPendientes', fn ($datos) => $datos->isEmpty());
});

it('excluye consultas finalizadas y pagos no pendientes del trabajo diario', function () {
    $this->usuarioInicio->setRelation('permisos', collect(['consultas', 'pagos', 'reservas'])->map(fn ($clave) => new Permiso(['clave' => $clave])));
    DB::table('consulta')->where('id', 2)->update(['estado_clinico' => Consulta::FINALIZADA]);
    DB::table('pago')->where('id', 2)->update(['estado' => 'Anulado']);
    DB::table('reserva')->where('id', 2)->update(['estado' => 'Cancelado']);
    $this->get(route('home'))->assertOk()
        ->assertViewHas('recentConsultas', fn ($datos) => $datos->count() === 1)
        ->assertViewHas('pagosPendientes', fn ($datos) => $datos->count() === 1)
        ->assertViewHas('upcomingReservations', fn ($datos) => $datos->count() === 1)
        ->assertSee(route('consulta.show', 1), false);
});
