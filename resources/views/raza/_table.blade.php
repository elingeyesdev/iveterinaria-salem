<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover shadow-sm">
        <thead class="bg-dark text-white">
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th>{{ __('Nombre') }}</th>
                <th class="text-center">{{ __('Cantidad Mascotas') }}</th>
                <th class="text-center">{{ __('Estado') }}</th>
                <th class="text-center" style="width: 200px;">{{ __('Acciones') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($razas as $raza)
                <tr>
                    <td class="text-center align-middle">
                        {{ $loop->iteration + ($razas->perPage() * ($razas->currentPage() - 1)) }}
                    </td>
                    <td class="align-middle font-weight-bold text-dark">{{ $raza->nombre }}</td>
                    <td class="text-center align-middle">
                        <span class="badge badge-info shadow-sm p-2">{{ $raza->mascotas_count }}</span>
                    </td>
                    <td class="text-center align-middle">
                        <span class="badge {{ $raza->estado ? 'badge-success' : 'badge-secondary' }} shadow-sm p-2">
                            {{ $raza->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        <form action="{{ route('razas.destroy', $raza->id) }}" method="POST" class="m-0 form-eliminar">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-success shadow-sm" href="{{ route('razas.edit', $raza->id) }}" title="Editar">
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
    {!! $razas->appends(request()->except('page'))->links('vendor.pagination.custom') !!}
</div>
