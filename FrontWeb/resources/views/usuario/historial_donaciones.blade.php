<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Donaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h1 class="mb-4">Historial de Donaciones</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(count($donaciones) > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Artículo</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donaciones as $donacion)
                    <tr>
                        <td>{{ $donacion['id'] }}</td>
                        <td>{{ $donacion['articuloP_id'] ?? 'N/A' }}</td>
                        <td>{{ $donacion['cantidad'] }}</td>
                        <td>{{ $donacion['fecha'] ?? 'No registrada' }}</td>
                        <td>{{ $donacion['estatus_id'] ?? 'Sin estatus' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No tienes donaciones registradas.</p>
    @endif
</body>
</html>