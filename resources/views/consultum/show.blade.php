@extends('adminlte::page')

@section('title', 'Detalle de Consulta')

@section('content_header')
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">
                                <i class="fa fa-stethoscope me-2"></i>
                                Detalle de Consulta #{{ $consulta->id }}
                            </span>
                        </div>
                        <div class="float-right d-flex gap-2">
                            <a class="btn btn-light btn-sm" href="{{ route('consulta.index') }}">
                                <i class="fa fa-arrow-left me-1"></i> Regresar
                            </a>
                        </div>
                    </div>

                    <div class="card-body bg-white">

                        @foreach (['success', 'info', 'warning', 'error'] as $mensaje)
                            @if(session($mensaje))
                                <div class="alert alert-{{ $mensaje === 'error' ? 'danger' : $mensaje }}" role="status">{{ session($mensaje) }}</div>
                            @endif
                        @endforeach

                        <div class="card mb-3">
                            <div class="card-body">
                                <p><strong>Estado clínico:</strong> {!! $consulta->estado_badge !!}</p>
                                @if($consulta->finalizada_at)
                                    <p>Finalizada el {{ $consulta->finalizada_at->format('d/m/Y H:i') }}</p>
                                @elseif($consulta->estado_clinico === null)
                                    <p>Consulta anterior a este control. Revisa la atención antes de confirmar su finalización.</p>
                                @endif
                                @if($consulta->estado_clinico !== \App\Models\Consulta::FINALIZADA)
                                    @can('complete', $consulta)
                                        <form method="POST" action="{{ route('consulta.completar', $consulta) }}" onsubmit="return confirm('¿Confirmas que terminó la atención clínica? El saldo se gestiona por separado.');">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Finalizar atención</button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </div>
                     

                        <!-- Datos Generales -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-light h-100">
                                    <div class="text-muted small mb-1">Tipo de Atención</div>
                                    @if($consulta->tipo == 'Emergencia')
                                        <span class="badge bg-danger"><i class="fa fa-exclamation-triangle me-1"></i> EMERGENCIA</span>
                                    @else
                                        <span class="badge bg-primary"><i class="fa fa-calendar me-1"></i> RESERVA</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-light h-100">
                                    <div class="text-muted small mb-1">Peso</div>
                                    <div class="fw-semibold">{{ $consulta->peso !== null ? $consulta->peso . ' kg' : '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-light h-100">
                                    <div class="text-muted small mb-1">Fecha registro</div>
                                    <div class="fw-semibold">{{ $consulta->created_at?->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-light h-100">
                                    <div class="text-muted small mb-1">Reserva asociada</div>
                                    @if($consulta->reserva)
                                        <div class="fw-semibold">
                                            #{{ $consulta->reserva->id }} · {{ $consulta->reserva->fecha }} {{ $consulta->reserva->hora_inicio }}
                                        </div>
                                    @else
                                        <span class="text-muted">Sin reserva (Emergencia)</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Involucrados -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="card border-0 bg-white shadow-sm h-100">
                                    <div class="card-header bg-light py-2">
                                        <i class="fa fa-user-md me-1"></i> <strong>Veterinario</strong>
                                    </div>
                                    <div class="card-body py-2">
                                        @if($consulta->veterinario)
                                            <div class="fw-semibold">{{ $consulta->veterinario->nombre }} {{ $consulta->veterinario->apellido }}</div>
                                            @if($consulta->veterinario->correo)
                                                <div class="small text-muted">{{ $consulta->veterinario->correo }}</div>
                                            @endif
                                            @if($consulta->veterinario->telefono)
                                                <div class="small text-muted">Tlf: {{ $consulta->veterinario->telefono }}</div>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-white shadow-sm h-100">
                                    <div class="card-header bg-light py-2">
                                        <i class="fa fa-paw me-1"></i> <strong>Mascota</strong>
                                    </div>
                                    <div class="card-body py-2">
                                        @if($consulta->mascotum)
                                            <div class="fw-semibold">
                                                {{ $consulta->mascotum->nombre }}
                                                @if($consulta->mascotum->sexo)
                                                    <span class="small text-muted ms-1">
                                                        ({{ $consulta->mascotum->sexo }})
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="small text-muted">
                                                @if($consulta->mascotum->especie) {{ $consulta->mascotum->especie->nombre }} @endif
                                                @if($consulta->mascotum->raza) · {{ $consulta->mascotum->raza->nombre }} @endif
                                            </div>
                                            @if($consulta->mascotum->cliente)
                                                <div class="small mt-1">
                                                    <strong>Dueño:</strong> {{ $consulta->mascotum->cliente->nombre }} {{ $consulta->mascotum->cliente->apellido }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-white shadow-sm h-100">
                                    <div class="card-header bg-light py-2">
                                        <i class="fa fa-user me-1"></i> <strong>Cliente (por reserva)</strong>
                                    </div>
                                    <div class="card-body py-2">
                                        @if($consulta->reserva?->cliente)
                                            <div class="fw-semibold">{{ $consulta->reserva->cliente->nombre }} {{ $consulta->reserva->cliente->apellido }}</div>
                                            @if($consulta->reserva->cliente->correo)
                                                <div class="small text-muted">{{ $consulta->reserva->cliente->correo }}</div>
                                            @endif
                                            @if($consulta->reserva->cliente->telefono)
                                                <div class="small text-muted">Tlf: {{ $consulta->reserva->cliente->telefono }}</div>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notas Clínicas -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group border rounded p-3 bg-light mb-0">
                                    <strong class="d-block mb-1"><i class="fa fa-comment-medical me-1"></i> Motivo</strong>
                                    <div>{{ $consulta->motivo ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group border rounded p-3 bg-light mb-0">
                                    <strong class="d-block mb-1"><i class="fa fa-stethoscope me-1"></i> Diagnóstico</strong>
                                    <div>{{ $consulta->diagnostico ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group border rounded p-3 bg-light mb-0">
                                    <strong class="d-block mb-1"><i class="fa fa-prescription-bottle me-1"></i> Tratamiento</strong>
                                    <div>{{ $consulta->tratamiento ?? '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- SERVICIOS REALIZADOS -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-0 bg-white shadow-sm mb-0">
                                    <div class="card-header bg-light py-2">
                                        <i class="fa fa-list-check me-1"></i> <strong>Servicios aplicados en la consulta</strong>
                                    </div>
                                    <div class="card-body p-0">
                                        @if($consulta->servicios && $consulta->servicios->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover mb-0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Servicio</th>
                                                            <th style="width: 150px;">Próxima fecha</th>
                                                            <th class="text-right" style="width: 150px;">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $totalServicios = 0; @endphp
                                                        @foreach($consulta->servicios as $servicio)
                                                            @php
                                                                $subtotal = (float) ($servicio->pivot->precio ?? 0);
                                                                $totalServicios += $subtotal;
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <div class="fw-semibold">{{ $servicio->subservicio->nombre }}</div>
                                                                    <small class="text-muted">{{ $servicio->nombre }}</small>
                                                                </td>
                                                                <td class="align-middle">
                                                                    {{ $servicio->pivot->fecha_prox }}
                                                                </td>
                                                                <td class="text-right align-middle fw-bold">Bs {{ number_format($subtotal, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="bg-light">
                                                            <th colspan="2" class="text-right py-3">TOTAL DE LA CONSULTA:</th>
                                                            <th class="text-right py-3 fs-5 text-primary">Bs {{ number_format($totalServicios, 2) }}</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @else
                                            <div class="p-4 text-muted text-center">
                                                <i class="fa fa-info-circle me-1"></i> No se registraron servicios para esta consulta.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- FIN SERVICIOS -->


                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection