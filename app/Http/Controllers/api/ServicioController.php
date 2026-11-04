<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $servicios = Servicio::all();
        if ($servicios->isEmpty()) {
            return response()->json(['message' => 'No se encontraron servicios', 'code' => 404], 404);
        }

        return response()->json(['data' => $servicios, 'code' => 200], 200);
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:servicio,nombre',
            'descripcion' => 'nullable|string|max:255',
            'precio' => 'required|numeric',
            'sub_servicio_id' => 'nullable|exists:sub_servicio,id',
            'estado' => 'nullable|boolean',
        ]);

        $servicio = Servicio::create($data);

        return response()->json([
            'message' => 'Servicio creado exitosamente',
            'data' => $servicio,
            'code' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $servicio = Servicio::find($id);
        if (! $servicio) {
            return response()->json(['message' => 'Servicio no encontrado', 'code' => 404], 404);
        }

        return response()->json(['data' => $servicio, 'code' => 200], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $servicio = Servicio::find($id);

        if (! $servicio) {
            return response()->json(['message' => 'Servicio no encontrado', 'code' => 404], 404);
        }

        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:255|unique:servicio,nombre,'.$id,
            'descripcion' => 'nullable|string|max:255',
            'precio' => 'sometimes|required|numeric',
            'sub_servicio_id' => 'sometimes|nullable|exists:sub_servicio,id',
            'estado' => 'nullable|boolean',
        ]);

        $servicio->update($data);

        return response()->json([
            'message' => 'Servicio actualizado exitosamente',
            'data' => $servicio,
            'code' => 200,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $servicio = Servicio::find($id);

        if (! $servicio) {
            return response()->json(['message' => 'Servicio no encontrado', 'code' => 404], 404);
        }

        $servicio->delete();

        return response()->json([
            'message' => 'Servicio eliminado exitosamente',
            'code' => 200,
        ], 200);
    }
}
