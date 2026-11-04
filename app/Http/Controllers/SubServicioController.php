<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubServicioRequest;
use App\Models\SubServicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SubServicioController extends Controller
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

        $subServicios = SubServicio::withCount('servicios')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->wantsJson() || $request->ajax()) {
            return view('sub_servicio._table', compact('subServicios', 'search', 'perPage'));
        }

        return view('sub_servicio.index', compact('subServicios', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $subServicio = new SubServicio;

        return view('sub_servicio.create', compact('subServicio'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubServicioRequest $request): RedirectResponse
    {
        SubServicio::create($request->validated());

        return Redirect::route('sub_servicios.index')
            ->with('success', 'Subservicio creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubServicio $subServicio): View
    {
        $subServicio->loadCount('servicios');

        return view('sub_servicio.show', compact('subServicio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubServicio $subServicio): View
    {
        return view('sub_servicio.edit', compact('subServicio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubServicioRequest $request, SubServicio $subServicio): RedirectResponse
    {
        $subServicio->update($request->validated());

        return Redirect::route('sub_servicios.index')
            ->with('success', 'Subservicio actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubServicio $subServicio): RedirectResponse
    {
        return $this->eliminarRegistro($subServicio, 'sub_servicios.index', 'Subservicio eliminado correctamente.');
    }
}
