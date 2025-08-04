<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Panel de Eventos | Administración</title>
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
            max-width: 500px;
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

        .btn-add {
            background: var(--primary);
            color: white;
        }

        .btn-add:hover {
            background: #6a2800;
            transform: translateY(-2px);
        }

        .btn-manage {
            background: var(--dark);
            color: white;
        }

        .btn-manage:hover {
            background: #333333;
            transform: translateY(-2px);
        }

        .btn-capacity {
            background: #28527a; /* un azul para diferenciar */
            color: white;
        }

        .btn-capacity:hover {
            background: #1f415a;
            transform: translateY(-2px);
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
            text-align: center;
            font-weight: 600;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary);
        }

        .alert {
            border-radius: 6px;
            border: none;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <div class="text-center">
                <h1 class="admin-title">Administración de Eventos</h1>
            </div>
        </div>
    </header>

    <main class="main-container">
        <div class="container">
            <div class="card-panel">
                <h2 class="section-title">Gestión de Eventos</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error!</strong> Por favor verifica:
                        <ul class="mt-2 mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-grid mt-4">
                    <a href="{{ route('admin.eventos.create') }}" class="action-btn btn-add">
                        <i class="fas fa-plus"></i> Agregar Evento
                    </a>
                    <a href="{{ route('admin.eventos.manage') }}" class="action-btn btn-manage">
                        <i class="fas fa-edit"></i> Administrar Eventos
                    </a>
                    <a href="{{ route('admin.eventos.capacidad') }}" class="action-btn btn-capacity">
                        <i class="fas fa-users"></i> Capacidad de Eventos
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
