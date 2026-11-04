<?php

use App\Http\Requests\ClienteRequest;
use App\Http\Requests\VeterinarioRequest;
use Illuminate\Support\Facades\Validator;

it('valida celulares locales al crear y editar contactos', function (string $telefono, bool $valido) {
    foreach ([new ClienteRequest, new VeterinarioRequest] as $request) {
        $validator = Validator::make(
            ['telefono' => $telefono],
            ['telefono' => $request->rules()['telefono']]
        );
        expect($validator->passes())->toBe($valido);
    }
})->with([
    ['61234567', true],
    ['53123456', true],
    ['56123456', true],
    ['57123456', true],
    ['60123456', true],
    ['65123456', true],
    ['67123456', true],
    ['69123456', true],
    ['79123456', true],
    ['66123456', false],
    ['50123456', false],
    ['51123456', false],
    ['52123456', false],
    ['54123456', false],
    ['55123456', false],
    ['58123456', false],
    ['59123456', false],
    ['71234567', true],
    ['21234567', false],
    ['81234567', false],
    ['7123456', false],
    ['712345678', false],
    ['7abcdefg', false],
    ['7123 456', false],
    ['+59171234567', false],
    ['71234567x', false],
    ['', false],
]);
