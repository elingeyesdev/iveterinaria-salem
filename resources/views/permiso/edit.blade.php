@extends('adminlte::page')

@section('title', 'Asignar permisos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 class="mb-2">{{ __('Asignar permisos') }}</h1>
        <a href="{{ route('permisos.index') }}" class="btn btn-outline-secondary mb-2">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card card-outline card-primary">
            <div class="card-body d-flex align-items-center flex-wrap">
                <div class="mr-3 mb-2">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        {{ mb_strtoupper(mb_substr($usuario->name, 0, 1)) }}
                    </span>
                </div>
                <div class="mb-2">
                    <h5 class="mb-0 font-weight-bold">{{ $usuario->name }}</h5>
                    <div class="text-muted small">{{ $usuario->email }}</div>
                    <div class="mt-1">
                        @forelse ($usuario->roles as $rol)
                            <span class="badge badge-secondary">{{ $rol->nombre }}</span>
                        @empty
                            <span class="text-muted small">Sin rol</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-times-circle mr-2"></i><strong>No se guardaron los cambios.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php($seleccionados = collect(old('permisos', $asignados))->map(fn ($id) => (int) $id)->all())

        <form method="POST" action="{{ route('permisos.update', $usuario) }}" id="formPermisos">
            @csrf
            @method('PUT')

            <div class="row">
                @foreach ($permisosPorModulo as $modulo => $permisos)
                    @php($idModulo = 'modulo-'.\Illuminate\Support\Str::slug($modulo))
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100" data-modulo="{{ $idModulo }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold mb-0">{{ $modulo }}</h3>
                                <div class="custom-control custom-checkbox ml-auto">
                                    <input type="checkbox" class="custom-control-input js-todo-modulo" id="{{ $idModulo }}-todo" data-modulo="{{ $idModulo }}">
                                    <label class="custom-control-label small" for="{{ $idModulo }}-todo">Todo</label>
                                </div>
                            </div>
                            <ul class="list-group list-group-flush">
                                @foreach ($permisos as $permiso)
                                    <li class="list-group-item">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox"
                                                   class="custom-control-input js-permiso"
                                                   id="permiso-{{ $permiso->id }}"
                                                   name="permisos[]"
                                                   value="{{ $permiso->id }}"
                                                   data-modulo="{{ $idModulo }}"
                                                   data-requiere="{{ implode(',', $dependencias[$permiso->id] ?? []) }}"
                                                   @checked(in_array($permiso->id, $seleccionados, true))>
                                            <label class="custom-control-label font-weight-normal" for="permiso-{{ $permiso->id }}">
                                                {{ $permiso->nombre }}
                                            </label>
                                        </div>
                                        @if ($permiso->descripcion)
                                            <small class="text-muted d-block ml-4 pl-2">{{ $permiso->descripcion }}</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card mt-3">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                    <span class="text-muted mb-2">
                        <i class="fas fa-check-square mr-1"></i>
                        <strong id="contadorSeleccionados">0</strong> submódulo(s) seleccionado(s)
                    </span>
                    <div class="mb-2">
                        <a href="{{ route('permisos.index') }}" class="btn btn-outline-secondary mr-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Guardar permisos
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script>
    (function () {
        const permisos = Array.from(document.querySelectorAll('.js-permiso'));
        const togglesModulo = Array.from(document.querySelectorAll('.js-todo-modulo'));
        const contador = document.getElementById('contadorSeleccionados');

        function permisosDelModulo(modulo) {
            return permisos.filter(p => p.dataset.modulo === modulo);
        }

        function marcarDependencias(casilla) {
            if (!casilla.checked || !casilla.dataset.requiere) {
                return;
            }
            casilla.dataset.requiere.split(',').forEach(id => {
                const requerido = document.getElementById('permiso-' + id);
                if (requerido && !requerido.checked) {
                    requerido.checked = true;
                }
            });
        }

        function desmarcarDependientes(casilla) {
            if (casilla.checked) {
                return;
            }
            permisos
                .filter(p => p.checked && (p.dataset.requiere || '').split(',').includes(casilla.value))
                .forEach(p => { p.checked = false; });
        }

        function refrescar() {
            togglesModulo.forEach(toggle => {
                const lista = permisosDelModulo(toggle.dataset.modulo);
                const marcados = lista.filter(p => p.checked).length;
                toggle.checked = marcados === lista.length && lista.length > 0;
                toggle.indeterminate = marcados > 0 && marcados < lista.length;
            });
            contador.textContent = permisos.filter(p => p.checked).length;
        }

        permisos.forEach(casilla => {
            casilla.addEventListener('change', () => {
                marcarDependencias(casilla);
                desmarcarDependientes(casilla);
                refrescar();
            });
        });

        togglesModulo.forEach(toggle => {
            toggle.addEventListener('change', () => {
                permisosDelModulo(toggle.dataset.modulo).forEach(p => {
                    p.checked = toggle.checked;
                    marcarDependencias(p);
                    desmarcarDependientes(p);
                });
                refrescar();
            });
        });

        refrescar();
    })();
</script>
@endsection
