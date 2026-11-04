<?php

namespace App\Http\Controllers;

use App\Http\Requests\VeterinarioRequest;
use App\Models\Veterinario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class VeterinarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Response
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);

        $veterinarios = Veterinario::when($search, function ($query, $search) {
            return $query->where('nombre', 'LIKE', "%{$search}%")
                ->orWhere('apellido', 'LIKE', "%{$search}%");
        })
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->view('veterinario._table', compact('veterinarios'));
        }

        return view('veterinario.index', compact('veterinarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $veterinario = new Veterinario;

        return view('veterinario.create', compact('veterinario'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VeterinarioRequest $request): RedirectResponse
    {
        Veterinario::create($request->validated());

        return Redirect::route('veterinarios.index')
            ->with('success', 'Veterinario created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $veterinario = Veterinario::find($id);

        return view('veterinario.show', compact('veterinario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $veterinario = Veterinario::find($id);

        return view('veterinario.edit', compact('veterinario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VeterinarioRequest $request, Veterinario $veterinario): RedirectResponse
    {
        $veterinario->update($request->validated());

        return Redirect::route('veterinarios.index')
            ->with('success', 'Veterinario updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        return $this->eliminarRegistro(Veterinario::findOrFail($id), 'veterinarios.index', 'Veterinario eliminado correctamente.');
    }
}
