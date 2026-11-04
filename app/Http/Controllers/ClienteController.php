<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Response
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);

        $clientes = Cliente::when($search, function ($query, $search) {
            return $query->where('nombre', 'LIKE', "%{$search}%")
                ->orWhere('apellido', 'LIKE', "%{$search}%");
        })
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->view('cliente._table', compact('clientes'));
        }

        return view('cliente.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $cliente = new Cliente;

        return view('cliente.create', compact('cliente'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClienteRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        if ($request->input('from') === 'reserva' && (int) $datos['estado'] !== 1) {
            return back()->withInput()->withErrors([
                'estado' => 'Para registrar una reserva, el responsable debe estar activo.',
            ]);
        }

        $cliente = Cliente::create($datos);

        // Flujo interno: después de crear al responsable, registrar su mascota
        // antes de volver al formulario de reserva.
        if ($request->input('from') === 'reserva') {
            return Redirect::route('mascota.create', [
                'from' => 'reserva',
                'cliente_id' => $cliente->id,
            ])->with('success', 'Responsable registrado. Ahora registre la mascota.');
        }

        return Redirect::route('clientes.index')
            ->with('success', 'Cliente created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $cliente = Cliente::with('mascotas')->findOrFail($id);

        return view('cliente.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $cliente = Cliente::find($id);

        return view('cliente.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        return Redirect::route('clientes.index')
            ->with('success', 'Cliente updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        return $this->eliminarRegistro(Cliente::findOrFail($id), 'clientes.index', 'Cliente eliminado correctamente.');
    }
}
