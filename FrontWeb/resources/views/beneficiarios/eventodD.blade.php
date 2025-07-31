<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Eventos | Panel</title>
    <style>
        :root {
            --primary: #7C2E00;
            --dark: #000000;
            --light: #FFFFFF;
        }
        
        body {
            background-color: #F8F9FA;
            font-family: 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        
        .admin-header {
            background: var(--primary);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .container {
            width: 95%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .text-center {
            text-align: center;
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        
        th {
            background-color: var(--dark);
            color: white;
            font-weight: 500;
            text-align: center;
        }
        
        input, select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(124, 46, 0, 0.2);
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #6a2800;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-secondary {
            background-color: var(--dark);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #333;
        }
        
        .btn-logout {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: white;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.5);
        }
        
        .btn-logout:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .mt-3 {
            margin-top: 1rem;
        }
        
        .d-flex {
            display: flex;
        }
        
        .gap-2 {
            gap: 8px;
        }
        
        .justify-content-center {
            justify-content: center;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1 class="text-center mb-0">Administración de Eventos</h1>
            <button class="btn btn-logout btn-sm">Cerrar sesión</button>
        </div>
    </header>

    <main class="container">
        <div class="table-container">
            <h2 class="page-title">Gestión de Eventos</h2>
            
            <!-- Ejemplo de alerta de éxito (puedes eliminar esto en producción) -->
            <div class="alert alert-success">
                Evento actualizado correctamente
            </div>
            
            <!-- Ejemplo de alerta de error (puedes eliminar esto en producción) -->
            <div class="alert alert-danger">
                Error al eliminar el evento
            </div>

            <table>
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
                    <!-- Ejemplo de fila de evento 1 -->
                    <tr>
                        <td class="text-center">1</td>
                        <td><input type="text" value="Conferencia de Tecnología" class="form-control"></td>
                        <td><input type="date" value="2023-12-15" class="form-control"></td>
                        <td><input type="date" value="2023-12-16" class="form-control"></td>
                        <td><input type="text" value="Evento anual de tecnología" class="form-control"></td>
                        <td>
                            <select class="form-control">
                                <option value="1" selected>Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                        </td>
                        <td>
                            <div class="action-buttons justify-content-center">
                                <button class="btn btn-primary btn-sm">Guardar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Ejemplo de fila de evento 2 -->
                    <tr>
                        <td class="text-center">2</td>
                        <td><input type="text" value="Taller de Marketing" class="form-control"></td>
                        <td><input type="date" value="2024-01-20" class="form-control"></td>
                        <td><input type="date" value="2024-01-21" class="form-control"></td>
                        <td><input type="text" value="Taller práctico de marketing digital" class="form-control"></td>
                        <td>
                            <select class="form-control">
                                <option value="1">Activo</option>
                                <option value="2" selected>Inactivo</option>
                            </select>
                        </td>
                        <td>
                            <div class="action-buttons justify-content-center">
                                <button class="btn btn-primary btn-sm">Guardar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <a href="#" class="btn btn-secondary mt-3">
            ← Volver al menú
        </a>
    </main>

    <script>
        // Confirmación antes de eliminar
        document.querySelectorAll('.btn-danger').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('¿Estás seguro de eliminar este evento?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>