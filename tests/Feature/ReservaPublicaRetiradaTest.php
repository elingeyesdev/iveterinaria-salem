<?php

use Illuminate\Support\Facades\Route;

it('removes the public reservation and email verification routes', function () {
    foreach ([
        'reserva.publica',
        'reserva.correo.solicitar',
        'reserva.correo.verificar',
        'reserva.mascotas.correo',
        'reserva.confirmacion',
    ] as $routeName) {
        expect(Route::has($routeName))->toBeFalse();
    }

    $this->get('/reservar')->assertNotFound();
});

it('requires staff authentication for reservation creation and availability', function () {
    $this->post('/reserva')->assertRedirect(route('login'));
    $this->get('/reserva/disponibilidad?fecha=2026-10-31')->assertRedirect(route('login'));
});
