<?php

use App\Http\Controllers\MascotumController;
use App\Http\Requests\MascotumRequest;
use App\Models\Mascotum;
use App\Support\ReservaEstado;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->withoutMiddleware([Authenticate::class, Authorize::class]);
});

it('impide abrir la edicion de una mascota con reserva pendiente', function () {
    $mascota = Mockery::mock(Mascotum::class)->makePartial();
    $mascota->id = 15;
    $mascota->shouldReceive('tieneReservaPendiente')->once()->andReturnTrue();
    $mascota->shouldNotReceive('load');
    Route::bind('mascotum', fn () => $mascota);

    $this->get(route('mascota.edit', 15))->assertRedirect(route('mascota.index'))
        ->assertSessionHas('error', 'No puedes editar esta mascota porque tiene una reserva pendiente. Primero cancela o atiende la reserva.');
});

it('bloquea el guardado directo antes de validar datos o cambiar archivos', function (string $metodo) {
    $mascota = Mockery::mock(Mascotum::class)->makePartial();
    $mascota->id = 15;
    $mascota->shouldReceive('tieneReservaPendiente')->once()->andReturnTrue();
    $mascota->shouldNotReceive('update');
    Route::bind('mascotum', fn () => $mascota);

    $this->call($metodo, route('mascota.update', 15), ['nombre' => 'Otro nombre'])
        ->assertRedirect(route('mascota.index'))->assertSessionHas('error');
})->with(['PUT', 'PATCH']);

it('vuelve a comprobar la reserva al guardar un formulario ya abierto', function () {
    $mascota = Mockery::mock(Mascotum::class)->makePartial();
    $mascota->shouldReceive('tieneReservaPendiente')->once()->andReturnTrue();
    $mascota->shouldNotReceive('update');
    $request = Mockery::mock(MascotumRequest::class);
    $request->shouldNotReceive('validated');
    $request->shouldNotReceive('hasFile');

    $response = app(MascotumController::class)->update($request, $mascota);
    expect($response->getTargetUrl())->toBe(route('mascota.index'));
});

it('permite actualizar una mascota sin reservas pendientes', function () {
    $mascota = Mockery::mock(Mascotum::class)->makePartial();
    $mascota->shouldReceive('tieneReservaPendiente')->once()->andReturnFalse();
    $mascota->shouldReceive('update')->once()->with(['nombre' => 'Luna'])->andReturnTrue();
    $request = Mockery::mock(MascotumRequest::class);
    $request->shouldReceive('validated')->once()->andReturn(['nombre' => 'Luna']);
    $request->shouldReceive('hasFile')->with('imagen')->once()->andReturnFalse();

    $response = app(MascotumController::class)->update($request, $mascota);
    expect($response->getTargetUrl())->toBe(route('mascota.index'));
});

it('la comprobacion busca exclusivamente reservas pendientes de la mascota', function (bool $existe) {
    $relacion = Mockery::mock(HasMany::class);
    $relacion->shouldReceive('where')->once()->with('estado', ReservaEstado::PENDIENTE)->andReturnSelf();
    $relacion->shouldReceive('exists')->once()->andReturn($existe);
    $mascota = Mockery::mock(Mascotum::class)->makePartial();
    $mascota->shouldReceive('reservas')->once()->andReturn($relacion);

    expect($mascota->tieneReservaPendiente())->toBe($existe);
})->with([true, false]);

it('no bloquea el registro de una mascota nueva', function () {
    $request = MascotumRequest::create('/mascota', 'POST');
    expect($request->authorize())->toBeTrue();
});

it('autoriza editar cuando la mascota ya no tiene reservas pendientes', function () {
    $mascota = Mockery::mock(Mascotum::class)->makePartial();
    $mascota->shouldReceive('tieneReservaPendiente')->once()->andReturnFalse();
    $ruta = new Illuminate\Routing\Route('PATCH', 'mascota/{mascotum}', fn () => null);
    $ruta->bind(Request::create('/mascota/15', 'PATCH'));
    $ruta->setParameter('mascotum', $mascota);
    $request = MascotumRequest::create('/mascota/15', 'PATCH');
    $request->setRouteResolver(fn () => $ruta);

    expect($request->authorize())->toBeTrue();
});
