<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover shadow-sm">
        <thead class="bg-dark text-white">
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th>{{ __('Nombre') }}</th>
                <th>{{ __('Descripción') }}</th>
                <th>{{ __('Estado') }}</th>
                <th class="text-center" style="width: 200px;">{{ __('Acciones') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subServicios as $subServicio)
                <tr>
                    <td class="text-center align-middle">{{ $loop->iteration + ($subServicios->perPage() * ($subServicios->currentPage() - 1)) }}</td>
                    <td class="align-middle font-weight-bold text-dark">{{ $subServicio->nombre }}</td>
                    <td class="align-middle">{{ $subServicio->descripcion }}</td>
                    <td class="align-middle">
                        <span class="badge {{ $subServicio->estado ? 'badge-success' : 'badge-secondary' }} shadow-sm p-2">
                            {{ $subServicio->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        <form action="{{ route('sub_servicios.destroy', $subServicio->id) }}" method="POST" class="m-0 form-eliminar">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-success shadow-sm" href="{{ route('sub_servicios.edit', $subServicio->id) }}" title="Editar">
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
