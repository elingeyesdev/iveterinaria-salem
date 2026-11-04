<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\EspecieController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MascotumController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PermisoUsuarioController;
use App\Http\Controllers\RazaController;
use App\Http\Controllers\RecordatorioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoleUserController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\SubServicioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VeterinarioController;
use App\Support\PermisoCatalogo;
use App\Support\ReporteTipo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// El registro público de cuentas de personal está desactivado: los usuarios
// del sistema (admin/user) solo se crean desde el panel de
// Administración (ver grupo 'role:admin' más abajo).
Auth::routes(['register' => false]);

// =========================================
// ACCESO Y SEGURIDAD — solo administradores (no asignable)
// =========================================
// Incluye el panel donde el administrador decide a qué submódulos accede
// cada usuario.
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('role-users', RoleUserController::class);

    Route::get('permisos', [PermisoUsuarioController::class, 'index'])->name('permisos.index');
    Route::get('permisos/{user}/editar', [PermisoUsuarioController::class, 'edit'])->name('permisos.edit');
    Route::put('permisos/{user}', [PermisoUsuarioController::class, 'update'])->name('permisos.update');
});

// =========================================
// PARAMETRIZACIÓN — cada submódulo exige su permiso (middleware "can")
// =========================================
// Eliminar registros sigue siendo exclusivo del administrador
// (Gate 'eliminar-registros' en Controller::eliminarRegistro).
Route::resource('clientes', ClienteController::class)
    ->middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::CLIENTES)]);
Route::resource('veterinarios', VeterinarioController::class)
    ->middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::VETERINARIOS)]);
Route::resource('mascota', MascotumController::class)
    ->middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::MASCOTAS)]);
Route::resource('servicios', ServicioController::class)
    ->middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::SERVICIOS)]);
Route::resource('sub_servicios', SubServicioController::class)
    ->middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::SUBSERVICIOS)])
    ->parameters(['sub_servicios' => 'subServicio']);
Route::resource('especies', EspecieController::class)
    ->middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::ESPECIES)])
    ->parameters(['especies' => 'especie']);
Route::resource('razas', RazaController::class)
    ->middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::RAZAS)])
    ->parameters(['razas' => 'raza']);
// =========================================
// RESERVAS — gestión interna (personal autenticado)
// =========================================
Route::middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::RESERVAS)])->group(function () {
    Route::get('reserva/disponibilidad', [ReservaController::class, 'disponibilidad'])->name('disponibilidad');
    Route::get('reserva/eventos', [ReservaController::class, 'eventos'])->name('reserva.eventos');
    Route::patch('reserva/{reserva}/estado', [ReservaController::class, 'cambiarEstado'])->name('reserva.estado');
    Route::resource('reserva', ReservaController::class);
});

// =========================================
Route::middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::CONSULTAS)])->group(function () {
    Route::resource('consulta', ConsultaController::class)->parameters([
        'consulta' => 'consulta',
    ]);

    Route::post('consulta/continuar-directa', [ConsultaController::class, 'continuarAtencionDirecta'])->name('consulta.continuar-directa');

    Route::post('consulta/{consulta}/completar', [ConsultaController::class, 'marcarCompletada'])->name('consulta.completar');

    // Pagos: flujo financiero independiente de la edición clínica.
    // Registrar exige además el permiso "pagos" y anular solo el
    // administrador; ambas reglas se validan en PagoPolicy.
    Route::post('consulta/{consulta}/pagos', [PagoController::class, 'store'])->name('consulta.pagos.store');
    Route::patch('consulta/{consulta}/pagos/{pago}/anular', [PagoController::class, 'anular'])->name('consulta.pagos.anular');
});

Route::middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::PAGOS)])
    ->prefix('pagos')->name('pagos.')->group(function () {
        Route::get('/', [PagoController::class, 'index'])->name('index');
        Route::get('/crear', [PagoController::class, 'create'])->name('create');
        Route::get('/{pago}/comprobante', [PagoController::class, 'comprobante'])->name('comprobante');
        Route::get('/{pago}', [PagoController::class, 'show'])->name('show');
        Route::post('/consulta/{consulta}', [PagoController::class, 'store'])->name('store');
        Route::patch('/consulta/{consulta}/{pago}/anular', [PagoController::class, 'anular'])->name('anular');
    });

Route::resource('recordatorio', RecordatorioController::class)
    ->middleware(['auth', 'can:'.PermisoCatalogo::gate(PermisoCatalogo::RECORDATORIOS)]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

// =========================================
// REPORTES (solo lectura; el permiso por reporte se valida con Gates)
// =========================================
Route::middleware(['auth', 'can:'.PermisoCatalogo::GATE_REPORTES])->prefix('reportes')->name('reportes.')->group(function () {
    Route::get('/', [ReporteController::class, 'index'])->name('index');
    Route::get('/indicadores-kpi', [KpiController::class, 'index'])->name('kpi.index');

    Route::get('{tipo}', [ReporteController::class, 'show'])
        ->where('tipo', ReporteTipo::patronRuta())->name('show');
    Route::get('{tipo}/csv', [ReporteController::class, 'csv'])
        ->where('tipo', ReporteTipo::patronRuta())->middleware('throttle:20,1')->name('csv');
    Route::get('{tipo}/imprimir', [ReporteController::class, 'imprimir'])
        ->where('tipo', ReporteTipo::patronRuta())->name('imprimir');
});
