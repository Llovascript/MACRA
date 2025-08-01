<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="{{ asset('css/menuAdmin.css') }}">
    <style>
        /* Estilos básicos para el menú admin */
        .admin-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .admin-header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        .admin-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .admin-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .admin-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .card-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }
        
        .beneficiarios-icon {
            background: linear-gradient(135deg, #11998e, #38ef7d);
        }
        
        .donantes-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        .estadisticas-icon {
            background: linear-gradient(135deg, #ff9a9e, #fecfef);
        }
        
        .configuracion-icon {
            background: linear-gradient(135deg, #a8edea, #fed6e3);
        }
        
        .card-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }
        
        .card-description {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .card-button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .card-button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .stat-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }
        
        .loading {
            color: #999;
            font-style: italic;
        }
        
        /* Iconos usando caracteres Unicode */
        .beneficiarios-icon::before {
            content: "👥";
            font-size: 2.5rem;
        }
        
        .donantes-icon::before {
            content: "🤝";
            font-size: 2.5rem;
        }
        
        .estadisticas-icon::before {
            content: "📊";
            font-size: 2.5rem;
        }
        
        .configuracion-icon::before {
            content: "⚙️";
            font-size: 2.5rem;
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <h1>Panel de Administración</h1>
            <p>Gestiona usuarios, donantes y beneficiarios del sistema</p>
        </div>

        <!-- Grid de opciones -->
        <div class="admin-grid">
            <!-- Card de Beneficiarios -->
            <div class="admin-card" onclick="window.location.href='{{ route('adminBeneficiarios') }}'">
                <div class="card-icon beneficiarios-icon"></div>
                <h3 class="card-title">Beneficiarios</h3>
                <p class="card-description">
                    Gestiona los beneficiarios del sistema. Agregar, actualizar o eliminar perfiles de beneficiarios.
                </p>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number" id="totalBeneficiarios">-</span>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="beneficiariosActivos">-</span>
                        <div class="stat-label">Activos</div>
                    </div>
                </div>
                <br>
                <button class="card-button">Gestionar Beneficiarios</button>
            </div>

            <!-- Card de Donantes -->
            <div class="admin-card" onclick="window.location.href='{{ route('adminDonante') }}'">
                <div class="card-icon donantes-icon"></div>
                <h3 class="card-title">Donantes</h3>
                <p class="card-description">
                    Administra los donantes del sistema. Agregar, actualizar o eliminar perfiles de donantes.
                </p>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number" id="totalDonantes">-</span>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="donantesActivos">-</span>
                        <div class="stat-label">Activos</div>
                    </div>
                </div>
                <br>
                <button class="card-button">Gestionar Donantes</button>
            </div>

            <!-- Card de Estadísticas -->
            <div class="admin-card" onclick="window.location.href='{{ route('adminSolicitudes') }}'">
                <div class="card-icon estadisticas-icon"></div>
                <h3 class="card-title">Solicitudes</h3>
                <p class="card-description">
                    Revisa las solicitudes pendientes de aprobación y gestiona el estatus de los usuarios.
                </p>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number" id="totalUsuarios">-</span>
                        <div class="stat-label">Usuarios</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="usuariosPendientes">-</span>
                        <div class="stat-label">Pendientes</div>
                    </div>
                </div>
                <br>
                <button class="card-button">Ver Solicitudes</button>
            </div>

            <!-- Card de Configuración -->
            <div class="admin-card">
                <div class="card-icon configuracion-icon"></div>
                <h3 class="card-title">Configuración</h3>
                <p class="card-description">
                    Ajustes del sistema, respaldos y configuración general de la plataforma.
                </p>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number">✓</span>
                        <div class="stat-label">Sistema</div>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">🔧</span>
                        <div class="stat-label">Herramientas</div>
                    </div>
                </div>
                <br>
                <button class="card-button" onclick="alert('Función en desarrollo')">Configurar Sistema</button>
            </div>
        </div>
    </div>

    <script>
        // Cargar estadísticas al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarEstadisticas();
        });

        async function cargarEstadisticas() {
            try {
                const response = await fetch('{{ route('admin.estadisticas') }}');
                
                if (response.ok) {
                    const data = await response.json();
                    
                    // Actualizar los números en las cards
                    document.getElementById('totalBeneficiarios').textContent = data.total_beneficiarios || 0;
                    document.getElementById('totalDonantes').textContent = data.total_donantes || 0;
                    document.getElementById('totalUsuarios').textContent = data.total_usuarios || 0;
                    document.getElementById('usuariosPendientes').textContent = data.usuarios_pendientes || 0;
                    
                    // Para beneficiarios y donantes activos, usamos los usuarios activos
                    const beneficiariosActivos = Math.floor(data.usuarios_activos * (data.total_beneficiarios / (data.total_usuarios || 1)));
                    const donantesActivos = Math.floor(data.usuarios_activos * (data.total_donantes / (data.total_usuarios || 1)));
                    
                    document.getElementById('beneficiariosActivos').textContent = beneficiariosActivos;
                    document.getElementById('donantesActivos').textContent = donantesActivos;
                    
                } else {
                    // En caso de error, mostrar 0
                    console.error('Error al cargar estadísticas');
                    mostrarEstadisticasError();
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarEstadisticasError();
            }
        }

        function mostrarEstadisticasError() {
            const elementos = ['totalBeneficiarios', 'totalDonantes', 'totalUsuarios', 'usuariosPendientes', 'beneficiariosActivos', 'donantesActivos'];
            elementos.forEach(id => {
                const elemento = document.getElementById(id);
                if (elemento) {
                    elemento.textContent = '0';
                }
            });
        }

        // Prevenir que los clics en los botones disparen el onclick del card
        document.querySelectorAll('.card-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
</body>
</html>