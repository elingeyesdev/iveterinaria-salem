<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover shadow-sm">
        <thead class="bg-dark text-white">
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th class="text-center">{{ __('Foto') }}</th>
                <th>{{ __('Nombre') }}</th>
                <th>{{ __('Especie') }}</th>
                <th>{{ __('Raza') }}</th>
                <th>{{ __('Dueño') }}</th>
                <th>{{ __('Sexo') }}</th>
                <th>{{ __('Peso') }}</th>
                <th>{{ __('Estado') }}</th>
                <th class="text-center" style="width: 200px;">{{ __('Acciones') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mascota as $mascotum)
                <tr>
                    <!-- Numeración paginada -->
                    <td class="text-center align-middle">
                        {{ $loop->iteration + ($mascota->perPage() * ($mascota->currentPage() - 1)) }}
                    </td>
                    
                    <!-- Foto de la mascota -->
                    <td class="text-center align-middle">
                        @if($mascotum->imagen)
                            <img src="{{ Storage::url($mascotum->imagen) }}" alt="Foto" width="50" height="50" class="rounded-circle shadow-sm" style="object-fit: cover;">
                        @else
                            <span class="badge badge-secondary shadow-sm">Sin foto</span>
                        @endif
                    </td>
                    
                    <!-- Datos principales -->
                    <td class="align-middle font-weight-bold text-dark">{{ $mascotum->nombre }}</td>
                    <td class="align-middle">{{ $mascotum->especie->nombre ?? 'N/A' }}</td>
                    <td class="align-middle">{{ $mascotum->raza->nombre ?? 'N/A' }}</td>
                    <td class="align-middle">{{ $mascotum->cliente->nombre ?? 'N/A' }} {{ $mascotum->cliente->apellido ?? '' }}</td>
                    
                    <!-- Sexo con iconos de FontAwesome -->
                    <td class="align-middle">
                        @if($mascotum->sexo === 'Macho')
                            <span class="text-primary"><i class="fa fa-fw fa-mars"></i> Macho</span>
                        @elseif($mascotum->sexo === 'Hembra')
                            <span class="text-danger"><i class="fa fa-fw fa-venus"></i> Hembra</span>
                        @else
                            <span class="text-muted">No especificado</span>
                        @endif
                    </td>
                    
                    <td class="align-middle">{{ $mascotum->peso }} kg</td>
                    
                    <!-- Estado -->
                    <td class="align-middle">
                        <span class="badge {{ $mascotum->estado ? 'badge-success' : 'badge-secondary' }} shadow-sm p-2">
                            {{ $mascotum->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    
                    <!-- Acciones -->
                    <td class="text-center align-middle">
                        <form action="{{ route('mascota.destroy', $mascotum->id) }}" method="POST" class="m-0 form-eliminar">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-info shadow-sm" href="{{ route('mascota.show', $mascotum) }}" title="Ver ficha integral">
                                    <i class="fa fa-fw fa-eye" aria-hidden="true"></i> Ver ficha
                                </a>
                                <a class="btn btn-sm btn-outline-success shadow-sm" href="{{ route('mascota.edit', $mascotum->id) }}" title="Editar">
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

<!-- Paginación -->
<div class="d-flex justify-content-end mt-4">
    {!! $mascota->appends(request()->except('page'))->links('vendor.pagination.custom') !!}
</div>