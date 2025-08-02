<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Solicitudes</title>
    <link rel="stylesheet" href="css/adminSolicitudes.css">
</head>

<body>
    <div class="admin-container">
        <!-- Header con botón de regreso -->
        <header class="admin-header">
            <button class="back-btn" onclick="window.location.href='{{ route('admin.menu') }}'">
                <svg class="back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </button>
            <h1 class="page-title">Administración de Solicitudes</h1>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="modules-container">
                <!-- Módulo Perfiles -->
                <div class="module-card" onclick="window.location.href='{{ route('solicitudesPerfiles') }}'">
                    <div class="module-content">
                        <div class="module-icon">
                            <img src="images/Perfiles.png" alt="Perfiles" class="icon-img">
                        </div>
                        <h3>Perfiles</h3>
                    </div>
                </div>

                <!-- Módulo Donaciones -->
                <div class="module-card" onclick="#">
                    <div class="module-content">
                        <div class="module-icon">
                            <img src="images/Donaciones.png" alt="Donaciones" class="icon-img">
                        </div>
                        <h3>Donaciones</h3>
                    </div>
                </div>

                <!-- Módulo Eventos -->
                <div class="module-card" onclick="#">
                    <div class="module-content">
                        <div class="module-icon">
                            <img src="images/Eventos.png" alt="Eventos" class="icon-img">
                        </div>
                        <h3>Eventos</h3>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
