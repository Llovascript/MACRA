<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Donantes</title>
    <link rel="stylesheet" href="{{ asset('css/crudUsuarios.css') }}">
</head>
<body>
    <div class="admin-container">
        <!-- Header con botón de regreso -->
        <div class="admin-header">
            <button class="back-btn" onclick="window.location.href='{{ route('admin.menu') }}'">
                <svg class="back-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <!-- Título principal -->
        <div class="main-content">
            <h1 class="page-title">Administración de Donantes</h1>
            
            <!-- Menú de opciones -->
            <div class="menu-grid">
                <!-- Agregar Donante -->
                <div class="menu-card" onclick="window.location.href='{{ route('agregarDonante') }}'">
                    <div class="card-icon agregar">
                        <img src="{{ asset('images/agregar.png') }}" alt="Agregar" class="icon-image">
                    </div>
                    <h3 class="card-title">Agregar</h3>
                </div>

                <!-- Actualizar Donante -->
                <div class="menu-card" onclick="window.location.href='{{ route('actualizarDonante') }}'">
                    <div class="card-icon actualizar">
                        <img src="{{ asset('images/actualizar.png') }}" alt="Actualizar" class="icon-image">
                    </div>
                    <h3 class="card-title">Actualizar</h3>
                </div>

                <!-- Eliminar Donante -->
                <div class="menu-card" onclick="window.location.href='{{ route('eliminarDonante') }}'">
                    <div class="card-icon eliminar">
                        <img src="{{ asset('images/eliminar.png') }}" alt="Eliminar" class="icon-image">
                    </div>
                    <h3 class="card-title">Eliminar</h3>
                </div>
            </div>
        </div>
    </div>
</body>
</html>