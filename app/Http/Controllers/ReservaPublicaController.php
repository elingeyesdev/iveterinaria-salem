<?php

namespace App\Http\Controllers;

use App\Models\cr;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservaPublicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            ['correo' => $validated['correo']],
            [
                'nombre' => 'required|string',
                'telefono' => ['required', 'string', 'regex:/\A(?:5[367]|6[0-57-9]|7[0-9])[0-9]{6}\z/'],
                'fecha' => 'required|date|after_or_equal:today',
                'hora_inicio' => 'required|date_format:H:i',
                'motivo' => 'required|string',
            ]);

        DB::transaction(function () use ($request) {
            // 1. Busca si el cliente ya existe por su correo, si no, lo crea automáticamente
            $cliente = Cliente::firstOrCreate(
                ['correo' => $request->correo],
                [
                    'nombre' => $request->nombre,
                    'telefono' => $request->telefono,
                    'estado' => 1,
                ]
            );

            // 2. Crea la reserva vinculada a este cliente
            Reserva::create([
                'fecha' => $validated['fecha'],
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => Carbon::parse($request->hora_inicio)->addMinutes(30)->format('H:i'),
                // 'motivo'      => $request->motivo,
                'cliente_id' => $cliente->id,
                'estado' => 'pendiente', // El admin deberá aprobarla o atenderla en caja
            ]);
        });

        return back()->with('success', 'Reserva solicitada. Nos comunicaremos para confirmar.');
    }

    /**
     * Display the specified resource.
     */
    public function show(cr $cr)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(cr $cr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, cr $cr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(cr $cr)
    {
        //
    }
}
