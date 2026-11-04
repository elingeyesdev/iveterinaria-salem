<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover shadow-sm">
        <thead class="bg-dark text-white">
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Estado</th>
                <th class="text-center" style="width: 200px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($user as $users)
                <tr>
                    <td class="text-center align-middle">{{ $loop->iteration + ($user->perPage() * ($user->currentPage() - 1)) }}</td>
                    <td class="align-middle font-weight-bold text-dark">{{ $users->name }}</td>
                    <td class="align-middle">{{ $users->email }}</td>
                    <td class="align-middle">
                        <span class="badge {{ $users->estado ? 'badge-success' : 'badge-secondary' }} shadow-sm p-2">
                            {{ $users->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        <form action="{{ route('role-users.destroy', $users->id) }}" method="POST" class="m-0 form-eliminar">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-success shadow-sm" href="{{ route('role-users.edit', $users->id) }}" title="Editar">
                                    <i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}
                                </a>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm btn-eliminar" title="Eliminar">
                                    <i class="fa fa-fw fa-trash"></i> {{ __('Eliminar') }}
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-4">
    {!! $user->appends(request()->except('page'))->links('vendor.pagination.custom') !!}
</div>