<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Capacidad de Eventos | Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --primary: #7C2E00;
            --dark: #000000;
            --light: #FFFFFF;
        }

        body {
            background-color: #F8F9FA;
            font-family: 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            background: var(--primary);
            color: white;
            padding: 1.2rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .admin-title {
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 0.3rem;
        }

        .admin-url {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.8);
            font-family: monospace;
        }

        .main-container {
            flex: 1;
            padding: 2.5rem 0;
        }

        .card-panel {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            background: white;
            padding: 2rem;
            margin: 0 auto;
        }

        .action-btn {
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            margin-bottom: 15px;
            border: none;
            text-decoration: none;
        }

        .btn-secondary {
            background: var(--dark);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
        }

        .btn-secondary:hover {
            background: #333333;
            transform: translateY(-2px);
            color: white;
        }

        .admin-footer {
            background: var(--dark);
            color: white;
            padding: 1rem 0;
            font-size: 0.85rem;
        }

        .section-title {
            color: var(--primary);
            position: relative;
            padding-bottom: 12px;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--primary);
        }

        .alert {
            border-radius: 6px;
            border: none;
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .table thead th {
            background-color: var(--primary);
            color: white;
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: rgba(124, 46, 0, 0.05);
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <div class="text-center">
                <h1 class="admin-title">Capacidad de Eventos</h1>
            </div>
        </div>
    </header>

    <main class="main-container">
        <div class="container">
            <div class="card-panel">
                <h2 class="section-title">Capacidad por Evento</h2>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Evento</th>
                                <th>Beneficiarios</th>
                                <th>Donantes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($capacidades as $capacidad)
                                <tr>
                                    <td>{{ $capacidad['nombre_evento'] }}</td>
                                    <td>{{ $capacidad['beneficiarios'] }}</td>
                                    <td>{{ $capacidad['donantes'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay eventos disponibles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.eventos.menu') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al menú
                    </a>
                </div>
            </div>
        </div>
    </main>

    <footer class="admin-footer text-center">
        &copy; {{ date('Y') }} Sistema de Gestión de Eventos
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>