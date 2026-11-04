<?php

namespace App\Http\Controllers;

use App\Models\Mascotum;
use App\Models\Recordatorio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecordatorioController extends Controller
{
    public function index(Request $request): View
    {
        $recordatorios = Recordatorio::with(['mascota.cliente', 'reserva'])
            ->when($request->filled('estado'), function ($query) use ($request) {
                $query->where('estado', $request->estado);
            })
            ->when($request->filled('tipo'), function ($query) use ($request) {
                $query->where('tipo', $request->tipo);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('titulo', 'LIKE', "%{$search}%")
                        ->orWhere('descripcion', 'LIKE', "%{$search}%")
                        ->orWhereHas('mascota', function ($mascota) use ($search) {
                            $mascota->where('nombre', 'LIKE', "%{$search}%")
                                ->orWhereHas('cliente', function ($cliente) use ($search) {
                                    $cliente->where('nombre', 'LIKE', "%{$search}%")
                                        ->orWhere('apellido', 'LIKE', "%{$search}%");
                                });
                        });
                });
            })
            ->orderByRaw("CASE WHEN estado = 'Pendiente' THEN 0 WHEN estado = 'Completado' THEN 1 ELSE 2 END")
            ->orderBy('fecha')
            ->paginate($request->input('perPage', 10))
            ->withQueryString();

        $proximos = Recordatorio::with('mascota')
            ->where('estado', 'Pendiente')
            ->whereBetween('fecha', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->orderBy('fecha')
            ->limit(5)
            ->get();

        return view('recordatorio.index', compact('recordatorios', 'proximos'));
    }

    public function create(): View
    {
        $recordatorio = new Recordatorio(['estado' => 'Pendiente']);

        $mascotas = Mascotum::with('cliente')
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view('recordatorio.create', compact('recordatorio', 'mascotas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mascota_id' => ['required', 'exists:mascota,id'],
            'tipo' => ['required', 'in:Vacunación,Desparasitación,Control veterinario,Baño/Peluquería,Limpieza dental,Otro'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', 'in:Pendiente,Completado,Cancelado'],
        ]);

        Recordatorio::create($validated);

        return redirect()
            ->route('recordatorio.index')
            ->with('success', 'Recordatorio creado correctamente.');
    }

    public function show(Recordatorio $recordatorio): View
    {
        $recordatorio->load('mascota.cliente');

        return view('recordatorio.show', compact('recordatorio'));
    }

    public function edit(Recordatorio $recordatorio): View
    {
        $mascotas = Mascotum::with('cliente')
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view('recordatorio.edit', compact('recordatorio', 'mascotas'));
    }

    public function update(Request $request, Recordatorio $recordatorio)
    {
        $validated = $request->validate([
            'mascota_id' => ['required', 'exists:mascota,id'],
            'tipo' => ['required', 'in:Vacunación,Desparasitación,Control veterinario,Baño/Peluquería,Limpieza dental,Otro'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', 'in:Pendiente,Completado,Cancelado'],
        ]);

        $recordatorio->update($validated);

        return redirect()
            ->route('recordatorio.index')
            ->with('success', 'Recordatorio actualizado correctamente.');
    }

    public function destroy(Recordatorio $recordatorio): RedirectResponse
    {
        return $this->eliminarRegistro($recordatorio, 'recordatorio.index', 'Recordatorio eliminado correctamente.');
    }
}
