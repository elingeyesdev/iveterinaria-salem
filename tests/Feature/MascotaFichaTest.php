<?php

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
        DB::table('reserva')->insert(['id' => $id, 'mascota_id' => $id, 'fecha' => '2026-10-23', 'hora_inicio' => '09:00', 'hora_fin' => '09:30', 'estado' => 'Atendida']);
        DB::table('consulta')->insert(['id' => $id, 'reserva_id' => $id, 'veterinario_id' => 1, 'fecha' => '2026-10-23', 'peso' => 10 + $id, 'motivo' => 'Motivo mascota '.$id, 'diagnostico' => '<script>alert(1)</script>']);
        DB::table('consulta_servicio')->insert(['consulta_id' => $id, 'servicio_id' => 1, 'precio' => 100]);
        DB::table('pago')->insert(['id' => $id, 'consulta_id' => $id, 'monto' => 100, 'estado' => 'Pendiente']);
    }
    $this->usuarioFicha = User::factory()->make(['estado' => 1]);
    $this->usuarioFicha->id = 1;
    $this->usuarioFicha->setRelation('roles', collect());
    $this->usuarioFicha->setRelation('permisos', collect(['mascotas', 'consultas', 'reservas', 'pagos'])->map(fn ($clave) => new Permiso(['clave' => $clave])));
    $this->actingAs($this->usuarioFicha);
});

it('muestra la ficha real con responsable especie y raza', function () {
    $this->get(route('mascota.show', 1))->assertOk()->assertSee('Ficha de Mascota 1')
        ->assertSee('Jesus Cabrera')->assertSee('Canino')->assertSee('Mestizo');
    $this->get(route('mascota.show', 999))->assertNotFound();
});

it('aisla los registros de cada mascota en todas las secciones', function (string $seccion) {
    $this->get(route('mascota.show', [1, 'seccion' => $seccion]))->assertOk()
        ->assertViewHas('registros', fn ($registros) => $registros->total() === 1 && $registros->first()->id === 1);
})->with(['historial', 'servicios', 'peso', 'reservas', 'pagos']);

it('escapa el contenido clinico y muestra solo el historial del paciente', function () {
    $this->get(route('mascota.show', [1, 'seccion' => 'historial']))->assertOk()
        ->assertSee('Motivo mascota 1')->assertDontSee('Motivo mascota 2')
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false);
});

it('bloquea las secciones sin permiso incluso por url directa', function (string $seccion) {
    $this->usuarioFicha->setRelation('permisos', collect([new Permiso(['clave' => 'mascotas'])]));
    $this->get(route('mascota.show', 1))->assertOk()->assertViewHas('secciones', ['datos' => 'Datos generales']);
    $this->get(route('mascota.show', [1, 'seccion' => $seccion]))->assertForbidden();
})->with(['historial', 'servicios', 'peso', 'reservas', 'pagos']);

it('exige acceso al modulo de mascotas', function () {
    $this->usuarioFicha->setRelation('permisos', collect());
    $this->get(route('mascota.show', 1))->assertForbidden();
});

it('pagina el historial conservando la seccion', function () {
    foreach (range(3, 13) as $id) {
        DB::table('consulta')->insert(['id' => $id, 'reserva_id' => 1, 'veterinario_id' => 1, 'fecha' => '2026-10-23']);
    }
    $this->get(route('mascota.show', [1, 'seccion' => 'historial']))->assertOk()
        ->assertViewHas('registros', fn ($registros) => $registros->count() === 10 && $registros->total() === 12 && str_contains($registros->nextPageUrl(), 'seccion=historial'));
    $this->get(route('mascota.show', [1, 'seccion' => 'historial', 'page' => 2]))->assertOk()
        ->assertViewHas('registros', fn ($registros) => $registros->count() === 2);
});

it('muestra un estado vacio sin crear registros', function () {
    DB::table('mascota')->insert(['id' => 3, 'nombre' => 'Sin historial']);
    $this->get(route('mascota.show', [3, 'seccion' => 'historial']))->assertOk()->assertViewHas('registros', fn ($registros) => $registros->isEmpty());
    expect(DB::table('consulta')->count())->toBe(2);
});
