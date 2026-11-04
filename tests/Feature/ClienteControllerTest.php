<?php

test('la API de clientes permanece deshabilitada', function () {
    $this->postJson('/api/clientes', [
        'nombre' => 'Juan',
        'apellido' => 'Perez',
        'correo' => 'juan@example.com',
        'telefono' => '71234567',
    ])->assertNotFound();
});
