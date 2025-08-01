<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Beneficiarios</title>
    <link rel="stylesheet" href="{{ asset('css/admin-menu.css') }}">
</head>
<body>
    <div class="admin-container">
        <!-- Header con botón de regreso -->
        <div class="admin-header">
            <button class="back-btn" onclick="window.history.back()">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <!-- Título principal -->
        <div class="main-content">
            <h1 class="page-title">Administración de Beneficiarios</h1>
            
            <!-- Menú de opciones -->
            <div class="menu-grid">
                <!-- Agregar Beneficiario -->
                <div class="menu-card" onclick="window.location.href='{{ route('agregarBeneficiario') }}'">
                    <div class="card-icon">
                        <img src="{{ asset('images/agregar.png') }}" alt="Agregar">
                    </div>
                    <h3 class="card-title">Agregar</h3>
                </div>

                <!-- Actualizar Beneficiario -->
                <div class="menu-card" onclick="window.location.href='{{ route('actualizarBeneficiario') }}'">
                    <div class="card-icon">
                        <img src="{{ asset('images/actualizar.png') }}" alt="Actualizar">
                    </div>
                    <h3 class="card-title">Actualizar</h3>
                </div>

                <!-- Eliminar Beneficiario -->
                <div class="menu-card" onclick="window.location.href='{{ route('eliminarBeneficiario') }}'">
                    <div class="card-icon">
                        <img src="{{ asset('images/eliminar.png') }}" alt="Eliminar">
                    </div>
                    <h3 class="card-title">Eliminar</h3>
                </div>
            </div>
        </div>
    </div>
</body>
</html>