<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Beneficiario</title>
    <link href="{{ asset('css/menus.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="dashboard-container">
        <!-- Botón de cerrar sesión -->
        <div class="logout-container">
            <form action="/logout" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Cerrar Sesión
                </button>
            </form>
        </div>

        <!-- Hero Section con imagen de fondo -->
        <div class="hero-section beneficiario">
            <!-- Título de bienvenida -->
            <div class="welcome-title">
                <h1>¡Bienvenido Beneficiario!</h1>
            </div>

            <!-- Grid de módulos -->
            <div class="modules-grid beneficiario">

                <!-- Módulo Perfil -->
                <div class="module-card" onclick="window.location.href='{{ route('perfilBeneficiario') }}'">
                    <div class="module-content">
                        <div class="module-icon">
                            <img src="{{ asset('images/Perfiles.png') }}" alt="Perfil" class="icon-img">
                        </div>
                        <h3>Perfil</h3>
                    </div>
                </div>

                <!-- Módulo Eventos -->
                <div class="module-card" onclick="window.location.href='/eventos-beneficiario'">
                    <div class="module-content">
                        <div class="module-icon">
                            <img src="{{ asset('images/Eventos.png') }}" alt="Eventos" class="icon-img">
                        </div>
                        <h3>Eventos</h3>
                    </div>
                </div>
            </div>
        </div>
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
