@php
    /* ---------- Estado del formulario ---------- */
    $isCreate = ($isEdit ?? false) === false;

    $isEdit = ($isEdit ?? false) === true
        || (isset($consulta) && is_object($consulta) && $consulta->exists);

    /* ---------- Colecciones seguras ---------- */
    $reservasCollection = $reservas ?? collect();
    $veterinariosCollection = $veterinarios ?? collect();
    $serviciosCollection = $servicios ?? collect();

    /* ---------- Reserva precargada ---------- */
    $reservaPrecargada = $reservaSeleccionada ?? null;

    $reservaPrecargadaId = isset($reserva_id)
        ? (int) $reserva_id
        : ($reservaPrecargada ? $reservaPrecargada->id : null);

    if (!$reservaPrecargada && $reservaPrecargadaId) {
        $reservaPrecargada = $reservasCollection->firstWhere('id', $reservaPrecargadaId);
    }

    /* ---------- Fecha inicial ---------- */
    $fechaDesdeReserva = now()->format('Y-m-d');

    if ($reservaPrecargada) {
        $fechaDesdeReserva = is_string($reservaPrecargada->fecha)
            ? $reservaPrecargada->fecha
            : ($reservaPrecargada->fecha ? $reservaPrecargada->fecha->format('Y-m-d') : now()->format('Y-m-d'));
    }

    /* ---------- Datos para Alpine.js ---------- */
    $reservasForJs = $reservasCollection
        ->map(function ($r) {
            $mascota = $r->mascota;
            $cliente = $mascota?->cliente;

            return [
                'id' => $r->id,
                'fecha' => is_string($r->fecha) ? $r->fecha : ($r->fecha ? $r->fecha->format('Y-m-d') : ''),
                'hora' => $r->hora_inicio,
                'cliente' => [
                    'nombre' => $cliente ? trim($cliente->nombre . ' ' . ($cliente->apellido ?? '')) : 'Sin cliente',
                    'telefono' => $cliente->telefono ?? null,
                    'correo' => $cliente->correo ?? null,
                ],
                'mascota' => [
                    'id' => $mascota?->id,
                    'nombre' => $mascota?->nombre ?? 'Sin mascota',
                ],
            ];
        })
        ->values()
        ->toArray();

    $veterinariosForJs = $veterinariosCollection
        ->map(function ($v) {
            return [
                'id' => $v->id,
                'nombre' => trim($v->nombre . ' ' . ($v->apellido ?? '')),
            ];
        })
        ->values()
        ->toArray();

    $serviciosForJs = $serviciosCollection
        ->map(function ($s) {
            return [
                'id' => $s->id,
                'nombre' => $s->nombre,
                'descrip' => $s->descripcion ?? '',
                // Precio informativo: el backend vuelve a obtener el precio real.
                'costo_base' => round((float) ($s->precio ?? 0), 2),
            ];
        })
        ->values()
        ->toArray();

    /* ---------- Valores anteriores ---------- */
    $oldReservaId = old('reserva_id', ($isEdit && isset($consulta)) ? $consulta->reserva_id : $reservaPrecargadaId);
    $oldVetId = old('veterinario_id', ($isEdit && isset($consulta)) ? $consulta->veterinario_id : '');

    $oldFecha = old('fecha');
    if (!$oldFecha) {
        $oldFecha = $isEdit && isset($consulta)
            ? (optional($consulta->fecha)->format('Y-m-d')
                ?? optional($consulta->created_at)->format('Y-m-d')
                ?? now()->format('Y-m-d'))
            : $fechaDesdeReserva;
    }

    $oldPeso = old('peso', ($isEdit && isset($consulta)) ? $consulta->peso : '');
    $oldMotivo = old('motivo', ($isEdit && isset($consulta)) ? ($consulta->motivo ?? '') : '');
    $oldDiag = old('diagnostico', ($isEdit && isset($consulta)) ? ($consulta->diagnostico ?? '') : '');
    $oldTrata = old('tratamiento', ($isEdit && isset($consulta)) ? ($consulta->tratamiento ?? '') : '');

    /* ---------- Servicios iniciales ---------- */
    $oldServicios = old('servicios', []);
    $serviciosIniciales = [];

    // Edición: recuperar servicios guardados anteriormente
    if (empty($oldServicios) && $isEdit && isset($consulta) && $consulta->servicios) {
        $serviciosIniciales = $consulta->servicios
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'nombre' => $s->nombre,
                    'descrip' => $s->descripcion ?? '',
                    'costo_unitario' => round((float) ($s->pivot->precio ?? 0), 2),
                    'fecha_prox' => optional($s->pivot->fecha_prox)->format('Y-m-d'),
                ];
            })
            ->values()
            ->toArray();
    }
    // Recuperar servicios después de un error de validación
    elseif (!empty($oldServicios)) {
        foreach ($oldServicios as $s) {
            $servicioId = $s['servicio_id'] ?? null;
            $base = collect($serviciosForJs)->firstWhere('id', $servicioId);

            if (!$base) {
                continue;
            }

            $serviciosIniciales[] = [
                'id' => $base['id'],
                'nombre' => $base['nombre'],
                'descrip' => $base['descrip'],
                'costo_unitario' => round((float) $base['costo_base'], 2),
                'fecha_prox' => $s['fecha_prox'] ?? '',
            ];
        }
    }
