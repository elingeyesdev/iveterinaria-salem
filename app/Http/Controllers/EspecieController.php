<?php

namespace App\Http\Controllers;

use App\Http\Requests\EspecieRequest;
use App\Models\Especie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EspecieController extends Controller
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

        $especies = Especie::withCount('mascotas')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->wantsJson() || $request->ajax()) {
            return view('especie._table', compact('especies', 'search', 'perPage'));
        }

        return view('especie.index', compact('especies', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $especie = new Especie;

        return view('especie.create', compact('especie'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EspecieRequest $request): RedirectResponse
    {
        Especie::create($request->validated());

        return Redirect::route('especies.index')
            ->with('success', 'Especie creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Especie $especie): View
    {
        $especie->loadCount('mascotas');

        return view('especie.show', compact('especie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Especie $especie): View
    {
        return view('especie.edit', compact('especie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EspecieRequest $request, Especie $especie): RedirectResponse
    {
        $especie->update($request->validated());

        return Redirect::route('especies.index')
            ->with('success', 'Especie actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Especie $especie): RedirectResponse
    {
        return $this->eliminarRegistro($especie, 'especies.index', 'Especie eliminada correctamente.');
    }
}
