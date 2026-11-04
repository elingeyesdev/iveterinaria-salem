@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1>{{ __('Administrar Rol Usuario') }}</h1>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="mr-2">{{ __('Mostrar:') }}</span>
                                    <select name="perPage" id="perPageSelect" class="form-control" style="width: auto;" onchange="updatePerPage(this.value)">
                                        <option value="5" {{ request('perPage') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="15" {{ request('perPage') == 15 ? 'selected' : '' }}>15</option>
                                    </select>
                                </div>
                            </div>

                            <div class="float-right">
                                <form id="searchForm" action="{{ route('users.index') }}" method="GET" role="search">
                                    <input type="hidden" name="perPage" value="{{ request('perPage', 5) }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" placeholder="Buscar usuario..." value="{{ request('search') }}">
                                        <span class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white" id="table-container">
                        <!-- Aca llamo a mi tabla -->
                        @include('user._table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    let debounceTimer;
    const searchInput = document.querySelector('input[name="search"]');
    const tableContainer = document.getElementById('table-container');
    const perPageInput = document.querySelector('input[name="perPage"]');

    function updatePerPage(value) {
        perPageInput.value = value;
        performSearch(searchInput.value, value, 1);
    }

    searchInput.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            performSearch(e.target.value, perPageInput.value, 1);
        }, 300);
    });

    function performSearch(search, perPage, page = 1) {
        fetch('{{ route('users.index') }}?search=' + encodeURIComponent(search) + '&perPage=' + encodeURIComponent(perPage) + '&page=' + page, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            // Reattach event listeners for pagination links
            document.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const pageNum = url.searchParams.get('page') || 1;
                    performSearch(search, perPage, pageNum);
                });
            });
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endsection