@endphp

<style>
    [x-cloak] { display: none !important; }

    .atn {
        --at-primary: #0f6b5c;
        --at-primary-dark: #0b4f44;
        --at-primary-soft: #e3f4f0;
        --at-ink: #1f2937;
        --at-muted: #4b5563;
        --at-line: #e5e7eb;
        color: var(--at-ink);
    }

    /* ---------- Tarjetas ---------- */
    .atn .at-card {
        background: #fff;
        border: 1px solid var(--at-line);
        border-top: 4px solid var(--at-primary);
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(15, 107, 92, .08);
        overflow: hidden;
    }
    .atn .at-head {
        display: flex; align-items: center; gap: 14px; padding: 16px 24px;
        background: linear-gradient(90deg, var(--at-primary-soft), #fff 70%);
        border-bottom: 1px solid var(--at-line);
    }
    .atn .at-step {
        flex: 0 0 auto; width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: var(--at-primary); color: #fff; font-weight: 700; font-size: 17px;
        box-shadow: 0 4px 10px rgba(15, 107, 92, .35);
    }
    .atn .at-head h5 { margin: 0; font-size: 18px; font-weight: 700; }
    .atn .at-head p { margin: 2px 0 0; font-size: 13px; color: var(--at-muted); }
    .atn .at-body { padding: 24px; }

    /* ---------- Campos ---------- */
    .atn .at-label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 600; }
    .atn .at-req { color: #b91c1c; }
    .atn .form-control, .atn .form-select {
        min-height: 46px; border: 1.5px solid #9ca3af; border-radius: 10px; font-size: 15px;
    }
    .atn textarea.form-control { min-height: 96px; }
    .atn .form-control:focus, .atn .form-select:focus {
        border-color: var(--at-primary); box-shadow: 0 0 0 4px rgba(15, 107, 92, .18);
    }
    .atn .form-control:disabled { background: #f3f4f6; }
    .atn .form-control.is-invalid { border-color: #b91c1c; }
    .atn .input-group-text { border: 1.5px solid #9ca3af; border-radius: 0 10px 10px 0; background: var(--at-primary-soft); color: var(--at-primary-dark); font-weight: 700; }
    .atn .input-group > .form-control { border-radius: 10px 0 0 10px; }
    .atn .at-error { display: block; margin-top: 6px; font-size: 13px; font-weight: 600; color: #b91c1c; }
    .atn .at-help { margin-top: 6px; font-size: 13px; color: var(--at-muted); }
    .atn .at-count { font-size: 12px; color: var(--at-muted); font-variant-numeric: tabular-nums; }

    .atn .at-note {
        display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; border-radius: 12px;
        background: #eff6ff; color: #1e3a8a; font-size: 13px;
    }
    .atn .at-note i { margin-top: 2px; }
    .atn .at-note.warn { background: #fffbeb; color: #92400e; }
    .atn .at-note.error { background: #fef2f2; color: #991b1b; }
    /* ---------- Paciente (reserva elegida) ---------- */
    .atn .at-paciente {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px;
        margin-top: 16px; padding: 16px; border-radius: 12px; background: var(--at-primary-soft);
    }
    .atn .at-paciente small { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: var(--at-primary-dark); }
    .atn .at-paciente strong, .atn .at-paciente span.v { font-size: 15px; font-weight: 600; }

    /* ---------- Servicios ---------- */
    .atn .at-btn-add {
        min-height: 46px; padding: 0 20px; border: 2px solid var(--at-primary); border-radius: 10px;
        background: #fff; color: var(--at-primary-dark); font-weight: 700; cursor: pointer;
    }
    .atn .at-btn-add:hover:not(:disabled) { background: var(--at-primary-soft); }
    .atn .at-btn-add:disabled { border-color: #d1d5db; color: #6b7280; cursor: not-allowed; }
    .atn .at-vacio { text-align: center; padding: 28px 16px; border: 2px dashed #d1d5db; border-radius: 12px; color: var(--at-muted); }
    .atn .at-vacio i { font-size: 30px; color: #9ca3af; margin-bottom: 8px; }
    .atn table.at-tabla { margin: 0; }
    .atn table.at-tabla thead th {
        background: #f1f5f4; color: var(--at-muted); font-size: 12px; font-weight: 700;
        letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; border-bottom: 0;
    }
    .atn table.at-tabla td { vertical-align: middle; }
    .atn .at-precio { font-variant-numeric: tabular-nums; font-weight: 700; white-space: nowrap; }
    .atn .at-quitar {
        width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center;
        border: 1.5px solid #fca5a5; border-radius: 10px; background: #fff; color: #b91c1c; cursor: pointer;
    }
    .atn .at-quitar:hover { background: #fef2f2; }

    /* ---------- Resumen lateral ---------- */
    .atn .at-resumen {
        position: sticky; top: 16px; border-radius: 16px; overflow: hidden; color: #fff;
        background: linear-gradient(160deg, var(--at-primary) 0%, var(--at-primary-dark) 100%);
        box-shadow: 0 10px 28px rgba(11, 79, 68, .35);
    }
    .atn .at-resumen-body { padding: 24px; }
    .atn .at-resumen h5 { margin: 0 0 12px; font-size: 18px; font-weight: 700; }
    .atn .at-item { display: flex; gap: 12px; align-items: flex-start; padding: 12px 0; border-top: 1px solid rgba(255, 255, 255, .2); }
    .atn .at-item i {
        width: 36px; height: 36px; flex: 0 0 auto; display: flex; align-items: center; justify-content: center;
        border-radius: 10px; background: rgba(255, 255, 255, .16);
    }
    .atn .at-item small { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #c9ece5; }
    .atn .at-item strong { display: block; font-size: 15px; font-weight: 600; word-break: break-word; }
    .atn .at-item strong.vacio { color: #a7d9cf; font-weight: 500; }
    .atn .at-total { margin-top: 8px; padding: 16px; border-radius: 12px; background: rgba(255, 255, 255, .14); }
    .atn .at-total small { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #c9ece5; }
    .atn .at-total strong { font-size: 30px; font-weight: 700; font-variant-numeric: tabular-nums; }
    .atn .at-btn-guardar {
        width: 100%; min-height: 52px; margin-top: 16px; border: 0; border-radius: 12px;
        background: #fbbf24; color: #1f2937; font-size: 16px; font-weight: 700; cursor: pointer;
        transition: transform .08s ease, background .15s ease;
    }
    .atn .at-btn-guardar:hover:not(:disabled) { background: #f59e0b; transform: translateY(-1px); }
    .atn .at-btn-guardar:disabled { background: rgba(255, 255, 255, .25); color: #e5f6f2; cursor: not-allowed; }
    .atn .at-btn-volver {
        display: block; margin-top: 10px; padding: 12px; border: 1.5px solid rgba(255, 255, 255, .5);
        border-radius: 12px; color: #fff; text-align: center; font-weight: 600; text-decoration: none;
    }
    .atn .at-btn-volver:hover { background: rgba(255, 255, 255, .12); color: #fff; }
    .atn .at-btn-volver.disabled { opacity: .5; pointer-events: none; }
    .atn .at-aviso { margin-top: 12px; padding: 10px 12px; border-radius: 10px; background: #fef3c7; color: #92400e; font-size: 13px; font-weight: 600; }
    .atn .at-precio-nota { margin: 12px 0 0; font-size: 12px; color: #c9ece5; }
</style>

<div class="atn" x-data="atencionForm()" x-init="inicializar()">

    {{-- ================= ERRORES DE VALIDACIÓN (servidor) ================= --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius:14px;" role="alert" aria-live="assertive">
            <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                <div>
                    <strong>Por favor, corrige los siguientes errores:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- ================= CABECERA DE EDICIÓN ================= --}}
    @if($isEdit && isset($consulta))
        <div class="at-card mb-4">
            <div class="at-head" style="border-bottom:0">
                <span class="at-step" aria-hidden="true"><i class="fa fa-file-medical"></i></span>
                <div>
                    <h5>Expediente de consulta {!! $consulta->estado_badge !!}</h5>
                    <p>Modifica los datos clínicos de la atención.</p>
                </div>
            </div>
        </div>
    @endif

    @if(isset($consulta_id))
        <input type="hidden" name="redirect_to_edit_consulta" value="{{ $consulta_id }}">
    @endif

    <div class="row g-4">

        {{-- =============================================================== --}}
        {{-- COLUMNA PRINCIPAL --}}
        {{-- =============================================================== --}}
        <div class="col-lg-8">

            {{-- ---------- 1. ORIGEN ---------- --}}
            <div class="at-card mb-4">
                <div class="at-head">
                    <span class="at-step">1</span>
                    <div>
                        <h5>Origen de la atención</h5>
                        <p>¿A qué paciente vas a atender?</p>
                    </div>
                </div>
                <div class="at-body">

                    <label for="reserva" class="at-label">
                        <i class="fa fa-calendar-check me-1"></i>
                        Reserva a atender
                        <span class="at-req">*</span>
                    </label>

                    <select
                        name="reserva_id"
                        id="reserva"
                        class="form-control @error('reserva_id') is-invalid @enderror"
                        x-model="reservaId"
                        :class="{ 'is-invalid': campoInvalido('reserva') }"
                        required
                        aria-required="true"
                        :aria-invalid="campoInvalido('reserva') ? 'true' : 'false'"
                    >
                        <option value="">Selecciona una reserva pendiente</option>
                        @foreach($reservasCollection as $r)
                            <option value="{{ $r->id }}">
                                {{ $r->mascota?->cliente?->nombre }}
                                {{ $r->mascota?->cliente?->apellido }}
                                · {{ $r->mascota?->nombre ?? 'Sin mascota' }}
                                · {{ $r->fecha }}
                                {{ substr((string) $r->hora_inicio, 0, 5) }}
                            </option>
                        @endforeach
                    </select>

                    @error('reserva_id')
                        <div class="at-error" role="alert">{{ $message }}</div>
                    @enderror

                    <div class="at-error" x-show="campoInvalido('reserva')" x-cloak role="alert">
                        Selecciona la reserva que estás atendiendo.
                    </div>

                    {{-- Información de la reserva --}}
                    <template x-if="reservaSeleccionada">
                        <div class="at-paciente" role="status">
                            <div>
                                <small>Paciente</small>
                                <strong x-text="reservaSeleccionada.mascota.nombre"></strong>
                            </div>
                            <div>
                                <small>Responsable</small>
                                <strong x-text="reservaSeleccionada.cliente.nombre"></strong>
                            </div>
                            <div>
                                <small>Contacto</small>
                                <span class="v" x-text="reservaSeleccionada.cliente.telefono || 'No registrado'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ---------- 2. DATOS ---------- --}}
            <div class="at-card mb-4">
                <div class="at-head">
                    <span class="at-step">2</span>
                    <div>
                        <h5>Datos de la atención</h5>
                        <p>Quién atiende, cuándo y el peso del día.</p>
                    </div>
                </div>
                <div class="at-body">
                    <div class="row g-3">

                        {{-- VETERINARIO --}}
                        <div class="col-md-6">
                            <label for="veterinario_id" class="at-label">
                                <i class="fa fa-user-md me-1"></i> Veterinario <span class="at-req">*</span>
                            </label>
                            <select
                                name="veterinario_id"
                                id="veterinario_id"
                                class="form-control @error('veterinario_id') is-invalid @enderror"
                                x-model="veterinarioId"
                                :class="{ 'is-invalid': campoInvalido('veterinario') }"
                                required
                                aria-required="true"
                            >
                                <option value="">Seleccione un veterinario</option>
                                @foreach($veterinariosCollection as $veterinario)
                                    <option value="{{ $veterinario->id }}">
                                        {{ $veterinario->nombre }} {{ $veterinario->apellido }}
                                    </option>
                                @endforeach
                            </select>
                            @error('veterinario_id')
                                <div class="at-error" role="alert">{{ $message }}</div>
                            @enderror
                            <div class="at-error" x-show="campoInvalido('veterinario')" x-cloak role="alert">
                                Selecciona el veterinario responsable.
                            </div>
                        </div>

                        {{-- FECHA --}}
                        <div class="col-md-6">
                            <label for="fecha" class="at-label">
                                <i class="fa fa-calendar-alt me-1"></i> Fecha <span class="at-req">*</span>
                            </label>
                            <input
                                type="date"
                                name="fecha"
                                id="fecha"
                                class="form-control @error('fecha') is-invalid @enderror"
                                x-model="fecha"
                                :class="{ 'is-invalid': campoInvalido('fecha') }"
                                required
                                aria-required="true"
                            >
                            @error('fecha')
                                <div class="at-error" role="alert">{{ $message }}</div>
                            @enderror
                            <div class="at-error" x-show="campoInvalido('fecha')" x-cloak role="alert">
                                Selecciona una fecha válida.
                            </div>
                        </div>

                        {{-- PESO --}}
                        <div class="col-md-6">
                            <label for="pesoDia" class="at-label">
                                <i class="fa fa-weight me-1"></i> Peso del día
                            </label>
                            <div class="input-group">
                                <input
                                    type="number"
                                    name="peso"
                                    id="pesoDia"
                                    step="0.1"
                                    min="0"
                                    max="999.99"
                                    inputmode="decimal"
                                    class="form-control @error('peso') is-invalid @enderror"
                                    x-model="pesoDia"
                                    :class="{ 'is-invalid': campoInvalido('peso') }"
                                    placeholder="Ej. 12.5"
                                >
                                <span class="input-group-text">kg</span>
                            </div>
                            @error('peso')
                                <div class="at-error" role="alert">{{ $message }}</div>
                            @enderror
                            <div class="at-error" x-show="campoInvalido('peso')" x-cloak role="alert">
                                El peso debe ser un número mayor o igual a 0.
                            </div>
                            <div class="at-help">El peso registrado aquí actualizará el peso actual de la mascota.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ---------- 3. NOTAS CLÍNICAS ---------- --}}
            <div class="at-card mb-4">
                <div class="at-head">
                    <span class="at-step">3</span>
                    <div>
                        <h5>Notas clínicas</h5>
                        <p>Motivo, diagnóstico y tratamiento indicado.</p>
                    </div>
                </div>
                <div class="at-body">
                    <div class="row g-3">

                        {{-- MOTIVO --}}
                        <div class="col-md-6">
                            <label for="motivo" class="at-label">
                                <i class="fa fa-comment-medical me-1"></i> Motivo <span class="at-req">*</span>
                            </label>
                            <textarea
                                name="motivo"
                                id="motivo"
                                class="form-control @error('motivo') is-invalid @enderror"
                                x-model="motivo"
                                rows="3"
                                maxlength="160"
                                minlength="3"
                                placeholder="Ej. Control de rutina, vómitos, cojera..."
                                required
                                :class="{ 'is-invalid': campoInvalido('motivo') }"
                            ></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="at-help m-0">Describe el motivo de la atención.</span>
                                <span class="at-count" x-text="`${motivo.length}/160`"></span>
                            </div>
                            @error('motivo')
                                <div class="at-error" role="alert">{{ $message }}</div>
                            @enderror
                            <div class="at-error" x-show="campoInvalido('motivo')" x-cloak role="alert">
                                El motivo de la atención es obligatorio.
                            </div>
                        </div>

                        {{-- DIAGNÓSTICO --}}
                        <div class="col-md-6">
                            <label for="diagnostico" class="at-label">
                                <i class="fa fa-stethoscope me-1"></i> Diagnóstico <span class="at-req">*</span>
                            </label>
                            <textarea
                                name="diagnostico"
                                id="diagnostico"
                                class="form-control @error('diagnostico') is-invalid @enderror"
                                x-model="diagnostico"
                                rows="3"
                                maxlength="160"
                                minlength="3"
                                placeholder="Ej. Otitis leve en oído derecho."
                                required
                                :class="{ 'is-invalid': campoInvalido('diagnostico') }"
                            ></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="at-help m-0">Diagnóstico clínico.</span>
                                <span class="at-count" x-text="`${diagnostico.length}/160`"></span>
                            </div>
                            @error('diagnostico')
                                <div class="at-error" role="alert">{{ $message }}</div>
                            @enderror
                            <div class="at-error" x-show="campoInvalido('diagnostico')" x-cloak role="alert">
                                El diagnóstico es obligatorio.
                            </div>
                        </div>

                        {{-- TRATAMIENTO --}}
                        <div class="col-12">
                            <label for="tratamiento" class="at-label">
                                <i class="fa fa-pills me-1"></i> Tratamiento
                            </label>
                            <textarea
                                name="tratamiento"
                                id="tratamiento"
                                class="form-control @error('tratamiento') is-invalid @enderror"
                                x-model="tratamiento"
                                rows="3"
                                maxlength="160"
                                placeholder="Ej. Limpieza ótica, antibiótico por 7 días."
                            ></textarea>
                            <div class="d-flex justify-content-end mt-1">
                                <span class="at-count" x-text="`${tratamiento.length}/160`"></span>
                            </div>
                            @error('tratamiento')
                                <div class="at-error" role="alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ---------- 4. SERVICIOS ---------- --}}
            <div class="at-card">
                <div class="at-head">
                    <span class="at-step">4</span>
                    <div>
                        <h5>Servicios aplicados</h5>
                        <p>Agrega al menos un servicio a la atención.</p>
                    </div>
                </div>
                <div class="at-body">

                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-md-8">
                            <label for="servicio_id" class="at-label">Agregar servicio</label>
                            <select
                                id="servicio_id"
                                class="form-control @error('servicio_id') is-invalid @enderror"
                                x-model="servicioSeleccionado"
                                @change="limpiarError('servicio')"
                                @keydown.enter.prevent="agregarServicio()"
                            >
                                <option value="">Seleccione un servicio</option>
                                @foreach($serviciosCollection as $servicio)
                                    <option value="{{ $servicio->id }}">
                                        {{ $servicio->nombre }}
                                        @if(isset($servicio->precio))
                                            — Bs {{ number_format((float) $servicio->precio, 2) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('servicio_id')
                                <div class="at-error" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <button type="button" class="at-btn-add w-100" @click="agregarServicio()" :disabled="!servicioSeleccionado">
                                <i class="fa fa-plus me-1" aria-hidden="true"></i> Agregar servicio
                            </button>
                        </div>
                    </div>

                    {{-- Aviso temporal (por ejemplo, servicio repetido) --}}
                    <div x-show="avisoServicio" x-cloak class="at-note warn mb-3" role="alert">
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                        <span x-text="avisoServicio"></span>
                    </div>

                    {{-- Error de servicios --}}
                    <div x-show="campoInvalido('servicios')" x-cloak class="at-note error mb-3" role="alert">
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                        <span x-text="mensajeServicios"></span>
                    </div>

                    {{-- Sin servicios --}}
                    <div x-show="serviciosAgregados.length === 0" x-cloak class="at-vacio">
                        <i class="fa fa-concierge-bell" aria-hidden="true"></i>
                        <div class="fw-semibold">Aún no agregaste ningún servicio.</div>
                        <small>Selecciona un servicio para incorporarlo a la atención.</small>
                    </div>

                    {{-- Tabla de servicios --}}
                    <div x-show="serviciosAgregados.length > 0" x-cloak class="table-responsive">
                        <table class="table align-middle at-tabla">
                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th class="text-end" style="width: 130px;">Precio</th>
                                    <th style="width: 190px;">Próxima fecha</th>
                                    <th class="text-end" style="width: 130px;">Importe</th>
                                    <th style="width: 60px;" class="text-center"><span class="visually-hidden">Quitar</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in serviciosAgregados" :key="item.id + '-' + index">
                                    <tr>
                                        <td>
                                            <div class="fw-semibold" x-text="item.nombre"></div>
                                            <small class="text-muted" x-show="item.descrip" x-text="item.descrip"></small>
                                        </td>
                                        <td class="text-end at-precio">
                                            Bs <span x-text="formatearMoneda(item.costo_unitario)"></span>
                                        </td>
                                        <td>
                                            <input
                                                type="date"
                                                class="form-control form-control-sm"
                                                :name="`servicios[${index}][fecha_prox]`"
                                                x-model="item.fecha_prox"
                                                :min="fecha"
                                                @change="validarFechaServicio(index)"
                                                :class="{ 'is-invalid': servicioFechaInvalida(index) }"
                                                :aria-label="`Próxima fecha de ${item.nombre}`"
                                            >
                                            <div class="at-error" x-show="servicioFechaInvalida(index)" x-cloak>
                                                La fecha no puede ser anterior a la consulta.
                                            </div>
                                        </td>
                                        <td class="text-end at-precio">
                                            Bs <span x-text="formatearMoneda(item.costo_unitario)"></span>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" :name="`servicios[${index}][servicio_id]`" :value="item.id">
                                            <button
                                                type="button"
                                                class="at-quitar"
                                                @click="quitarServicio(index)"
                                                title="Quitar servicio"
                                                :aria-label="`Quitar ${item.nombre}`"
                                            >
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total servicios</th>
                                    <th class="text-end at-precio">Bs <span x-text="formatearMoneda(total)"></span></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- =============================================================== --}}
        {{-- RESUMEN LATERAL --}}
        {{-- =============================================================== --}}
        <div class="col-lg-4">
            <div class="at-resumen">
                <div class="at-resumen-body">
                    <h5><i class="fas fa-clipboard-check me-2"></i>Resumen de la atención</h5>

                    <div class="at-item">
                        <i class="fas fa-paw"></i>
                        <div>
                            <small>Paciente</small>
                            <strong :class="{ 'vacio': !reservaSeleccionada }"
                                    x-text="reservaSeleccionada ? reservaSeleccionada.mascota.nombre : 'Sin seleccionar'"></strong>
                        </div>
                    </div>

                    <div class="at-item">
                        <i class="fas fa-user"></i>
                        <div>
                            <small>Responsable</small>
                            <strong :class="{ 'vacio': !reservaSeleccionada }"
                                    x-text="reservaSeleccionada ? reservaSeleccionada.cliente.nombre : 'Sin seleccionar'"></strong>
                        </div>
                    </div>

                    <div class="at-item">
                        <i class="fas fa-user-md"></i>
                        <div>
                            <small>Veterinario</small>
                            <strong :class="{ 'vacio': !veterinarioSeleccionado }"
                                    x-text="veterinarioSeleccionado ? veterinarioSeleccionado.nombre : 'Sin seleccionar'"></strong>
                        </div>
                    </div>

                    <div class="at-item">
                        <i class="fas fa-calendar-day"></i>
                        <div>
                            <small>Fecha</small>
                            <strong :class="{ 'vacio': !fechaTexto }" x-text="fechaTexto || 'Sin seleccionar'"></strong>
                        </div>
                    </div>

                    <div class="at-item">
                        <i class="fas fa-concierge-bell"></i>
                        <div>
                            <small>Servicios</small>
                            <strong :class="{ 'vacio': serviciosAgregados.length === 0 }"
                                    x-text="serviciosAgregados.length === 0 ? 'Ninguno agregado' : (serviciosAgregados.length === 1 ? '1 servicio' : serviciosAgregados.length + ' servicios')"></strong>
                        </div>
                    </div>

                    <div class="at-total">
                        <small>Total de servicios</small>
                        <strong x-text="'Bs ' + formatearMoneda(total)"></strong>
                    </div>
                    <p class="at-precio-nota">
                        Precios del catálogo actual. El precio definitivo se valida nuevamente en el servidor al guardar.
                    </p>

                    <button type="submit" class="at-btn-guardar" :disabled="guardando">
                        <template x-if="!guardando">
                            <span>
                                <i class="fa fa-save me-1" aria-hidden="true"></i>
                                {{ $isEdit ? 'Guardar cambios' : 'Guardar consulta' }}
                            </span>
                        </template>
                        <template x-if="guardando">
                            <span>
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Guardando...
                            </span>
                        </template>
                    </button>

                    <a href="{{ route('consulta.index') }}"
                       class="at-btn-volver"
                       :class="{ 'disabled': guardando }"
                       :aria-disabled="guardando ? 'true' : 'false'">
                        <i class="fa fa-arrow-left me-1" aria-hidden="true"></i> Cancelar
                    </a>

                    {{-- Mensaje general de validación --}}
                    <div x-show="mostrarErrores && !formularioValido()" x-cloak class="at-aviso" role="alert">
                        <i class="fa fa-exclamation-triangle me-1" aria-hidden="true"></i>
                        Revisa los campos marcados antes de guardar la consulta.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('js')

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
function atencionForm() {

    return {

        /* ---------- Estado principal ---------- */
        reservaId: @json($oldReservaId),
        veterinarioId: @json($oldVetId),
        fecha: @json($oldFecha),
        pesoDia: @json($oldPeso),
        motivo: @json($oldMotivo),
        diagnostico: @json($oldDiag),
        tratamiento: @json($oldTrata),

        /* ---------- Servicios ---------- */
        servicioSeleccionado: '',
        serviciosAgregados: @json($serviciosIniciales),
        avisoServicio: '',
        avisoTimer: null,

        /* ---------- Estado UI ---------- */
        mostrarErrores: false,
        guardando: false,

        /* ---------- Datos disponibles ---------- */
        reservasDisponibles: @json($reservasForJs),
        veterinariosDisponibles: @json($veterinariosForJs),
        serviciosDisponibles: @json($serviciosForJs),

        /* ---------- Errores internos ---------- */
        errores: {
            reserva: false,
            veterinario: false,
            fecha: false,
            peso: false,
            motivo: false,
            diagnostico: false,
            servicios: false
        },

        /* ---------- Inicialización ---------- */
        inicializar() {

            // Normalizar servicios recuperados de old()
            this.serviciosAgregados =
                Array.isArray(this.serviciosAgregados) ? this.serviciosAgregados : [];

            // Asegurar que los precios sean números
            this.serviciosAgregados = this.serviciosAgregados.map(servicio => ({
                ...servicio,
                costo_unitario: Number(servicio.costo_unitario) || 0,
                fecha_prox: servicio.fecha_prox || ''
            }));

            this.$nextTick(() => {
                this.configurarLimpiezaErrores();
                this.escucharEnvio();
            });
        },

        /*
         * El evento "submit" se dispara en el <form> y sube hacia sus padres,
         * no baja a los hijos: por eso se escucha en el formulario que contiene
         * este bloque y no en el div de Alpine.
         */
        escucharEnvio() {
            const formulario = this.$root.closest('form');

            if (!formulario) {
                return;
            }

            formulario.addEventListener('submit', (event) => {
                event.preventDefault();
                this.submitForm(event);
            });
        },

        /* ---------- Getters ---------- */
        get reservaSeleccionada() {
            return this.reservasDisponibles.find(
                reserva => String(reserva.id) === String(this.reservaId)
            ) || null;
        },

        get veterinarioSeleccionado() {
            return this.veterinariosDisponibles.find(
                vet => String(vet.id) === String(this.veterinarioId)
            ) || null;
        },

        get fechaTexto() {
            if (!this.fecha) {
                return '';
            }

            const f = new Date(this.fecha + 'T00:00:00');

            if (isNaN(f.getTime())) {
                return '';
            }

            const texto = f.toLocaleDateString('es-BO', {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            });

            return texto.charAt(0).toUpperCase() + texto.slice(1);
        },

        get total() {
            return this.serviciosAgregados.reduce(
                (sum, servicio) => sum + (Number(servicio.costo_unitario) || 0),
                0
            );
        },

        get mensajeServicios() {
            return this.serviciosAgregados.length === 0
                ? 'Debes agregar al menos un servicio.'
                : 'Revisa las fechas de próxima atención: no pueden ser anteriores a la consulta.';
        },

        /* ---------- Formatear moneda ---------- */
        formatearMoneda(valor) {
            return (Number(valor) || 0).toFixed(2);
        },

        /* ---------- Agregar servicio ---------- */
        agregarServicio() {

            this.limpiarError('servicios');

            if (!this.servicioSeleccionado) {
                return;
            }

            // Evitar duplicados
            const yaExiste = this.serviciosAgregados.some(
                servicio => String(servicio.id) === String(this.servicioSeleccionado)
            );

            if (yaExiste) {
                this.mostrarErrorTemporal('El servicio seleccionado ya fue agregado.');
                this.servicioSeleccionado = '';
                return;
            }

            // Buscar servicio en catálogo
            const base = this.serviciosDisponibles.find(
                servicio => String(servicio.id) === String(this.servicioSeleccionado)
            );

            if (!base) {
                return;
            }

            this.serviciosAgregados.push({
                id: base.id,
                nombre: base.nombre,
                descrip: base.descrip || '',
                costo_unitario: Number(base.costo_base) || 0,
                fecha_prox: ''
            });

            this.avisoServicio = '';
            this.servicioSeleccionado = '';
        },

        /* ---------- Eliminar servicio ---------- */
        quitarServicio(index) {

            if (index < 0 || index >= this.serviciosAgregados.length) {
                return;
            }

            this.serviciosAgregados.splice(index, 1);

            if (this.serviciosAgregados.length > 0) {
                this.limpiarError('servicios');
            }
        },

        /* ---------- Validar fecha de servicio ---------- */
        validarFechaServicio(index) {

            const servicio = this.serviciosAgregados[index];

            if (!servicio) {
                return false;
            }

            // La fecha próxima es opcional
            if (!servicio.fecha_prox) {
                return true;
            }

            if (!this.fecha) {
                return false;
            }

            return servicio.fecha_prox >= this.fecha;
        },

        servicioFechaInvalida(index) {
            return !this.validarFechaServicio(index);
        },

        /* ---------- Validar peso ---------- */
        pesoValido() {

            if (this.pesoDia === '' || this.pesoDia === null || this.pesoDia === undefined) {
                return true;
            }

            const peso = Number(this.pesoDia);

            return Number.isFinite(peso) && peso >= 0 && peso <= 999.99;
        },

        /* ---------- Validar formulario ---------- */
        formularioValido() {

            const reservaOk = Boolean(this.reservaId);
            const veterinarioOk = Boolean(this.veterinarioId);
            const fechaOk = Boolean(this.fecha);
            const motivoOk = this.textoValido(this.motivo);
            const diagnosticoOk = this.textoValido(this.diagnostico);
            const pesoOk = this.pesoValido();
            const serviciosOk = this.serviciosAgregados.length > 0;
            const fechasServiciosOk = this.serviciosAgregados.every(
                (_, index) => this.validarFechaServicio(index)
            );

            return (
                reservaOk
                && veterinarioOk
                && fechaOk
                && motivoOk
                && diagnosticoOk
                && pesoOk
                && serviciosOk
                && fechasServiciosOk
            );
        },

        /* ---------- Validar y establecer errores ---------- */
        validarFormulario() {

            this.errores.reserva = !this.reservaId;
            this.errores.veterinario = !this.veterinarioId;
            this.errores.fecha = !this.fecha;
            this.errores.peso = !this.pesoValido();
            this.errores.motivo = !this.textoValido(this.motivo);
            this.errores.diagnostico = !this.textoValido(this.diagnostico);

            this.errores.servicios =
                this.serviciosAgregados.length === 0
                || !this.serviciosAgregados.every(
                    (_, index) => this.validarFechaServicio(index)
                );

            return this.formularioValido();
        },

        textoValido(valor) {
            return String(valor ?? '').trim().length >= 3;
        },

        /* ---------- Errores ---------- */
        campoInvalido(campo) {
            return this.mostrarErrores && Boolean(this.errores[campo]);
        },

        limpiarError(campo) {
            if (Object.prototype.hasOwnProperty.call(this.errores, campo)) {
                this.errores[campo] = false;
            }
        },

        configurarLimpiezaErrores() {

            const campos = {
                reserva: '#reserva',
                veterinario: '#veterinario_id',
                fecha: '#fecha',
                peso: '#pesoDia',
                motivo: '#motivo',
                diagnostico: '#diagnostico',
                tratamiento: '#tratamiento',
                servicio: '#servicio_id'
            };

            Object.entries(campos).forEach(([nombre, selector]) => {

                const elemento = document.querySelector(selector);

                if (!elemento) {
                    return;
                }

                elemento.addEventListener('input', () => this.limpiarError(nombre));
                elemento.addEventListener('change', () => this.limpiarError(nombre));
            });
        },

        /* ---------- Aviso temporal dentro de la tarjeta de servicios ---------- */
        mostrarErrorTemporal(mensaje) {

            this.avisoServicio = mensaje;

            clearTimeout(this.avisoTimer);

            this.avisoTimer = setTimeout(() => {
                this.avisoServicio = '';
            }, 4000);
        },

        /* ---------- Envío del formulario ---------- */
        submitForm(event) {

            // Evitar doble envío
            if (this.guardando) {
                return;
            }

            this.mostrarErrores = true;

            const valido = this.validarFormulario();

            if (!valido) {
                this.enfocarPrimerError();
                return;
            }

            // Validación HTML5 adicional
            const formulario = event?.target;

            if (
                formulario
                && typeof formulario.reportValidity === 'function'
                && !formulario.reportValidity()
            ) {
                return;
            }

            this.mostrarErrores = false;
            this.guardando = true;

            // Enviar el formulario normalmente
            if (formulario) {
                formulario.submit();
            }
        },

        /* ---------- Enfocar primer error ---------- */
        enfocarPrimerError() {

            const campos = [
                { selector: '#reserva', invalido: this.errores.reserva },
                { selector: '#veterinario_id', invalido: this.errores.veterinario },
                { selector: '#fecha', invalido: this.errores.fecha },
                { selector: '#pesoDia', invalido: this.errores.peso },
                { selector: '#motivo', invalido: this.errores.motivo },
                { selector: '#diagnostico', invalido: this.errores.diagnostico },
                { selector: '#servicio_id', invalido: this.errores.servicios }
            ];

            const primero = campos.find(campo => campo.invalido);

            if (primero) {

                const elemento = document.querySelector(primero.selector);

                if (elemento) {
                    elemento.focus();
                    elemento.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }
    };
}
</script>

@endpush