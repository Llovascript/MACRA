<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Evento | Administración</title>
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
            padding: 1.2rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-title {
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .form-container {
            max-width: 700px;
            margin: 2rem auto;
            background: var(--light);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 2rem;
        }
        
        .form-title {
            color: var(--primary);
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }
        
        .form-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #495057;
        }
        
        .form-control {
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 1.2rem;
            border: 1px solid #ced4da;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(124, 46, 0, 0.25);
        }
        
        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-submit:hover {
            background: #6a2800;
            transform: translateY(-2px);
        }
        
        .btn-return {
            background: var(--dark);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.5rem;
            text-decoration: none;
        }
        
        .btn-return:hover {
            background: #333333;
            color: white;
            transform: translateY(-2px);
        }
        
        .alert {
            border-radius: 6px;
            border: none;
        }
        
        .admin-footer {
            background: var(--dark);
            color: white;
            padding: 1rem 0;
            font-size: 0.85rem;
            margin-top: 2rem;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
        }
        
        .button-group .btn-submit {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <div class="text-center">
                <h1 class="admin-title">Crear Nuevo Evento</h1>
            </div>
        </div>
    </header>

    <main class="container">
        <a href="{{ route('admin.eventos.menu') }}" class="btn btn-return">
    <i class="fas fa-arrow-left"></i> Regresar al Menú
</a>
        
        <div class="form-container">
            <h2 class="form-title">Información del Evento</h2>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error!</strong> Por favor corrige los siguientes errores:
                    <ul class="mt-2 mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.eventos.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del evento:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fechaIn" class="form-label">Fecha de inicio:</label>
                        <input type="date" class="form-control" id="fechaIn" name="fechaIn" value="{{ old('fechaIn') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fechaTer" class="form-label">Fecha de término:</label>
                        <input type="date" class="form-control" id="fechaTer" name="fechaTer" value="{{ old('fechaTer') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required>{{ old('descripcion') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="estatus_id" class="form-label">Estatus:</label>
                    <select class="form-select" id="estatus_id" name="estatus_id" required>
                        <option value="">Selecciona un estatus</option>
                        <option value="1" {{ old('estatus_id') == 1 ? 'selected' : '' }}>Activo</option>
                        <option value="2" {{ old('estatus_id') == 2 ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-calendar-plus me-2"></i> Crear Evento
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="admin-footer text-center">
        &copy; {{ date('Y') }} Sistema de Gestión de Eventos
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>