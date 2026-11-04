<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover shadow-sm">
        <thead class="bg-dark text-white">
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th>{{ __('Nombre') }}</th>
                <th>{{ __('Subservicio') }}</th>
                <th>{{ __('Descripción') }}</th>
                <th>{{ __('Precio') }}</th>
                <th>{{ __('Estado') }}</th>
                <th class="text-center" style="width: 200px;">{{ __('Acciones') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($servicios as $servicio)
                <tr>
                    <td class="text-center align-middle">{{ $loop->iteration + ($servicios->perPage() * ($servicios->currentPage() - 1)) }}</td>
                    <td class="align-middle font-weight-bold text-dark">{{ $servicio->nombre }}</td>
                    <td class="align-middle">{{ $servicio->subServicio?->nombre ?? __('Sin asignar') }}</td>
                    <td class="align-middle">{{ $servicio->descripcion }}</td>
                    <td class="align-middle">{{ $servicio->precio }}</td>
                    <td class="align-middle">
                        <span class="badge {{ $servicio->estado ? 'badge-success' : 'badge-secondary' }} shadow-sm p-2">
                            {{ $servicio->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        <form action="{{ route('servicios.destroy', $servicio->id) }}" method="POST" class="m-0 form-eliminar">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-success shadow-sm" href="{{ route('servicios.edit', $servicio->id) }}" title="Editar">
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
