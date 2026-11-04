<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServicioRequest;
use App\Models\Servicio;
use App\Models\SubServicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ServicioController extends Controller
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

        $servicios = Servicio::with('subServicio')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->wantsJson() || $request->ajax()) {
            return view('servicios._table', compact('servicios', 'search', 'perPage'));
        }

        return view('servicio.index', compact('servicios', 'search', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $servicio = new Servicio;
        $subServicios = SubServicio::where('estado', 1)->get();

        return view('servicio.create', compact('servicio', 'subServicios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServicioRequest $request): RedirectResponse
    {
        Servicio::create($request->validated());

        return Redirect::route('servicios.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $servicio = Servicio::with('subServicio')->findOrFail($id);

        return view('servicio.show', compact('servicio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $servicio = Servicio::findOrFail($id);
        $subServicios = SubServicio::where('estado', 1)->get();

        return view('servicio.edit', compact('servicio', 'subServicios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServicioRequest $request, Servicio $servicio): RedirectResponse
    {
        $servicio->update($request->validated());

        return Redirect::route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        return $this->eliminarRegistro(Servicio::findOrFail($id), 'servicios.index', 'Servicio eliminado correctamente.');
    }
}
