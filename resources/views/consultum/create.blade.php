@extends('adminlte::page')

@section('title', 'Crear Consulta')

@section('content_header')
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header bg-dark text-white">
                        <span class="card-title">{{ __('CREAR') }} Consulta Médica</span>
                    </div>
                    <div class="card-body bg-white">
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger mb-3">{{ $message }}</div>
                        @endif
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success mb-3">{{ $message }}</div>
                        @endif

                        @if ($mascotaDirecta)
                            <div class="alert alert-info">
                                <h5><i class="fas fa-paw mr-1"></i> Atención sin reserva previa</h5>
                                <p class="mb-1"><strong>Cliente:</strong> {{ $mascotaDirecta->cliente?->nombre }} {{ $mascotaDirecta->cliente?->apellido }}</p>
                                <p class="mb-1"><strong>Mascota:</strong> {{ $mascotaDirecta->nombre }}</p>
                                <p class="mb-0">Al continuar, el sistema buscará el siguiente horario disponible y creará una reserva interna para vincular la consulta con la mascota.</p>
                            </div>
                            <form method="POST" action="{{ route('consulta.continuar-directa') }}">
                                @csrf
                                <input type="hidden" name="cliente_id" value="{{ $mascotaDirecta->cliente_id }}">
                                <input type="hidden" name="mascota_id" value="{{ $mascotaDirecta->id }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-arrow-right mr-1"></i> Continuar a consulta
                                </button>
                                <a class="btn btn-secondary" href="{{ route('consulta.create') }}">Cancelar</a>
                            </form>

                        @elseif(request('reserva_id') || request('modo') === 'reserva' || !request()->has('modo'))
                            <a class="btn btn-outline-secondary mb-3" href="{{ route('consulta.index') }}">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a consulta
                            </a>
                            <form method="POST" action="{{ route('consulta.store') }}" role="form" enctype="multipart/form-data" novalidate>
                                @csrf
                                @include('consultum.form')
                            </form>

                        @else
                            <h5 class="mb-3">Seleccione cómo llega la mascota</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-primary">
                                        <div class="card-body">
                                            <h5><i class="fas fa-calendar-check text-primary mr-1"></i> Atención con reserva</h5>
                                            <p>Seleccione una reserva pendiente registrada en la agenda.</p>
                                            <a class="btn btn-primary" href="{{ route('consulta.create', ['modo' => 'reserva']) }}">Seleccionar reserva</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-success">
                                        <div class="card-body">
                                            <h5><i class="fas fa-walking text-success mr-1"></i> Atención sin reserva</h5>
                                            <p>Si el cliente o la mascota son nuevos, regístrelos primero. Si ya existen, selecciónelos abajo.</p>
                                            <a class="btn btn-success mb-3" href="{{ route('clientes.create', ['from' => 'consulta']) }}">
                                                <i class="fas fa-user-plus mr-1"></i> Registrar nuevo cliente
                                            </a>
                                            <form method="GET" action="{{ route('consulta.create') }}" id="clienteExistenteForm">
                                                <input type="hidden" name="from" value="directa">
                                                <div class="form-group">
                                                    <label for="cliente_directo">Cliente registrado</label>
                                                    <select id="cliente_directo" name="cliente_id" class="form-control" required>
                                                        <option value="">Seleccione un cliente</option>
                                                        @foreach(($clientes ?? collect()) as $cliente)
                                                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellido }} — {{ $cliente->telefono }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mascota_directa">Mascota</label>
                                                    <select id="mascota_directa" name="mascota_id" class="form-control" required disabled>
                                                        <option value="">Primero seleccione un cliente</option>
                                                    </select>
                                                </div>
                                                <a id="registrarMascotaDirecta" class="btn btn-sm btn-outline-primary mb-3 d-none" href="#">
                                                    <i class="fas fa-paw mr-1"></i> Registrar mascota para este cliente
                                                </a>
                                                <button type="submit" class="btn btn-outline-success">Continuar con cliente registrado</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const clientes = @json(($clientes ?? collect())->map(fn($c) => ['id' => $c->id, 'mascotas' => $c->mascotas->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre])->values()])->values());
                                    const clienteSelect = document.getElementById('cliente_directo');
                                    const mascotaSelect = document.getElementById('mascota_directa');
                                    const registrarMascota = document.getElementById('registrarMascotaDirecta');
                                    if (!clienteSelect || !mascotaSelect) return;
                                    clienteSelect.addEventListener('change', function () {
                                        mascotaSelect.innerHTML = '';
                                        const cliente = clientes.find(c => String(c.id) === String(this.value));
                                        mascotaSelect.disabled = !cliente || cliente.mascotas.length === 0;
                                        if (registrarMascota) {
                                            registrarMascota.classList.toggle('d-none', !cliente);
                                            registrarMascota.href = cliente ? @json(route('mascota.create')) + '?from=consulta&cliente_id=' + encodeURIComponent(cliente.id) : '#';
                                        }
                                        const first = document.createElement('option');
                                        first.value = '';
                                        first.textContent = cliente && cliente.mascotas.length ? 'Seleccione una mascota' : 'Este cliente no tiene mascotas registradas';
                                        mascotaSelect.appendChild(first);
                                        if (cliente) cliente.mascotas.forEach(m => {
                                            const option = document.createElement('option');
                                            option.value = m.id;
                                            option.textContent = m.nombre;
                                            mascotaSelect.appendChild(option);
                                        });
                                    });
                                });
                            </script>
                        @endif
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection