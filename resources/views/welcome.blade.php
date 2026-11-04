<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria Salem | Cuidado experto para tu mejor amigo</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome para Íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1576201836106-db1758fd1c97?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
            color: white;
            padding: 120px 0;
            min-height: 80vh;
            display: flex;
            align-items: center;
        }
        .service-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            background-color: #f1f2f6;
            color: #2ed573;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .top-bar { background-color: #2f3542; color: white; padding: 8px 0; font-size: 14px; }
    </style>
</head>
<body>

    <!-- Top Bar (Emergencias y Horarios) -->
    <div class="top-bar text-center text-md-start">
        <div class="container d-flex justify-content-between flex-wrap">
            <span><i class="fa-solid fa-clock me-2"></i> Lunes a Sabado: 08:00 - 20:00</span>
            <span class="fw-bold text-warning"><i class="fa-solid fa-phone me-2"></i> Emergencias 24/7: +591 8588886</span>
        </div>
    </div>

    <!-- Navbar Principal -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-primary" href="#">
                <i class="fa-solid fa-paw me-2 text-danger"></i>Veterinaria Salem
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active fw-bold" href="#">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold" href="#servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold" href="#equipo">Nuestro Equipo</a></li>
                </ul>
                <div class="d-flex">
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">Ir a mi Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4 me-2">Iniciar Sesión</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-md-7 col-lg-6">
                    <span class="badge bg-danger mb-3 px-3 py-2 fs-6 rounded-pill">Expertos en pequeños animales</span>
                    <h1 class="display-4 fw-bold mb-4">Amor incondicional, cuidado excepcional.</h1>
                    <p class="lead mb-4">Brindamos servicios veterinarios de alta calidad con equipos de última generación y un equipo médico que tratará a tu mascota como parte de su familia.</p>
                    <a href="#servicios" class="btn btn-light btn-lg fw-bold">Ver Servicios</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- SECCIÓN: BENEFICIOS / INDICADORES RAPIDOS  -->
    <!-- ========================================== -->
    <section class="py-5 bg-white border-bottom">
        <div class="container">
            <div class="row g-4 text-center">
                
                <!-- Beneficio 1: 24 Horas -->
                <div class="col-md-6 col-lg-3">
                    <div class="px-3">
                        <div class="icon-box mx-auto mb-3" style="background-color: #ffeaa7; color: #fdcb6e;">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <h5 class="fw-bold">Veterinaria 24hrs</h5>
                        <p class="text-muted small mb-0">Brindamos atención 24hrs para todas las emergencias.</p>
                    </div>
                </div>

                <!-- Beneficio 2: Seguros -->
                <div class="col-md-6 col-lg-3">
                    <div class="px-3">
                        <div class="icon-box mx-auto mb-3" style="background-color: #d1ccc0; color: #84817a;">
                            <i class="fa-solid fa-shield-dog"></i>
                        </div>
                        <h5 class="fw-bold">Seguros</h5>
                        <p class="text-muted small mb-0">Junto a Alianza Seguros creamos el seguro Guau.</p>
                    </div>
                </div>

                <!-- Beneficio 3: Alimentación -->
                <div class="col-md-6 col-lg-3">
                    <div class="px-3">
                        <div class="icon-box mx-auto mb-3" style="background-color: #ffda79; color: #cc8e35;">
                            <i class="fa-solid fa-bowl-food"></i>
                        </div>
                        <h5 class="fw-bold">Alimentación</h5>
                        <p class="text-muted small mb-0">Contamos con los mejores productos de alimentación.</p>
                    </div>
                </div>

                <!-- Beneficio 4: Envíos -->
                <div class="col-md-6 col-lg-3">
                    <div class="px-3">
                        <div class="icon-box mx-auto mb-3" style="background-color: #c7ecee; color: #22a6b3;">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <h5 class="fw-bold">Envíos gratis</h5>
                        <p class="text-muted small mb-0">En compras dentro del 4to anillo.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- SECCIÓN: SERVICIOS                         -->
    <!-- ========================================== -->
    <section id="servicios" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h6 class="text-primary fw-bold text-uppercase">Lo que hacemos</h6>
                <h2 class="fw-bold">Nuestros Servicios Veterinarios</h2>
            </div>
            <div class="row g-4">
                <!-- Servicio 1 -->
                <div class="col-md-4">
                    <div class="card service-card p-4 h-100 text-center">
                        <div class="icon-box mx-auto"><i class="fa-solid fa-stethoscope"></i></div>
                        <h4 class="fw-bold mb-3">Consulta Médica</h4>
                        <p class="text-muted mb-0">Evaluaciones completas para asegurar que tu mascota mantenga un estado de salud óptimo en cada etapa de su vida.</p>
                    </div>
                </div>
                <!-- Servicio 2 -->
                <div class="col-md-4">
                    <div class="card service-card p-4 h-100 text-center">
                        <div class="icon-box mx-auto"><i class="fa-solid fa-syringe"></i></div>
                        <h4 class="fw-bold mb-3">Vacunación</h4>
                        <p class="text-muted mb-0">Programas de vacunación y desparasitación adaptados a la especie, raza y edad de tu engreído.</p>
                    </div>
                </div>
                <!-- Servicio 3 -->
                <div class="col-md-4">
                    <div class="card service-card p-4 h-100 text-center">
                        <div class="icon-box mx-auto"><i class="fa-solid fa-scissors"></i></div>
                        <h4 class="fw-bold mb-3">Peluquería y Spa</h4>
                        <p class="text-muted mb-0">Cortes de raza, baños medicados y estética integral para que tu mascota luzca y se sienta espectacular.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-paw me-2 text-danger"></i>Veterinaria Salem</h5>
                    <p class="text-secondary">Tu clínica veterinaria de confianza. Innovación y empatía al servicio de la salud animal.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold mb-4">Contáctanos</h5>
                    <p class="text-secondary mb-1"><i class="fa-solid fa-location-dot me-2"></i> Av. Paragua Entre 2do y 3er Anillo</p>
                    <p class="text-secondary mb-1"><i class="fa-solid fa-phone me-2"></i> +591 78588886</p>
                    <p class="text-secondary"><i class="fa-solid fa-envelope me-2"></i> contacto@Salem.com</p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold mb-4">Portal Cliente</h5>
                    <p class="text-secondary">Accede a tus reservas, carnet de vacunación e historial clínico ingresando al sistema.</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill px-4">Ingresar al Sistema</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>