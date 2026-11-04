<?php

namespace App\Http\Controllers;

use App\Http\Requests\RazaRequest;
use App\Models\Raza;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RazaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Response
    {
        $search = trim($request->input('search', ''));
        $perPage = (int) $request->input('perPage', 5);
        if ($perPage <= 0) {
            $perPage = 5;
        }

        $razas = Raza::withCount('mascotas')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->wantsJson() || $request->ajax()) {
            return view('raza._table', compact('razas', 'search', 'perPage'));
        }

        return view('raza.index', compact('razas', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $raza = new Raza;

        return view('raza.create', compact('raza'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RazaRequest $request): RedirectResponse
    {
        Raza::create($request->validated());

        return Redirect::route('razas.index')
            ->with('success', 'Raza creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Raza $raza): View
    {
        $raza->loadCount('mascotas');

        return view('raza.show', compact('raza'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Raza $raza): View
    {
        return view('raza.edit', compact('raza'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RazaRequest $request, Raza $raza): RedirectResponse
    {
        $raza->update($request->validated());

        return Redirect::route('razas.index')
            ->with('success', 'Raza actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Raza $raza): RedirectResponse
    {
        return $this->eliminarRegistro($raza, 'razas.index', 'Raza eliminada correctamente.');
    }
}
