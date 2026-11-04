<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover shadow-sm">
        <thead class="bg-dark text-white">
            <tr>
                <th class="text-center">No</th>
                <th>Fecha</th>
                <th>Consulta</th>
                <th class="text-right">Monto</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pagos as $pago)
                <tr>
                    <td class="text-center align-middle">{{ $pagos->firstItem() + $loop->index }}</td>
                    <td class="align-middle">{{ $pago->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="align-middle">#{{ $pago->consulta_id }}</td>
                    <td class="text-right align-middle">Bs {{ number_format((float) $pago->monto, 2) }}</td>
                    <td class="align-middle">
                        <span class="badge {{ $pago->estado === \App\Models\Pago::PAGADO ? 'badge-success' : ($pago->estado === \App\Models\Pago::PENDIENTE ? 'badge-warning' : 'badge-secondary') }} shadow-sm p-2">{{ $pago->estado }}</span>
                    </td>
                    <td class="text-center align-middle">
                        <div class="btn-group" role="group" aria-label="Acciones del pago {{ $pago->id }}">
                            @if($pago->estado === \App\Models\Pago::PENDIENTE)
                                <a href="{{ route('pagos.show', $pago) }}" class="btn btn-sm btn-outline-info shadow-sm" title="Pagar">
                                    <i class="fas fa-fw fa-money-bill-wave" aria-hidden="true"></i> Pagar
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-info shadow-sm" disabled aria-disabled="true" title="El pago no está pendiente">
                                    <i class="fas fa-fw fa-money-bill-wave" aria-hidden="true"></i> Pagar
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No se encontraron pagos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-4">
    {{ $pagos->links('vendor.pagination.custom') }}
</div>
