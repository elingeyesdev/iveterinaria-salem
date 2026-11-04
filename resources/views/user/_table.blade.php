<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover shadow-sm">
        <thead class="bg-dark text-white">
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th class="text-center" style="width: 200px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td class="text-center align-middle">{{ $loop->iteration + ($users->perPage() * ($users->currentPage() - 1)) }}</td>
                    <td class="align-middle font-weight-bold text-dark">{{ $user->name }}</td>
                    <td class="align-middle">{{ $user->email }}</td>
                    <td class="align-middle">
                        <span class="badge badge-info shadow-sm p-2">{{ $user->role?->nombre }}</span>
                    </td>
                    <td class="text-center align-middle">
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="m-0 form-eliminar">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-success shadow-sm" href="{{ route('users.edit', $user->id) }}" title="Editar">
                                    <i class="fa fa-fw fa-edit"></i> {{ __('Asignar Rol') }}
                                </a>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm btn-eliminar" title="Eliminar">
                                    <i class="fa fa-fw fa-trash"></i> {{ __('Eliminar Rol') }}
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
    {!! $users->appends(request()->except('page'))->links('vendor.pagination.custom') !!}
</div>