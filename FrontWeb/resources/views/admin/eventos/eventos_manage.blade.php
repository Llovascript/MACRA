<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Eventos | Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #7C2E00;
            --dark: #000000;
            --light: #FFFFFF;
        }

        body {
            background-color: #F8F9FA;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        .admin-header {
            background: var(--primary);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            color: var(--primary);
            font-weight: 600;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 10px;
        }

        .page-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--primary);
        }

        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--dark);
            color: white;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-logout {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #6a2800;
            border-color: #6a2800;
        }

        .btn-secondary {
            background-color: var(--dark);
            border-color: var(--dark);
        }

        .btn-secondary:hover {
            background-color: #333333;
            border-color: #333333;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.875rem;
        }

        .form-control {
            border-radius: 4px;
            padding: 6px 12px;
            font-size: 0.875rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(124, 46, 0, 0.25);
        }

        .alert {
            border-radius: 6px;
            border: none;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <div class="container position-relative">
            <h1 class="text-center mb-0">Administración de Eventos</h1>
            {{-- <form method="POST" action="{{ route('logout') }}" class="position-absolute top-0 end-0">
                @csrf
                <button type="submit" class="btn btn-logout btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Cerrar sesión
                </button>
            </form> --}}
        </div>
    </header>

    <main class="container">
        <div class="table-container">
            <h2 class="page-title">Gestión de Eventos</h2>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Término</th>
                            <th>Descripción</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($eventos as $evento)
                            <tr>
                                <form method="POST" action="{{ route('admin.eventos.update', $evento['id']) }}">
                                    @csrf
                                    <td class="text-center">{{ $evento['id'] }}</td>
                                    <td><input type="text" name="nombre" class="form-control"
                                            value="{{ $evento['nombre'] }}" required></td>
                                    <td><input type="date" name="fechaIn" class="form-control"
                                            value="{{ $evento['fechaIn'] }}" required></td>
                                    <td><input type="date" name="fechaTer" class="form-control"
                                            value="{{ $evento['fechaTer'] }}" required></td>
                                    <td><input type="text" name="descripcion" class="form-control"
                                            value="{{ $evento['descripcion'] }}" required></td>
                                    <td>
                                        <select name="estatus_id" class="form-select" required>
                                            <option value="1" {{ $evento['estatus_id'] == 1 ? 'selected' : '' }}>
                                                Activo</option>
                                            <option value="2" {{ $evento['estatus_id'] == 2 ? 'selected' : '' }}>
                                                Inactivo</option>
                                        </select>
                                    </td>
                                    <td class="action-buttons">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-save me-1"></i> Guardar
                                        </button>
                                </form>
                                <form method="POST" action="{{ route('admin.eventos.destroy', $evento['id']) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('¿Estás seguro de eliminar este evento?')"
                                        class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt me-1"></i> Eliminar
                                    </button>
                                </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('admin.eventos.menu') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver al menú
        </a>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirmación antes de eliminar
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('¿Estás seguro de eliminar este evento?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>

</html>
