<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Disponibles</title>
    <style>
        :root {
            --primary: #7C2E00;
            --dark: #000000;
            --light: #FFFFFF;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        .admin-header {
            background: var(--primary);
            color: white;
            padding: 1.2rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-2px);
        }

        .back-icon {
            width: 20px;
            height: 20px;
        }

        .page-title {
            font-weight: 600;
            letter-spacing: 0.5px;
            margin: 0;
            flex: 1;
            text-align: center;
            margin-right: 47px;
            /* Compensar el ancho del botón para centrar */
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            color: var(--primary);
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 10px;
        }

        h1:after {
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
            padding: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: var(--dark);
            color: white;
            font-weight: 500;
            padding: 12px 15px;
            text-align: left;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #6a2800;
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-disabled {
            background-color: #6c757d;
            color: white;
            cursor: not-allowed;
            opacity: 0.65;
        }

        .btn-disabled:hover {
            background-color: #6c757d;
            transform: none;
        }

        .status-joined {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            color: #28a745;
        }

        .status-icon {
            width: 16px;
            height: 16px;
        }

        .alert {
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid transparent;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .text-center {
            text-align: center;
        }

        .empty-message {
            color: #6c757d;
            font-style: italic;
            padding: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .header-content {
                padding: 0 16px;
            }

            .page-title {
                font-size: 18px;
                margin-right: 35px;
            }

            .table-container {
                padding: 10px;
            }

            th,
            td {
                padding: 8px 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Header con botón de regreso -->
    <header class="admin-header">
        <div class="header-content">
            <button class="back-btn" onclick="window.location.href='{{ route('beneficiario.menu') }}'">
                <svg class="back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </button>
        </div>
    </header>

    <div class="container">
        <h1>Eventos Disponibles</h1>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventos as $evento)
                        <tr>
                            <td>{{ $evento['nombre'] }}</td>
                            <td>{{ $evento['descripcion'] }}</td>
                            <td>{{ date('d/m/Y', strtotime($evento['fechaIn'])) }}</td>
                            <td>{{ date('d/m/Y', strtotime($evento['fechaTer'])) }}</td>
                            <td>
                                @if (isset($evento['usuario_unido']) && $evento['usuario_unido'])
                                    {{-- Usuario ya está unido al evento --}}
                                    <div class="status-joined">
                                        <svg class="status-icon" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Ya inscrito
                                    </div>
                                @elseif(isset($evento['evento_lleno']) && $evento['evento_lleno'])
                                    {{-- Evento lleno --}}
                                    <button class="btn btn-disabled btn-sm" type="button" disabled>
                                        Lleno
                                    </button>
                                @else
                                    {{-- Usuario puede unirse --}}
                                    <form action="{{ route('eventos.unirse', $evento['id']) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-primary btn-sm" type="submit">Unirse</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-message">No hay eventos disponibles</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
