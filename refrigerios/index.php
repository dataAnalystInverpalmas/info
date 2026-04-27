<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Refrigerios - Gestión</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .module-card {
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }
        
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .module-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .module-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            padding: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🍽️ Módulo de Refrigerios y Comidas</h1>
            <p>Sistema de gestión</p>
        </div>

        <!-- Módulos disponibles -->
        <div class="modules-grid">
            <!-- Áreas -->
            <a href="areas/" class="module-link">
                <div class="card module-card">
                    <div class="card-body text-center">
                        <div class="module-icon" style="color: #667eea;">
                            <i class="fas fa-map"></i>
                        </div>
                        <h5 class="card-title">Áreas</h5>
                        <p class="card-text text-muted">Gestionar áreas y fincas</p>
                        <small class="text-secondary">📍 Zonas de trabajo</small>
                    </div>
                </div>
            </a>

            <!-- Secciones -->
            <a href="secciones/" class="module-link">
                <div class="card module-card">
                    <div class="card-body text-center">
                        <div class="module-icon" style="color: #764ba2;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h5 class="card-title">Secciones</h5>
                        <p class="card-text text-muted">Gestionar secciones por área</p>
                        <small class="text-secondary">🏗️ Divisiones de trabajo</small>
                    </div>
                </div>
            </a>

            <!-- Proveedores -->
            <a href="proveedores/" class="module-link">
                <div class="card module-card">
                    <div class="card-body text-center">
                        <div class="module-icon" style="color: #f093fb;">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h5 class="card-title">Proveedores</h5>
                        <p class="card-text text-muted">Gestionar proveedores</p>
                        <small class="text-secondary">🏢 Suministradores</small>
                    </div>
                </div>
            </a>

            <!-- Refrigerios -->
            <a href="refrigerios/" class="module-link">
                <div class="card module-card">
                    <div class="card-body text-center">
                        <div class="module-icon" style="color: #4facfe;">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h5 class="card-title">Refrigerios</h5>
                        <p class="card-text text-muted">Gestionar refrigerios y comidas</p>
                        <small class="text-secondary">🥗 Productos y servicios</small>
                    </div>
                </div>
            </a>

            <!-- Jornadas -->
            <a href="jornadas/" class="module-link">
                <div class="card module-card">
                    <div class="card-body text-center">
                        <div class="module-icon" style="color: #fa709a;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h5 class="card-title">Jornadas</h5>
                        <p class="card-text text-muted">Gestionar jornadas de trabajo</p>
                        <small class="text-secondary">⏰ Turnos disponibles</small>
                    </div>
                </div>
            </a>

            <!-- Valores -->
            <a href="valores/" class="module-link">
                <div class="card module-card">
                    <div class="card-body text-center">
                        <div class="module-icon" style="color: #30cfd0;">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h5 class="card-title">Valores</h5>
                        <p class="card-text text-muted">Gestionar tarifas y valores</p>
                        <small class="text-secondary">💰 Precios por servicio</small>
                    </div>
                </div>
            </a>

            <!-- Hechos -->
            <a href="hechos/" class="module-link">
                <div class="card module-card">
                    <div class="card-body text-center">
                        <div class="module-icon" style="color: #a8edea;">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h5 class="card-title">Registros</h5>
                        <p class="card-text text-muted">Registrar consumos y servicios</p>
                        <small class="text-secondary">📊 Hechos/Transacciones</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
