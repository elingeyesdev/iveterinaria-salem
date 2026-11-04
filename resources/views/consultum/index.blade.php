@extends('adminlte::page')

@section('title', 'Consultas Médicas')

@section('content_header')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                <h4 class="mb-0">Consultas Médicas</h4>
                                <p class="text-muted mb-0">Historial de atenciones clínicas.</p>
                            </span>

                            <div class="float-right d-flex gap-2">
                                <a href="{{ route('consulta.create') }}" class="btn btn-primary btn-sm float-right">
                                    <i class="fa fa-plus me-1"></i> {{ __('Nueva Consulta') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success mx-4 mb-0 mt-3">
                            <p class="mb-0">{{ $message }}</p>
                        </div>
                    @endif
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger mx-4 mb-0 mt-3">
                            <p class="mb-0">{{ $message }}</p>
                        </div>
                    @endif

                   

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th style="width: 60px;">N°</th>
                                        <th style="width: 110px;">Fecha</th>
                                        <th style="width: 120px;">Tipo</th>
                                        <th>Paciente</th>
                                        <th>Dueño</th>
                                        <th>Veterinario</th>
                                        <th>Motivo</th>
                                        <th style="width: 120px;">Total</th>
                                        <th>Estado clínico</th>
                                        @can('permiso.pagos')
                                        <th style="width: 120px;">Estado pago</th>
                                        @endcan
                                        <th style="width: 140px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($consultas as $consulta)
                                        @php
                                            $total = $consulta->total_servicios;
                                            $puedeVerPagos = auth()->user()?->can('permiso.pagos') ?? false;
                                            $estadoPago = $puedeVerPagos ? $consulta->estado_pago_resumen : null;
                                            $colPago = match ($estadoPago) {
                                                'Pagado' => 'success',
                                                'Pendiente' => 'warning',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="fw-semibold text-center">{{ $consulta->id }}</td>
                                            <td>
                                                {{ optional($consulta->fecha)->format('d/m/Y') ?: $consulta->created_at->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                @if ($consulta->tipo == 'Emergencia')
                                                    <span class="badge bg-danger">Emergencia</span>
                                                @else
                                                    <span class="badge bg-primary">Reserva</span>
                                                @endif
                                                @if ($consulta->reserva_id)
                                                    <div class="small text-muted mt-1">Reserva #{{ $consulta->reserva_id }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $consulta->mascotum?->nombre ?? '—' }}</div>
                                                <div class="small text-muted">
                                                    {{ $consulta->mascotum?->especie?->nombre ?? '' }}
                                                    @if ($consulta->peso) · {{ $consulta->peso }} kg @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($consulta->mascotum?->cliente)
                                                    <div>{{ $consulta->mascotum->cliente->nombre }} {{ $consulta->mascotum->cliente->apellido }}</div>
                                                    @if ($consulta->mascotum->cliente->telefono)
                                                        <div class="small text-muted">{{ $consulta->mascotum->cliente->telefono }}</div>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $consulta->veterinario?->nombre }} {{ $consulta->veterinario?->apellido }}</td>
                                            <td>
                                                <div class="small text-truncate" style="max-width: 280px;" title="{{ $consulta->motivo }}">
                                                    {{ $consulta->motivo ?? '—' }}
                                                </div>
                                            </td>
                                            <td class="text-end fw-semibold">Bs {{ number_format($total, 2) }}</td>
                                            <td>{!! $consulta->estado_badge !!}</td>
                                            @can('permiso.pagos')
                                            <td><span class="badge bg-{{ $colPago }}">{{ $estadoPago }}</span></td>
                                            @endcan
                                            <td>
                                                <form action="{{ route('consulta.destroy', $consulta->id) }}" method="POST">
                                                    <div class="btn-group btn-group-sm w-100">
                                                        <a href="{{ route('consulta.show', $consulta->id) }}"
                                                           class="btn btn-outline-info">
                                                            Ver
                                                        </a>

                                                        @can('update', $consulta)
                                                        <a href="{{ route('consulta.edit', $consulta->id) }}"
                                                           class="btn btn-outline-primary">
                                                            Editar
                                                        </a>
                                                        @endcan

                                                        @csrf
                                                        @method('DELETE')
                                                        <!--
                                                        <button type="submit" class="btn btn-outline-danger"
                                                                onclick="return confirm('¿Eliminar consulta #{ $consulta->id }}?')">
                                                            Borrar
                                                        </button> -->
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()?->can('permiso.pagos') ? 11 : 10 }}" class="text-center text-muted py-5">
                                                Aún no hay consultas registradas.
                                                Usa el botón <strong>Nueva Consulta</strong> para comenzar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        {{ $consultas->withQueryString()->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
