<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(-45deg, #4481eb 0%, #04befe 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .logout-btn {
            background: linear-gradient(-45deg, #ff4757, #ff3742);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .logout-btn:hover {
            transform: translateY(-2px);
        }
        .welcome-message {
            color: #333;
            font-size: 1.2rem;
            font-weight: 500;
        }
        .user-info {
            background: linear-gradient(-45deg, #4481eb, #04befe);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .success-message {
            background: #e8ffe8;
            color: #2ed573;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #b3ffb3;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .dashboard-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #4481eb;
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        .card-content {
            color: #666;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #333; margin: 0;">🎉 Dashboard</h1>
            <div style="display: flex; align-items: center; gap: 20px;">
                <span class="welcome-message">¡Bienvenido!</span>
                <button class="logout-btn" onclick="logout()">Cerrar Sesión</button>
            </div>
        </div>
        
        <div class="success-message">
            ✅ ¡Has iniciado sesión exitosamente!
        </div>

        @if(Session::has('user'))
        <div class="user-info">
            <h3 style="margin-top: 0;">👤 Información del Usuario</h3>
            <p><strong>Nombre:</strong> {{ Session::get('user')['nombre'] ?? 'No disponible' }}</p>
            <p><strong>Email:</strong> {{ Session::get('user')['correo'] ?? 'No disponible' }}</p>
            <p><strong>ID:</strong> {{ Session::get('user')['id'] ?? 'No disponible' }}</p>
        </div>
        @endif
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-title">🔐 Autenticación</div>
                <div class="card-content">
                    Sistema de autenticación funcionando correctamente con FastAPI + Laravel.
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-title">🚀 Estado del Sistema</div>
                <div class="card-content">
                    Conexión establecida entre frontend (Laravel) y backend (FastAPI).
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-title">📊 Sesión Activa</div>
                <div class="card-content">
                    Tu sesión está activa y protegida con JWT tokens.
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-title">⚙️ Configuración</div>
                <div class="card-content">
                    Panel de control listo para agregar más funcionalidades.
                </div>
            </div>
        </div>

        <div style="margin-top: 40px; text-align: center; color: #666;">
            <p>🎯 <strong>¡Sistema funcionando correctamente!</strong></p>
            <p>Puedes comenzar a agregar más funcionalidades a tu dashboard.</p>
        </div>
    </div>

    <script>
        // Verificar token al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Dashboard cargado correctamente');
            
            // Verificar si hay información de sesión
            @if(Session::has('access_token'))
                console.log('Token de acceso presente en la sesión');
            @else
                console.log('No hay token de acceso');
                // Si no hay token, redirigir después de un momento
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            @endif
        });

        function logout() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                // Hacer petición de logout
                fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                }).then(() => {
                    // Limpiar localStorage también
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('token_type');
                    
                    // Redirigir al login
                    window.location.href = '/login';
                }).catch(() => {
                    // En caso de error, redirigir de todas formas
                    window.location.href = '/login';
                });
            }
        }

        // Prevenir que el usuario regrese con el botón atrás después del logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>
