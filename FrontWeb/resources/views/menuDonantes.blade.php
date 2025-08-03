<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Donador</title>
    <link rel="stylesheet" href="{{ asset('css/menus.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="dashboard-container">
        <!-- Botón de cerrar sesión -->
        <div class="logout-container">
            <form action="/logout" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Cerrar Sesión
                </button>
            </form>
        </div>

        <!-- Hero Section para Donador -->
        <section class="hero-section donador">
            <div class="welcome-title">
                <h1>¡Bienvenido Donador!</h1>
            </div>

            <div class="modules-grid donador">
                <!-- Módulo Perfil -->
                <div class="module-card" onclick="window.location.href='{{ route('perfilDonante') }}'">
                    <div class="module-content">
                        <div class="module-icon">
                            <img src="{{ asset('images/Perfiles.png') }}" alt="Perfil" class="icon-img">
                        </div>
                        <h3>Perfil</h3>
                    </div>
                </div>

                <!-- Módulo Donaciones Realizadas -->
                <div class="module-card" onclick="#">
                    <div class="module-content">
                        <div class="module-icon">
                            <img src="{{ asset('images/Donaciones.png') }}" alt="Donaciones" class="icon-img">
                        </div>
                        <h3>Donaciones Realizadas</h3>
                    </div>
                </div>

                <!-- Módulo Eventos -->
                <div class="module-card" onclick="#">
                    <div class="module-content">
                        <div class="module-icon">
                            <img src="{{ asset('images/Eventos.png') }}" alt="Eventos" class="icon-img">
                        </div>
                        <h3>Eventos</h3>
                    </div>
                </div>

                <!-- Módulo Estatus Solicitudes -->
                <div class="module-card" onclick="#">
                    <div class="module-content">
                        <div class="module-icon">
                            <img src="{{ asset('images/Estatus.png') }}" alt="Solicitudes" class="icon-img">
                        </div>
                        <h3>Estatus Solicitudes</h3>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Scripts adicionales -->
    <script>
        // Confirmación para cerrar sesión
        document.querySelector('.logout-btn').addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>
