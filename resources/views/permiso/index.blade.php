@extends('adminlte::page')

@section('title', 'Permisos de usuario')

@section('content_header')
    <h1>{{ __('Permisos de usuario') }}</h1>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif
                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="status">
                        <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                <div class="callout callout-info">
                    <p class="mb-0">
                        Elige un usuario y marca los submódulos que puede usar. Un usuario nuevo empieza
                        <strong>sin acceso</strong> a ningún submódulo. El administrador siempre tiene acceso total,
                        y el módulo <strong>Acceso y seguridad</strong> es exclusivo del administrador.
                    </p>
                </div>

                <div class="card">
                    <div class="card-header">
                        <form action="{{ route('permisos.index') }}" method="GET" role="search" class="float-right" style="max-width: 360px; width: 100%;">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Buscar por nombre o correo..." value="{{ $busqueda }}" aria-label="Buscar usuario">
                                <span class="input-group-append">
                                    <button class="btn btn-primary" type="submit" aria-label="Buscar"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>

                    <div class="card-body bg-white p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th style="min-width: 220px;">Acceso</th>
                                        <th class="text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($usuarios as $usuario)
                                        @php($esAdmin = $usuario->esAdministrador())
                                        <tr>
                                            <td class="font-weight-bold">{{ $usuario->name }}</td>
                                            <td>{{ $usuario->email }}</td>
                                            <td>
                                                @forelse ($usuario->roles as $rol)
                                                    <span class="badge badge-secondary">{{ $rol->nombre }}</span>
                                                @empty
                                                    <span class="text-muted small">Sin rol</span>
                                                @endforelse
                                            </td>
                                            <td>
                                                @if ($esAdmin)
                                                    <span class="badge badge-success"><i class="fas fa-shield-alt mr-1"></i> Acceso total</span>
                                                @else
                                                    @php($porcentaje = $totalPermisos > 0 ? round($usuario->permisos_count * 100 / $totalPermisos) : 0)
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                                                            <div class="progress-bar {{ $usuario->permisos_count === 0 ? 'bg-secondary' : 'bg-primary' }}" role="progressbar" style="width: {{ $porcentaje }}%" aria-valuenow="{{ $usuario->permisos_count }}" aria-valuemin="0" aria-valuemax="{{ $totalPermisos }}"></div>
                                                        </div>
                                                        <small class="text-nowrap {{ $usuario->permisos_count === 0 ? 'text-muted' : '' }}">
                                                            {{ $usuario->permisos_count }} de {{ $totalPermisos }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if ($esAdmin)
                                                    <span class="text-muted small"><i class="fas fa-lock mr-1"></i>No requiere</span>
                                                @else
                                                    <a href="{{ route('permisos.edit', $usuario) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-key mr-1"></i> Asignar permisos
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No se encontraron usuarios.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if ($usuarios->hasPages())
                        <div class="card-footer bg-white">
                            {{ $usuarios->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
