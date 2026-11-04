<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Horario</th>
                <th>Cliente</th>
                <th>Mascota</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($reservas as $reserva)
            @php $cliente = $reserva->mascota?->cliente; @endphp
            <tr>
                <td>{{ $loop->iteration + ($reservas->perPage() * ($reservas->currentPage() - 1)) }}</td>
                <td>{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                <td>{{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</td>
                <td>{{ trim(($cliente?->nombre ?? '') . ' ' . ($cliente?->apellido ?? '')) ?: 'Sin cliente' }}</td>
                <td>{{ $reserva->mascota?->nombre ?? 'Sin mascota' }}</td>
                <td>
                    @if($reserva->estado === 'Pendiente')
                        <span class="badge badge-warning">Pendiente</span>
                    @elseif($reserva->estado === 'Atendida')
                        <span class="badge badge-success">Atendida</span>
                    @elseif($reserva->estado === 'No asistio')
                        <span class="badge badge-secondary">No asistio</span>
                    @else
                        <span class="badge badge-danger">Cancelado</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Acciones de la reserva {{ $reserva->id }}">
                        <a class="btn btn-outline-info" href="{{ route('reserva.show', $reserva) }}" title="Ver" aria-label="Ver reserva {{ $reserva->id }}">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </a>
                        @can('permiso.consultas')
                            @if($reserva->estado === \App\Support\ReservaEstado::PENDIENTE && !$reserva->consulta)
                                <a class="btn btn-primary" href="{{ route('consulta.create', ['reserva_id' => $reserva->id]) }}" title="Atender" aria-label="Atender reserva {{ $reserva->id }}">
                                    <i class="fas fa-stethoscope" aria-hidden="true"></i>
                                </a>
                            @else
                                <button type="button" class="btn btn-primary" disabled aria-disabled="true" title="No se puede atender esta reserva" aria-label="Atender reserva {{ $reserva->id }} no disponible">
                                    <i class="fas fa-stethoscope" aria-hidden="true"></i>
                                </button>
                            @endif
                        @else
                            <button type="button" class="btn btn-primary" disabled aria-disabled="true" title="No tiene permiso para atender" aria-label="Atender reserva {{ $reserva->id }} no disponible">
                                <i class="fas fa-stethoscope" aria-hidden="true"></i>
                            </button>
                        @endcan
                        @can('update', $reserva)
                            <a class="btn btn-warning" href="{{ route('reserva.edit', $reserva) }}" title="Editar" aria-label="Editar reserva {{ $reserva->id }}">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                            </a>
                        @else
                            <button type="button" class="btn btn-warning" disabled aria-disabled="true" title="No se puede editar esta reserva" aria-label="Editar reserva {{ $reserva->id }} no disponible">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                            </button>
                        @endif
                        @can('changeStatus', $reserva)
                            @if($reserva->estado === \App\Support\ReservaEstado::PENDIENTE)
                            <form action="{{ route('reserva.estado', $reserva) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="estado" value="{{ \App\Support\ReservaEstado::CANCELADA }}">
                                <button class="btn btn-secondary" title="Cancelar" aria-label="Cancelar reserva {{ $reserva->id }}">
                                    <i class="fas fa-ban" aria-hidden="true"></i>
                                </button>
                            </form>
                            @else
                                <button type="button" class="btn btn-secondary" disabled aria-disabled="true" title="No se puede cancelar esta reserva" aria-label="Cancelar reserva {{ $reserva->id }} no disponible">
                                    <i class="fas fa-ban" aria-hidden="true"></i>
                                </button>
                            @endif
                        @else
                            <button type="button" class="btn btn-secondary" disabled aria-disabled="true" title="No tiene permiso para cancelar" aria-label="Cancelar reserva {{ $reserva->id }} no disponible">
                                <i class="fas fa-ban" aria-hidden="true"></i>
                            </button>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No existen reservas registradas.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">
    {{ $reservas->appends(request()->except('page'))->links('vendor.pagination.custom') }}
</div>
