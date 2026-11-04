@extends('adminlte::page')

@section('title', 'Reservas')

@section('content_header')
    <h1>Administrar Reservas</h1>
@stop

@section('content')
<div class="card border-0 shadow-sm">



    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('reserva.create') }}" class="btn btn-primary mr-4">
                    <i class="fas fa-plus-circle"></i> {{ __('Registrar Reserva') }}
                </a>

                <div class="d-flex align-items-center">
                    <span class="mr-2">{{ __('Mostrar:') }}</span>
                    <select name="perPage" class="form-control" style="width: auto;" onchange="updatePerPage(this.value)">
                        <option value="5" {{ request('perPage') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('perPage') == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ request('perPage', 20) == 20 ? 'selected' : '' }}>20</option>
                    </select>
                </div>
            </div>

            <div class="float-right">
                <form id="searchForm" action="{{ route('reserva.index') }}" method="GET" role="search">
                    <input type="hidden" name="perPage" value="{{ request('perPage', 20) }}">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Buscar reserva..." value="{{ request('search') }}">
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

    @if(session('success'))
        <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger m-3 mb-0">{{ session('error') }}</div>
    @endif

    <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-toggle="tab" data-target="#tabla">Tabla</button></li>
            <li class="nav-item"><button class="nav-link" data-toggle="tab" data-target="#calendario">Calendario</button></li>
        </ul>

        <div class="tab-content pt-3">
            <div class="tab-pane fade show active" id="tabla">
                <div id="table-container">
                    @include('reserva._table')
                </div>
            </div>

            <div class="tab-pane fade" id="calendario">
                <div id="calendar" style="min-height: 650px;"></div>
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet">
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
        buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', list: 'Lista' },
        events: '{{ route('reserva.eventos') }}',
        eventClick: function (info) {
            const p = info.event.extendedProps;
            Swal.fire({
                title: 'Detalle de reserva',
                html: `<div class="text-start"><p><strong>Cliente:</strong> ${p.cliente}</p><p><strong>Mascota:</strong> ${p.mascota}</p><p><strong>Fecha:</strong> ${p.fecha}</p><p><strong>Horario:</strong> ${p.hora}</p><p><strong>Estado:</strong> ${p.estado}</p></div>`,
                icon: 'info',
                confirmButtonText: 'Entendido'
            });
        }
    });
    calendar.render();
});
</script>
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
        const params = new URLSearchParams({
            search: search,
            perPage: perPage,
            page: page
        });

        fetch('{{ route('reserva.index') }}?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
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
@endpush
