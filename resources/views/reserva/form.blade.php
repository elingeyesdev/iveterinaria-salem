@php
    $clienteSeleccionado = old('cliente_id', $clienteSeleccionado ?? $reserva->mascota?->cliente_id);
    $mascotaSeleccionada = old('mascota_id', $mascotaSeleccionada ?? $reserva->mascota_id);
@endphp

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <strong><i class="fas fa-exclamation-triangle me-2"></i> Por favor, corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Paso 1 · Cliente y mascota</h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                        <label for="cliente_id">Cliente</label>
                        <select name="cliente_id" id="cliente_id" class="form-control @error('cliente_id') is-invalid @enderror" required>
                            <option value="">Seleccione un cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ $clienteSeleccionado == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} {{ $cliente->apellido }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @can('permiso.clientes')
                            @can('permiso.mascotas')
                                <a class="btn btn-sm btn-outline-primary mt-2" href="{{ route('clientes.create', ['from' => 'reserva']) }}">
                                    <i class="fas fa-user-plus me-1"></i> Administrar Cliente
                                </a>
                            @endcan
                        @endcan
                </div>

                <div class="form-group mb-3">
                        <label for="mascota_id">Mascota</label>
                        <select name="mascota_id" id="mascota_id" class="form-control @error('mascota_id') is-invalid @enderror" required>
                            <option value="">Seleccione primero un cliente</option>
                        </select>
                        @error('mascota_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @can('permiso.mascotas')
                            <a id="registrarMascotaLink" class="btn btn-sm btn-outline-primary mt-2 {{ $clienteSeleccionado ? '' : 'disabled' }}"
                               href="{{ $clienteSeleccionado ? route('mascota.create', ['from' => 'reserva', 'cliente_id' => $clienteSeleccionado]) : '#' }}"
                               aria-disabled="{{ $clienteSeleccionado ? 'false' : 'true' }}">
                                <i class="fas fa-paw me-1"></i> Administrar Mascota
                            </a>
                        @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Paso 2 · Fecha y horario</h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror"
                           value="{{ old('fecha', $reserva->fecha) }}" min="{{ now()->toDateString() }}" required>
                    @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div id="horarios">
                    <div class="alert alert-info border-0 mb-0">Seleccione una fecha para ver los horarios disponibles.</div>
                </div>

                <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio', $reserva->hora_inicio) }}">
                <input type="hidden" name="hora_fin" id="hora_fin" value="{{ old('hora_fin', $reserva->hora_fin) }}">
                @error('hora_inicio')<div class="text-danger mt-2">{{ $message }}</div>@enderror

                <div class="alert alert-success border-0 mt-3 mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    La duración de cada atención es de 20 minutos.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card-footer bg-white border-0 px-0 mt-3">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i> Guardar Reserva
    </button>
    <a href="{{ route('reserva.index') }}" class="btn btn-secondary">
        Regresar
    </a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const fecha = document.getElementById('fecha');
    const horarios = document.getElementById('horarios');
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    const clienteSelect = document.getElementById('cliente_id');
    const mascotaSelect = document.getElementById('mascota_id');

    const clientes = @json($clientes ?? []);
    const mascotaInicial = @json($mascotaSeleccionada);
    const registrarMascotaLink = document.getElementById('registrarMascotaLink');
    const urlCrearMascota = @json(route('mascota.create', ['from' => 'reserva']));

    //carga las mascotas que tiene el cliente selecionado
    function cargarMascotasInternas(clienteId) {
        mascotaSelect.innerHTML = '<option value="">Seleccione una mascota</option>';
        mascotaSelect.disabled = !clienteId;

        if (registrarMascotaLink) {
            if (clienteId) {
                registrarMascotaLink.href = urlCrearMascota + '&cliente_id=' + encodeURIComponent(clienteId);
                registrarMascotaLink.classList.remove('disabled');
                registrarMascotaLink.setAttribute('aria-disabled', 'false');
            } else {
                registrarMascotaLink.href = '#';
                registrarMascotaLink.classList.add('disabled');
                registrarMascotaLink.setAttribute('aria-disabled', 'true');
            }
        }

        if (!clienteId) return;

        const cliente = clientes.find(c => String(c.id) === String(clienteId));
        const mascotas = cliente?.mascotas || [];

        if (!mascotas.length) {
            mascotaSelect.innerHTML = '<option value="">Este responsable todavía no tiene mascotas activas</option>';
            return;
        }

        mascotas.forEach(mascota => {
            const option = new Option(mascota.nombre, mascota.id);
            if (String(mascota.id) === String(mascotaInicial)) option.selected = true;
            mascotaSelect.add(option);
        });
    }
    //carga las mascotas que tiene el cliente selecionado
    
    if (clienteSelect && clienteSelect.tagName === 'SELECT') {
        clienteSelect.addEventListener('change', function () {
            cargarMascotasInternas(this.value);
        });
        cargarMascotasInternas(clienteSelect.value);
    }

    if (registrarMascotaLink) {
        registrarMascotaLink.addEventListener('click', function (event) {
            if (!clienteSelect.value) {
                event.preventDefault();
                alert('Primero seleccione al responsable de la mascota.');
            }
        });
    }

    if (fecha) {
        fecha.addEventListener('change', cargarHorarios);
        if (fecha.value) cargarHorarios();
    }

    async function cargarHorarios() {
        if (!fecha.value) return;
        horarios.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><div class="mt-2 small text-muted">Buscando horarios...</div></div>';

        let url = '{{ route('disponibilidad') }}?fecha=' + encodeURIComponent(fecha.value);
        @if ($reserva->id)
            url += '&reserva_id={{ $reserva->id }}';
        @endif

        try {
            const response = await fetch(url);
            const data = await response.json();
            mostrarHorarios(data);
        } catch (error) {
            horarios.innerHTML = '<div class="alert alert-danger border-0">Error al cargar los horarios.</div>';
        }
    }

    function mostrarHorarios(data) {
        horarios.innerHTML = '';
        if (data.cerrado) {
            horarios.innerHTML = '<div class="alert alert-warning border-0">La clínica permanece cerrada ese día.</div>';
            return;
        }

        const horaActual = (horaInicio.value || '').substring(0, 5);
        data.turnos.forEach(turno => {
            const titulo = document.createElement('h6');
            titulo.className = 'mt-3 mb-2 text-primary fw-bold';
            titulo.textContent = turno.nombre;
            horarios.appendChild(titulo);

            const fila = document.createElement('div');
            fila.className = 'd-flex flex-wrap gap-2 mb-3';

            turno.slots.forEach(slot => {
                const hora = slot.hora.substring(0, 5);
                const boton = document.createElement('button');
                boton.type = 'button';
                boton.textContent = hora;
                boton.className = 'btn btn-sm rounded-pill px-3 py-2 fw-semibold';

                const esHoraActualSeleccionada = hora === horaActual;
                const estaOcupado = slot.ocupado && !esHoraActualSeleccionada;
                const estaPasada = slot.pasado && !esHoraActualSeleccionada;

                if (estaPasada) {
                    boton.classList.add('btn-light', 'text-muted');
                    boton.disabled = true;
                    boton.title = 'Hora pasada';
                } else if (estaOcupado) {
                    boton.classList.add('btn-secondary');
                    boton.disabled = true;
                    boton.title = 'Horario reservado';
                } else if (esHoraActualSeleccionada) {
                    boton.classList.add('btn-success');
                    horaInicio.value = hora;
                    horaFin.value = sumar20Minutos(hora);
                } else {
                    boton.classList.add('btn-outline-primary');
                    boton.addEventListener('click', () => seleccionarHora(hora));
                }
                fila.appendChild(boton);
            });
            horarios.appendChild(fila);
        });
    }

    function seleccionarHora(hora) {
        horaInicio.value = hora;
        horaFin.value = sumar20Minutos(hora);
        horarios.querySelectorAll('button:not(:disabled)').forEach(btn => {
            btn.className = btn.textContent.trim() === hora
                ? 'btn btn-sm rounded-pill px-3 py-2 fw-semibold btn-success'
                : 'btn btn-sm rounded-pill px-3 py-2 fw-semibold btn-outline-primary';
        });
    }

    function sumar20Minutos(hora) {
        const [h, m] = hora.split(':').map(Number);
        const total = h * 60 + m + 20;
        return String(Math.floor(total / 60)).padStart(2, '0') + ':' + String(total % 60).padStart(2, '0');
    }
    });
</script>
