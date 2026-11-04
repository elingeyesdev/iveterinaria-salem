<?php

use App\Models\Permiso;
use App\Models\Role;
use App\Models\User;
use App\Support\PermisoCatalogo;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Esquema mínimo en SQLite en memoria, independiente de las migraciones
 * del proyecto (mismo enfoque que ReporteServiceTest).
 */
function crearEsquemaPermisos(): void
{
    foreach (['permiso_user', 'permisos', 'role_user', 'roles', 'users'] as $tabla) {
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
        $t->unique(['user_id', 'permiso_id']);
    });
}

function usuarioConRol(string $rol, string $nombre = 'Usuario'): User
{
    $usuario = User::forceCreate([
        'name' => $nombre,
        'email' => strtolower($nombre).'@example.com',
        'password' => bcrypt('secret-secret'),
    ]);
    $usuario->roles()->attach(Role::firstOrCreate(['nombre' => $rol], ['estado' => 1])->id);

    return $usuario->fresh();
}

function idPermiso(string $clave): int
{
    return Permiso::where('clave', $clave)->value('id');
}

beforeEach(function () {
    crearEsquemaPermisos();
    Permiso::sincronizarCatalogo();
});

it('sincroniza el catálogo sin duplicar registros', function () {
    Permiso::sincronizarCatalogo();

    expect(Permiso::count())->toBe(count(PermisoCatalogo::claves()));
});

it('deniega por defecto: un usuario nuevo no accede a ningún submódulo', function () {
    $usuario = usuarioConRol('user', 'Recepcion');

    foreach (PermisoCatalogo::claves() as $clave) {
        expect(Gate::forUser($usuario)->allows(PermisoCatalogo::gate($clave)))->toBeFalse();
    }

    $this->actingAs($usuario)->get('/clientes')->assertForbidden();
    $this->actingAs($usuario)->get('/servicios')->assertForbidden();
    $this->actingAs($usuario)->get('/reportes')->assertForbidden();
});

it('habilita solo los submódulos asignados', function () {
    $usuario = usuarioConRol('user', 'Recepcion');
    $usuario->permisos()->attach([idPermiso(PermisoCatalogo::CLIENTES), idPermiso(PermisoCatalogo::REPORTE_SERVICIOS)]);
    $usuario = $usuario->fresh();

    expect(Gate::forUser($usuario)->allows('permiso.clientes'))->toBeTrue()
        ->and(Gate::forUser($usuario)->allows('permiso.servicios'))->toBeFalse()
        ->and(Gate::forUser($usuario)->allows(PermisoCatalogo::GATE_REPORTES))->toBeTrue()
        ->and(Gate::forUser($usuario)->allows('permiso.reportes.ingresos'))->toBeFalse();

    $this->actingAs($usuario)->get('/servicios')->assertForbidden();
});

it('el administrador tiene acceso total sin permisos asignados', function () {
    $admin = usuarioConRol('admin', 'Admin');

    foreach (PermisoCatalogo::claves() as $clave) {
        expect(Gate::forUser($admin)->allows(PermisoCatalogo::gate($clave)))->toBeTrue();
    }
});

it('solo el administrador puede abrir el panel de permisos', function () {
    $usuario = usuarioConRol('user', 'Recepcion');
    $usuario->permisos()->attach(Permiso::pluck('id'));

    $this->actingAs($usuario->fresh())->get(route('permisos.index'))->assertForbidden();
    $this->actingAs(usuarioConRol('admin', 'Admin'))->get(route('permisos.index'))
        ->assertOk()
        ->assertSee('Recepcion');
});

it('el administrador asigna y revoca permisos de un usuario', function () {
    $admin = usuarioConRol('admin', 'Admin');
    $usuario = usuarioConRol('veterinario', 'Doctora');
    $usuario->permisos()->attach(idPermiso(PermisoCatalogo::SERVICIOS));

    $this->actingAs($admin)
        ->put(route('permisos.update', $usuario), [
            'permisos' => [idPermiso(PermisoCatalogo::CONSULTAS), idPermiso(PermisoCatalogo::MASCOTAS)],
        ])
        ->assertRedirect(route('permisos.index'))
        ->assertSessionHas('success', 'Permisos de Doctora actualizados: 2 submódulos habilitados.');

    expect($usuario->fresh()->permisos->pluck('clave')->sort()->values()->all())
        ->toBe([PermisoCatalogo::CONSULTAS, PermisoCatalogo::MASCOTAS]);
});

it('exige las dependencias del catálogo (cobrar requiere consultas)', function () {
    $admin = usuarioConRol('admin', 'Admin');
    $usuario = usuarioConRol('user', 'Caja');

    $this->actingAs($admin)
        ->from(route('permisos.edit', $usuario))
        ->put(route('permisos.update', $usuario), ['permisos' => [idPermiso(PermisoCatalogo::PAGOS)]])
        ->assertRedirect(route('permisos.edit', $usuario))
        ->assertSessionHasErrors(['permisos' => 'El permiso «Cobrar consultas» requiere también «Consultas y emergencias».']);

    expect($usuario->fresh()->permisos)->toBeEmpty();
});

it('rechaza permisos inexistentes', function () {
    $admin = usuarioConRol('admin', 'Admin');
    $usuario = usuarioConRol('user', 'Caja');

    $this->actingAs($admin)
        ->put(route('permisos.update', $usuario), ['permisos' => [9999]])
        ->assertSessionHasErrors('permisos.0');
});

it('no permite editar permisos de un administrador', function () {
    $admin = usuarioConRol('admin', 'Admin');
    $otroAdmin = usuarioConRol('admin', 'Gerente');

    $this->actingAs($admin)
        ->get(route('permisos.edit', $otroAdmin))
        ->assertRedirect(route('permisos.index'))
        ->assertSessionHas('info');
});

it('muestra el formulario de asignación agrupado por módulo', function () {
    $admin = usuarioConRol('admin', 'Admin');
    $usuario = usuarioConRol('user', 'Caja');

    $this->actingAs($admin)
        ->get(route('permisos.edit', $usuario))
        ->assertOk()
        ->assertSee(PermisoCatalogo::MODULO_PARAMETRIZACION)
        ->assertSee(PermisoCatalogo::MODULO_TRANSACCIONAL)
        ->assertSee(PermisoCatalogo::MODULO_REPORTES)
        ->assertSee('Cobrar consultas');
});

it('el seeder inicial crea el administrador y se puede ejecutar dos veces', function () {
    Schema::table('users', function (Blueprint $t) {
        $t->timestamp('email_verified_at')->nullable();
    });

    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    $admin = User::where('email', 'susej.co2016@gmail.com')->firstOrFail();

    expect(User::count())->toBe(2)
        ->and(Role::count())->toBe(3)
        ->and($admin->esAdministrador())->toBeTrue()
        ->and(Hash::check(DatabaseSeeder::CLAVE_INICIAL, $admin->password))->toBeTrue()
        ->and(Permiso::count())->toBe(count(PermisoCatalogo::claves()));
});
