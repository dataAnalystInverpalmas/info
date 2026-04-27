<?php
/**
 * Script de Instalación del Módulo Proyectos-Tareas-Bitácora
 * 
 * Ejecutar desde terminal:
 * php scripts/instalar_modulo.php
 * 
 * Este script verifica:
 * - Conexión a MySQL
 * - Tablas requeridas
 * - Permisos
 * - Estructura de directorios
 */

echo "\n";
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║       Verificador de Instalación - Proyectos y Tareas        ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

$errores = [];
$advertencias = [];
$exito = [];

// 1. Verificar conexión a BD
echo "[1/6] Verificando conexión a MySQL...\n";
try {
    require_once dirname(__DIR__) . '/bd/conexion.php';
    $conexion = \Conexion::Conectar();
    $exito[] = "✓ Conexión a MySQL exitosa";
} catch (Exception $e) {
    $errores[] = "✗ Error en conexión: " . $e->getMessage();
}

// 2. Verificar tablas
if (isset($conexion)) {
    echo "[2/6] Verificando tablas...\n";
    
    $tablas_requeridas = ['proyectos', 'tareas', 'bitacora'];
    
    foreach ($tablas_requeridas as $tabla) {
        $stmt = $conexion->query("SHOW TABLES LIKE '$tabla'");
        if ($stmt && $stmt->rowCount() > 0) {
            $exito[] = "✓ Tabla '$tabla' existe";
        } else {
            $errores[] = "✗ Tabla '$tabla' no encontrada";
        }
    }
} else {
    $errores[] = "✗ No se puede verificar tablas sin conexión";
}

// 3. Verificar estructura de directorios
echo "[3/6] Verificando estructura de directorios...\n";

$directorios = [
    'src/Models' => file_exists('src/Models'),
    'src/Controllers' => file_exists('src/Controllers'),
    'ajax' => file_exists('ajax'),
    'views/Proyectos' => file_exists('views/Proyectos'),
    'views/Tareas' => file_exists('views/Tareas'),
    'docs' => file_exists('docs'),
];

foreach ($directorios as $dir => $existe) {
    if ($existe) {
        $exito[] = "✓ Directorio '$dir' existe";
    } else {
        $advertencias[] = "⚠ Directorio '$dir' no existe";
    }
}

// 4. Verificar archivos críticos
echo "[4/6] Verificando archivos críticos...\n";

$archivos = [
    'src/Models/Proyecto.php',
    'src/Models/Tarea.php',
    'src/Models/Bitacora.php',
    'src/Controllers/ProyectoController.php',
    'src/Controllers/TareaController.php',
    'src/Controllers/BitacoraController.php',
    'ajax/proyectos.php',
    'ajax/tareas.php',
    'ajax/bitacora.php',
];

foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        $exito[] = "✓ Archivo '$archivo' existe";
    } else {
        $errores[] = "✗ Archivo '$archivo' no encontrado";
    }
}

// 5. Verificar permisos de escritura
echo "[5/6] Verificando permisos de escritura...\n";

$rutas_escritura = [
    'logs' => 'logs',
];

foreach ($rutas_escritura as $dir => $ruta) {
    if (is_writable($ruta) || !file_exists($ruta)) {
        $exito[] = "✓ Directorio '$ruta' tiene permisos de escritura";
    } else {
        $advertencias[] = "⚠ Directorio '$ruta' podría no tener permisos de escritura";
    }
}

// 6. Verificar índices en tablas
if (isset($conexion)) {
    echo "[6/6] Verificando índices de optimización...\n";
    
    try {
        $indexes = $conexion->query("
            SELECT INDEX_NAME 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = 'informes' 
            AND TABLE_NAME IN ('proyectos', 'tareas', 'bitacora')
        ");
        
        if ($indexes && $indexes->rowCount() > 0) {
            $exito[] = "✓ Índices configurados correctamente";
        } else {
            $advertencias[] = "⚠ Revisar índices de optimización";
        }
    } catch (Exception $e) {
        $advertencias[] = "⚠ No se pudieron verificar índices: " . $e->getMessage();
    }
}

// Mostrar resultados
echo "\n";
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                        RESULTADOS                             ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

if (!empty($exito)) {
    echo "✅ ÉXITO:\n";
    foreach ($exito as $msg) {
        echo "   $msg\n";
    }
    echo "\n";
}

if (!empty($advertencias)) {
    echo "⚠️  ADVERTENCIAS:\n";
    foreach ($advertencias as $msg) {
        echo "   $msg\n";
    }
    echo "\n";
}

if (!empty($errores)) {
    echo "❌ ERRORES:\n";
    foreach ($errores as $msg) {
        echo "   $msg\n";
    }
    echo "\n";
}

// Resumen final
echo "╔═══════════════════════════════════════════════════════════════╗\n";
if (empty($errores)) {
    echo "║  ✅ INSTALACIÓN COMPLETADA SIN ERRORES                    ║\n";
} else {
    echo "║  ❌ HAY ERRORES QUE RESOLVER ANTES DE USAR                ║\n";
}
echo "╚═══════════════════════════════════════════════════════════════╝\n";

// Estadísticas
echo "\nESTADÍSTICAS:\n";
echo "  ✅ Exitosos:     " . count($exito) . "\n";
echo "  ⚠️  Advertencias: " . count($advertencias) . "\n";
echo "  ❌ Errores:       " . count($errores) . "\n";

// Próximos pasos
if (empty($errores)) {
    echo "\n📋 PRÓXIMOS PASOS:\n";
    echo "  1. Ejecutar instancia de prueba:\n";
    echo "     php -S 0.0.0.0:9258 -t .\n\n";
    echo "  2. Acceder a API Tester:\n";
    echo "     http://localhost:9258/api_tester.html\n\n";
    echo "  3. Acceder a vistas:\n";
    echo "     http://localhost:9258/views/Proyectos/index.php\n";
    echo "     http://localhost:9258/views/Tareas/index.php\n";
} else {
    echo "\n⚠️  Resuelve los errores anteriores y vuelve a ejecutar este script.\n";
}

echo "\n";

exit($errores ? 1 : 0);
