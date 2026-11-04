<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Vet System') }}</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #f4f6f9; }
        .app-navbar { border-bottom: 1px solid #e9ecef; }
        .brand-mark { width: 34px; height: 34px; display:inline-flex; align-items:center; justify-content:center; border-radius:9px; background:#0d6efd; color:#fff; }
        .public-shell { min-height: calc(100vh - 56px); }
        .navbar .nav-link { font-weight: 600; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm app-navbar">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ url('/') }}">
                <span class="brand-mark"><i class="fas fa-paw"></i></span>
                <span>{{ config('app.name', 'Vet System') }}</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#appNavbar" aria-controls="appNavbar" aria-expanded="false" aria-label="Abrir navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="appNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}"><i class="fas fa-home me-1"></i>Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('reserva.index') }}"><i class="fas fa-calendar-alt me-1"></i>Reservas</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('consulta.index') }}"><i class="fas fa-stethoscope me-1"></i>Consultas</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('clientes.index') }}"><i class="fas fa-users me-1"></i>Clientes</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('mascota.index') }}"><i class="fas fa-paw me-1"></i>Mascotas</a></li>
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a></li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarUser" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarUser">
                                <li><h6 class="dropdown-header">Cuenta</h6></li>
                                <li><span class="dropdown-item-text small text-muted">{{ Auth::user()->email }}</span></li>
                                @if(Auth::user()->hasRole('admin'))
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('users.index') }}"><i class="fas fa-users-cog me-2"></i>Administración</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="public-shell py-4">
        @yield('content')
    </main>

    @include('partials.sweetalert')
</body>
</html>
