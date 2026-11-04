<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover shadow-sm">
        <thead class="bg-dark text-white">
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th>{{ __('Nombre') }}</th>
                <th>{{ __('Apellido') }}</th>
                <th>{{ __('Dirección') }}</th>
                <th>{{ __('Correo') }}</th>
                <th>{{ __('Teléfono') }}</th>
                <th>{{ __('Estado') }}</th>
                <th class="text-center" style="width: 200px;">{{ __('Acciones') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clientes as $cliente)
                <tr>
                    <td class="text-center align-middle">{{ $loop->iteration + ($clientes->perPage() * ($clientes->currentPage() - 1)) }}</td>
                    <td class="align-middle font-weight-bold text-dark">{{ $cliente->nombre }}</td>
                    <td class="align-middle">{{ $cliente->apellido }}</td>
                    <td class="align-middle">{{ $cliente->direccion }}</td>
                    <td class="align-middle">{{ $cliente->correo }}</td>
                    <td class="align-middle">{{ $cliente->telefono }}</td>
                    <td class="align-middle">
                        <span class="badge {{ $cliente->estado ? 'badge-success' : 'badge-secondary' }} shadow-sm p-2">
                            {{ $cliente->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="m-0 form-eliminar">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-success shadow-sm" href="{{ route('clientes.edit', $cliente->id) }}" title="Editar">
                                    <i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}
                                </a>
                                @csrf
                                @method('DELETE')
                                @can('eliminar-registros')
<button type="submit" class="btn btn-sm btn-outline-danger shadow-sm btn-eliminar" title="Eliminar">
                                    <i class="fa fa-fw fa-trash"></i> {{ __('Eliminar') }}
                                </button>
@endcan
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-4">
    {!! $clientes->appends(request()->except('page'))->links('vendor.pagination.custom') !!}
</div>