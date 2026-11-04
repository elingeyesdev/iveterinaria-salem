<?php

namespace App\Http\Controllers;

use App\Http\Requests\MascotumRequest;
use App\Models\Cliente;
use App\Models\Consulta;
use App\Models\Especie;
use App\Models\Mascotum;
use App\Models\Pago;
use App\Models\Raza;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MascotumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Response
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);

        $mascota = Mascotum::with('cliente')->when($search, function ($query, $search) {
            return $query->where('nombre', 'LIKE', "%{$search}%")
                ->orWhereHas('especie', fn ($q) => $q->where('nombre', 'LIKE', "%{$search}%"))
                ->orWhereHas('raza', fn ($q) => $q->where('nombre', 'LIKE', "%{$search}%"));
        })
            ->paginate($perPage);

        $clientes = Cliente::all();

        if ($request->ajax()) {
            return response()->view('mascotum._table', compact('mascota', 'clientes'));
        }

        return view('mascotum.index', compact('mascota', 'clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $mascotum = new Mascotum;
        $clientes = Cliente::where('estado', 1)->orderBy('nombre')->get();
        $clienteId = $request->integer('cliente_id') ?: null;
        $reservaId = $request->integer('reserva_id') ?: null;

        // No aceptar como preselección un cliente inexistente o inactivo.
        if ($clienteId && ! $clientes->contains('id', $clienteId)) {
            $clienteId = null;
        }

        $especies = Especie::where('estado', 1)->orderBy('nombre')->get();
        $razas = Raza::where('estado', 1)->orderBy('nombre')->get();

        return view('mascotum.create', compact(
            'mascotum', 'clientes', 'especies', 'razas', 'clienteId', 'reservaId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MascotumRequest $request)
    {
        $datos = $request->validated();

        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');

            // =====================================================================
            // A08:2021 — Fallos de Integridad de Software y Datos
            // Verificación de la integridad del archivo subido.
            //
            //   PASO 1 — Verificación MIME real mediante finfo:
            //   Laravel valida el MIME-type en el Request, pero un archivo
            //   poliglota (ej: shell.php.jpg con contenido <?php al final)
            //   puede pasar la comprobación de la cabecera. Volvemos a
            //   inspeccionar el contenido binario completo para asegurarnos
            //   de que el tipo real coincide con la extensión declarada.
            // =====================================================================
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeReal = $finfo->file($archivo->getRealPath());
            if (! in_array($mimeReal, ['image/jpeg', 'image/png'], true)) {
                throw ValidationException::withMessages([
                    'imagen' => ['A08: El contenido del archivo no corresponde a una imagen JPEG o PNG válida.'],
                ]);
            }

            // =====================================================================
            // A08:2021 — PASO 2 — Renombrado seguro:
            // NUNCA se usa el nombre original que envía el cliente (puede
            // contener caracteres maliciosos, path traversal o XSS). Se
            // genera un nombre aleatorio de 32 caracteres hexadecimales
            // impredecible y se le añade ÚNICAMENTE la extensión que el
            // propio Request ya validó (jpeg/png/jpg).
            // =====================================================================
            $nombreSeguro = bin2hex(random_bytes(16)).'.'.$archivo->getClientOriginalExtension();

            // =====================================================================
            // A08:2021 — PASO 3 — Cálculo de checksum SHA-256:
            // Se lee el contenido del archivo y se genera un digest criptográfico
            // que identifica inequívocamente sus bytes. Se almacena en la columna
            // `imagen_hash` para:
            //   - Detectar si la imagen fue alterada en disco (ej: inyección de
            //     código PHP posterior a la subida).
            //   - Verificar la integridad al restaurar backups de BD + storage.
            //   - Auditar reutilizaciones de imágenes en registros históricos.
            // =====================================================================
            $hashIntegridad = hash_file('sha256', $archivo->getRealPath());

            $path = $archivo->storeAs('mascotas', $nombreSeguro, 'public');

            // Diagnóstico A08: si Storage no pudo escribir el archivo, lo reportamos
            // en los logs para no quedarnos con un INSERT sin archivo en disco.
            if ($path === false) {
                Log::error('A08 - Fallo al guardar imagen en storage', [
                    'ruta_solicitada' => 'mascotas/'.$nombreSeguro,
                    'disk' => 'public',
                    'tamano_original' => $archivo->getSize(),
                    'cliente_id' => $datos['cliente_id'] ?? null,
                ]);
                throw ValidationException::withMessages([
                    'imagen' => ['A08: No se pudo guardar el archivo en el almacenamiento del servidor.'],
                ]);
            }

            $datos['imagen'] = $path;
            $datos['imagen_hash'] = $hashIntegridad;

            Log::info('A08 - Imagen de mascota procesada correctamente (store)', [
                'path' => $path,
                'sha256' => $hashIntegridad,
                'mime_real' => $mimeReal ?? null,
                'tamanio_bytes' => $archivo->getSize(),
            ]);
        } else {
            $datos['imagen'] = null;
            $datos['imagen_hash'] = null;
        }

        $mascotaCreada = Mascotum::create($datos);

        // Si el alta comenzó desde una reserva interna, volver al formulario
        // con el responsable y la mascota recién registrados preseleccionados.
        if ($request->input('from') === 'reserva') {
            return redirect()->route('reserva.create', [
                'cliente_id' => $mascotaCreada->cliente_id,
                'mascota_id' => $mascotaCreada->id,
            ])->with('success', 'Mascota registrada. Revise la fecha y el horario para confirmar la reserva.');
        }

        if ($request->filled('reserva_id_redirect')) {
            return redirect()->route('consulta.create', ['reserva_id' => $request->reserva_id_redirect])
                ->with('success', 'Mascota registrada y asociada al cliente correctamente.');
        }

        return redirect()->route('mascota.index')
            ->with('success', 'Mascota creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Mascotum $mascotum): View
    {
        $secciones = ['datos' => 'Datos generales'];
        if (Gate::allows('permiso.consultas')) {
            $secciones += ['historial' => 'Historial clínico', 'servicios' => 'Servicios', 'peso' => 'Evolución de peso'];
        }
        if (Gate::allows('permiso.reservas')) {
            $secciones['reservas'] = 'Reservas';
        }
        if (Gate::allows('permiso.pagos')) {
            $secciones['pagos'] = 'Pagos';
        }

        $seccion = $request->query('seccion', 'datos');
        abort_unless(is_string($seccion) && array_key_exists($seccion, $secciones), 403);
        $mascotum->load(['cliente', 'especie', 'raza']);
        $registros = null;

        if (in_array($seccion, ['historial', 'servicios', 'peso'], true)) {
            $query = Consulta::query()->whereHas('reserva', fn ($query) => $query->where('mascota_id', $mascotum->id));
            if ($seccion === 'historial') {
                $query->with('veterinario');
            } elseif ($seccion === 'servicios') {
                $query->with('servicios')->whereHas('servicios');
            } else {
                $query->whereNotNull('peso');
            }
            $registros = $query->orderByDesc('fecha')->orderByDesc('id')->paginate(10);
        } elseif ($seccion === 'reservas') {
            $registros = $mascotum->reservas()->orderByDesc('fecha')->orderByDesc('hora_inicio')->orderByDesc('id')->paginate(10);
        } elseif ($seccion === 'pagos') {
            $registros = Pago::query()
                ->whereHas('consulta.reserva', fn ($query) => $query->where('mascota_id', $mascotum->id))
                ->orderByDesc('id')->paginate(10);
        }
        $registros?->appends(['seccion' => $seccion]);

        return view('mascotum.show', compact('mascotum', 'secciones', 'seccion', 'registros'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mascotum $mascotum): View|RedirectResponse
    {
        if ($mascotum->tieneReservaPendiente()) {
            return redirect()->route('mascota.index')
                ->with('error', 'No puedes editar esta mascota porque tiene una reserva pendiente. Primero cancela o atiende la reserva.');
        }

        $mascotum->load('cliente');
        $clientes = Cliente::all();

        // Obtenemos solo las especies activas
        $especies = Especie::where('estado', 1)->get();

        $razas = Raza::where('estado', 1)->get();

        // Devolver la vista con los datos
        return view('mascotum.edit', compact('mascotum', 'clientes', 'especies', 'razas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MascotumRequest $request, Mascotum $mascotum): RedirectResponse
    {
        if ($mascotum->tieneReservaPendiente()) {
            return redirect()->route('mascota.index')
                ->with('error', 'No puedes editar esta mascota porque tiene una reserva pendiente. Primero cancela o atiende la reserva.');
        }

        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');

            // =====================================================================
            // A08:2021 — Fallos de Integridad de Software y Datos
            // Mismos 3 pasos de integridad que en store():
            //   1. Verificación MIME real (finfo) para rechazar polyglots.
            //   2. Renombrado seguro criptográficamente aleatorio.
            //   3. Cálculo y persistencia de checksum SHA-256.
            // =====================================================================
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeReal = $finfo->file($archivo->getRealPath());
            if (! in_array($mimeReal, ['image/jpeg', 'image/png'], true)) {
                throw ValidationException::withMessages([
                    'imagen' => ['A08: El contenido del archivo no corresponde a una imagen JPEG o PNG válida.'],
                ]);
            }

            $nombreSeguro = bin2hex(random_bytes(16)).'.'.$archivo->getClientOriginalExtension();
            $hashIntegridad = hash_file('sha256', $archivo->getRealPath());

            $path = $archivo->storeAs('mascotas', $nombreSeguro, 'public');
            if ($path === false) {
                Log::error('A08 - Fallo al guardar imagen actualizada en storage', [
                    'mascotum_id' => $mascotum->id,
                    'ruta_solicitada' => 'mascotas/'.$nombreSeguro,
                    'disk' => 'public',
                    'tamano_original' => $archivo->getSize(),
                ]);
                throw ValidationException::withMessages([
                    'imagen' => ['A08: No se pudo guardar la nueva imagen en el almacenamiento del servidor.'],
                ]);
            }

            $data['imagen'] = $path;
            $data['imagen_hash'] = $hashIntegridad;

            Log::info('A08 - Imagen de mascota actualizada correctamente', [
                'mascotum_id' => $mascotum->id,
                'path' => $path,
                'sha256' => $hashIntegridad,
                'mime_real' => $mimeReal ?? null,
                'tamanio_bytes' => $archivo->getSize(),
            ]);
        }

        $mascotum->update($data);

        return Redirect::route('mascota.index')
            ->with('success', 'Mascota actualizada correctamente.');
    }

    public function destroy(Mascotum $mascotum): RedirectResponse
    {
        return $this->eliminarRegistro($mascotum, 'mascota.index', 'Mascota eliminada correctamente.');
    }
}
