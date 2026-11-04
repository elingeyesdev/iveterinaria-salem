<?php

use Illuminate\Support\Facades\Route;

it('bloquea parámetros GET duplicados', function () {
    $response = $this->get('/?search=uno&search=dos');

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Solicitud inválida: se detectaron parámetros HTTP duplicados.',
        ]);
});

it('permite un parámetro GET único', function () {
    $response = $this->get('/?search=uno');

    expect($response->status())->not->toBe(400);
});

it('permite colecciones PHP legítimas', function () {
    $response = $this->get('/?servicios[]=1&servicios[]=2');

    expect($response->status())->not->toBe(400);
});

it('bloquea claves JSON duplicadas en el cuerpo real', function (string $json) {
    $this->call('POST', '/', [], [], [], ['CONTENT_TYPE' => 'application/json'], $json)
        ->assertStatus(400);
})->with([
    '{"nombre":"Uno","nombre":"Dos"}',
    '{"datos":{"nombre":"Uno","nombre":"Dos"}}',
    '{"nombre":"Uno","\\u006eombre":"Dos"}',
]);

it('permite arreglos JSON y claves iguales en objetos diferentes', function (string $json) {
    Route::post('/prueba-json', fn () => response()->json(['ok' => true]));
    $this->call('POST', '/prueba-json', [], [], [], ['CONTENT_TYPE' => 'application/json'], $json)
        ->assertOk();
})->with([
    '{"items":["nombre","nombre"],"nombre":"Ana"}',
    '{"items":[{"nombre":"Uno"},{"nombre":"Dos"}]}',
]);

it('detecta nombres GET equivalentes codificados', function () {
    $this->get('/?search=uno&%73earch=dos')->assertStatus(400);
});
