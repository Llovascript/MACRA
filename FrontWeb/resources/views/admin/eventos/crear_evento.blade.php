<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Evento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        label {
            display: block;
            margin-top: 1rem;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            max-width: 400px;
            padding: 0.5rem;
            margin-top: 0.25rem;
            box-sizing: border-box;
        }
        button {
            margin-top: 1.5rem;
            padding: 0.7rem 1.5rem;
            background-color: #2d89ef;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            border-radius: 4px;
        }
        button:hover {
            background-color: #1b5dbf;
        }
        .success {
            color: green;
            margin-bottom: 1rem;
        }
        .errors {
            color: red;
            margin-bottom: 1rem;
        }
        ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <h1>Crear Evento</h1>

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.eventos.store') }}">
        @csrf

        <label for="nombre">Nombre evento:</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>

        <label for="fechaIn">Fecha inicio:</label>
        <input type="date" id="fechaIn" name="fechaIn" value="{{ old('fechaIn') }}" required>

        <label for="fechaTer">Fecha término:</label>
        <input type="date" id="fechaTer" name="fechaTer" value="{{ old('fechaTer') }}" required>

        <label for="descripcion">Descripción:</label>
        <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion') }}" required>

        <label for="estatus_id">Estatus:</label>
        <select id="estatus_id" name="estatus_id" required>
            <option value="">Selecciona estatus</option>
            <option value="1" {{ old('estatus_id') == 1 ? 'selected' : '' }}>Activo</option>
            <option value="2" {{ old('estatus_id') == 2 ? 'selected' : '' }}>Inactivo</option>
        </select>

        <button type="submit">Crear evento</button>
    </form>
</body>
</html>
