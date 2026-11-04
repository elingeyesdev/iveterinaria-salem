<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Veterinario;
use Illuminate\Http\Request;

class VeterinarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $veterinarios = Veterinario::all();
        if ($veterinarios->isEmpty()) {
            return response()->json(['message' => 'No se encontraron veterinarios', 'code' => 404], 404);
        }

        return response()->json(['data' => $veterinarios, 'code' => 200], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255|unique:veterinario,correo',
            'telefono' => ['nullable', 'string', 'regex:/\A(?:5[367]|6[0-57-9]|7[0-9])[0-9]{6}\z/'],
            'estado' => 'nullable|boolean',
        ]);

        $veterinario = Veterinario::create($data);

        return response()->json([
            'message' => 'Veterinario creado exitosamente',
            'data' => $veterinario,
            'code' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $veterinario = Veterinario::find($id);
        if (! $veterinario) {
            return response()->json(['message' => 'Veterinario no encontrado', 'code' => 404], 404);
        }

        return response()->json(['data' => $veterinario, 'code' => 200], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $veterinario = Veterinario::find($id);

        if (! $veterinario) {
            return response()->json(['message' => 'Veterinario no encontrado', 'code' => 404], 404);
        }

        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255|unique:veterinario,correo,'.$id,
            'telefono' => ['nullable', 'string', 'regex:/\A(?:5[367]|6[0-57-9]|7[0-9])[0-9]{6}\z/'],
            'estado' => 'nullable|boolean',
        ]);

        $veterinario->update($data);

        return response()->json([
            'message' => 'Veterinario actualizado exitosamente',
            'data' => $veterinario,
            'code' => 200,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $veterinario = Veterinario::find($id);
        if (! $veterinario) {
            return response()->json(['message' => 'Veterinario no encontrado', 'code' => 404], 404);
        }

        $veterinario->delete();

        return response()->json([
            'message' => 'Veterinario eliminado exitosamente',
            'code' => 200,
        ], 200);
    }
}
