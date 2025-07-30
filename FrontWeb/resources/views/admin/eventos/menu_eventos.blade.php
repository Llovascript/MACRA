<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú de Gestión de Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
        <a class="navbar-brand" href="#">Administrador</a>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Menú de Gestión de Eventos</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-grid gap-3 col-6 mx-auto">
            <a href="{{ route('admin.eventos.create') }}" class="btn btn-success">➕ Agregar Evento</a>
            <a href="#" class="btn btn-warning disabled">✏️ Actualizar Evento (Próximamente)</a>
            <a href="#" class="btn btn-danger disabled">🗑️ Eliminar Evento (Próximamente)</a>
        </div>
    </div>

    <footer class="text-center mt-5">
        <p>&copy; {{ date('Y') }} Panel de administración</p>
    </footer>
</body>
</html>
