<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    //
    public function index()
    {
        $clientes = Cliente::all();
        if ($clientes->isEmpty()) {
            return response()->json(['message' => 'No se encontraron clientes'], 404);
        }

        return response()->json(['data' => $clientes, 'code' => 200], 200);
    }

    public function show($id)
    {
        $cliente = Cliente::find($id);
        if (! $cliente) {
            return response()->json(['message' => 'Cliente no encontrado', 'code' => 404], 404);
        }

        return response()->json(['data' => $cliente, 'code' => 200], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255|unique:cliente,correo',
            'telefono' => ['nullable', 'string', 'regex:/\A(?:5[367]|6[0-57-9]|7[0-9])[0-9]{6}\z/'],
            'estado' => 'nullable|boolean',
        ]);

        $cliente = Cliente::create($data);

        return response()->json([
            'message' => 'Cliente creado exitosamente',
            'data' => $cliente,
            'code' => 201,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);
        if (! $cliente) {
            return response()->json(['message' => 'Cliente no encontrado', 'code' => 404], 404);
        }

        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255|unique:cliente,apellido,'.$id,
            'direccion' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255|unique:cliente,correo,'.$id,
            'telefono' => ['nullable', 'string', 'regex:/\A(?:5[367]|6[0-57-9]|7[0-9])[0-9]{6}\z/'],
            'estado' => 'nullable|boolean',
        ]);

        $cliente->update($data);

        return response()->json([
            'message' => 'Cliente actualizado exitosamente',
            'data' => $cliente,
            'code' => 200,
        ], 200);
    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        if (! $cliente) {
            return response()->json(['message' => 'Cliente no encontrado', 'code' => 404], 404);
        }

        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado exitosamente',
            'code' => 200,
        ], 200);
    }
}
