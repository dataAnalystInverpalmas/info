<?php
// Traer conexión
require("../funciones/conexion.php");

// ============================================================================
// LIMPIEZA INICIAL
// ============================================================================
$sqlTruncate = "TRUNCATE TABLE viewsowing";
$conexion->query($sqlTruncate);

// ============================================================================
// DEFINICIÓN DE CONSULTAS SQL
// ============================================================================

// ----------------------------------------------------------------------------
// SIEMBRAS TEÓRICAS - PROGRAMF
// ----------------------------------------------------------------------------
$queryTeoSiembra = "
    INSERT INTO viewsowing 
    SELECT 
        a.fecha_siembra,
        a.finca,
        a.bloque AS bloque,
        v.producto,
        a.variedad,
        b.año,
        a.temporada_obj,
        'TEO_SIEMBRA' AS tipo,
        b.fecha_pico,
        if(a.ciclo=1,'1PICO','CONTINUA') AS tipo_siembra,
        '' AS maquilador,
        SUM(a.plantas) AS valor 
    FROM programf AS a 
    LEFT JOIN seasons AS b ON b.nombre = a.temporada_obj
    LEFT JOIN varieties AS v ON v.nombre = a.variedad
    WHERE a.plantas > 0 
        AND v.producto IN ('CLAVEL', 'MINICLAVEL') 
        AND a.estado = 1
        AND a.fecha_siembra IS NOT NULL
    GROUP BY a.fecha_siembra, a.variedad, a.temporada_obj, a.tipo, b.fecha_pico, a.finca, a.bloque
";

// ----------------------------------------------------------------------------
// ENSARTE TEÓRICO - PROGRAM
// ----------------------------------------------------------------------------
$queryTeoEnsarte = "
    INSERT INTO viewsowing 
    SELECT 
        a.fecha_ensarte,
        '' AS finca,
        '0' AS bloque,
        v.producto,
        a.variedad,
        b.año,
        a.temporada_obj,
        'TEO_ENSARTE' AS tipo,
        b.fecha_pico,
        a.tipo AS tipo_siembra,
        br.nombre AS maquilador,
        SUM(a.plantas) AS valor
    FROM program AS a
    LEFT JOIN seasons AS b ON b.nombre = a.temporada_obj
    LEFT JOIN varieties AS v ON v.nombre = a.variedad
    LEFT JOIN breeders AS br ON br.id = a.casa_id
    WHERE a.plantas > 0 
        AND v.producto IN ('CLAVEL', 'MINICLAVEL') 
        AND a.estado = 1 
        AND a.raiz = 0
        AND a.fecha_ensarte IS NOT NULL
    GROUP BY a.fecha_ensarte, a.variedad, a.temporada_obj, a.tipo, b.fecha_pico, br.nombre
";

// ----------------------------------------------------------------------------
// COSECHA TEÓRICA - PROGRAM
// ----------------------------------------------------------------------------
$queryTeoCosecha = "
    INSERT INTO viewsowing 
    SELECT 
        a.fecha_cosecha,
        '' AS finca,
        '0' AS bloque,
        v.producto,
        a.variedad,
        b.año,
        a.temporada_obj,
        'TEO_COSECHA' AS tipo,
        b.fecha_pico,
        a.tipo AS tipo_siembra,
        br.nombre AS maquilador,
        SUM(a.plantas) AS valor 
    FROM program AS a 
    LEFT JOIN seasons AS b ON b.nombre = a.temporada_obj
    LEFT JOIN varieties AS v ON v.nombre = a.variedad
    LEFT JOIN breeders AS br ON br.id = a.casa_id
    WHERE a.plantas > 0 
        AND v.producto IN ('CLAVEL', 'MINICLAVEL') 
        AND a.estado = 1 
        AND a.raiz = 0
        AND a.fecha_cosecha IS NOT NULL
    GROUP BY a.fecha_cosecha, a.variedad, a.temporada_obj, a.tipo, b.fecha_pico, br.nombre
";

// ----------------------------------------------------------------------------
// SIEMBRA REAL - HPLANE
// ----------------------------------------------------------------------------
$querySiembra = "
    INSERT INTO viewsowing 
    SELECT 
        a.fecha_siembra,
        a.finca,
        a.bloque AS bloque,
        v.producto,
        a.variedad,
        b.año,
        a.temporada,
        'SIEMBRA' AS tipo,
        b.fecha_pico,
        a.tipo_siembra AS tipo_siembra,
        maquilador,
        SUM(a.plantas) AS valor
    FROM hplane AS a 
    LEFT JOIN seasons AS b ON b.nombre = a.temporada
    LEFT JOIN varieties AS v ON v.nombre = a.variedad
    WHERE a.plantas > 0 
        AND v.producto IN ('CLAVEL', 'MINICLAVEL')
        AND a.fecha_siembra IS NOT NULL
    GROUP BY a.fecha_siembra, a.variedad, a.temporada, b.fecha_pico, a.finca, a.bloque, a.tipo_siembra
";

// ----------------------------------------------------------------------------
// ENSARTE REAL - PROGRAM
// ----------------------------------------------------------------------------
$queryEnsartes = "
    INSERT INTO viewsowing
    SELECT 
        a.fecha_ensarte AS fecha,
        'PROPAGACION' AS finca,
        '0' AS bloque,
        a.producto,
        a.variedad,
        a.programa,
        a.temporada_obj,
        'ENSARTE' AS tipo,
        a.fecha_pico,
        a.tipo AS tipo_siembra,
        br.nombre AS maquilador,
        a.esquejes_ensarte AS valor
    FROM program AS a
    LEFT JOIN breeders AS br ON br.id = a.casa_id
    WHERE a.estado = 1 
        AND a.raiz = 0
        AND a.fecha_ensarte IS NOT NULL
";

// ----------------------------------------------------------------------------
// COSECHA REAL - PROGRAM
// ----------------------------------------------------------------------------
$queryCosechas = "
    INSERT INTO viewsowing
    SELECT 
        a.fecha_cosecha AS fecha,
        'PROPAGACION' AS finca,
        '0' AS bloque,
        a.producto,
        a.variedad,
        a.programa,
        a.temporada_obj,
        'COSECHA' AS tipo,
        a.fecha_pico,
        a.tipo AS tipo_siembra,
        br.nombre AS maquilador,
        a.esquejes_cosecha AS valor
    FROM program AS a
    LEFT JOIN breeders AS br ON br.id = a.casa_id
    WHERE a.estado = 1 
        AND a.raiz = 0
        AND a.fecha_cosecha IS NOT NULL
";

// ----------------------------------------------------------------------------
// ENSARTE REAL - PROGRAM_ADD_PTO
// ----------------------------------------------------------------------------
$queryEnsartes_pto = "
    INSERT INTO viewsowing
    SELECT 
        a.fecha_ensarte AS fecha,
        'PROPAGACION' AS finca,
        '0' AS bloque,
        a.producto,
        a.variedad,
        a.programa,
        a.temporada_obj,
        'ENSARTE' AS tipo,
        a.fecha_pico,
        a.tipo AS tipo_siembra,
        br.nombre AS maquilador,
        a.esquejes_ensarte AS valor
    FROM program_add_pto AS a
    LEFT JOIN breeders AS br ON br.id = a.casa_id
    WHERE a.estado = 1 
        AND a.raiz = 0
        AND a.fecha_ensarte IS NOT NULL
";

// ----------------------------------------------------------------------------
// COSECHA REAL - PROGRAM_ADD_PTO
// ----------------------------------------------------------------------------
$queryCosechas_pto = "
    INSERT INTO viewsowing
    SELECT 
        a.fecha_cosecha_r AS fecha,
        'PROPAGACION' AS finca,
        '0' AS bloque,
        a.producto,
        a.variedad,
        a.programa,
        a.temporada_obj,
        'COSECHA' AS tipo,
        a.fecha_pico,
        a.tipo AS tipo_siembra,
        br.nombre AS maquilador,
        a.esquejes_cosecha AS valor
    FROM program_add_pto AS a
    LEFT JOIN breeders AS br ON br.id = a.casa_id
    WHERE a.estado = 1 
        AND a.raiz = 0
        AND a.fecha_cosecha_r IS NOT NULL
";

// ============================================================================
// EJECUCIÓN DE CONSULTAS
// ============================================================================

$errores = [];
$exitosas = 0;

// Consultas activas
$resultTeoSiembra = $conexion->query($queryTeoSiembra);
if (!$resultTeoSiembra) {
    $errores[] = "Error en TeoSiembra: " . $conexion->error;
} else {
    $exitosas++;
}

$resultTeoEnsarte = $conexion->query($queryTeoEnsarte);
if (!$resultTeoEnsarte) {
    $errores[] = "Error en TeoEnsarte: " . $conexion->error;
} else {
    $exitosas++;
}

$resultTeoCosecha = $conexion->query($queryTeoCosecha);
if (!$resultTeoCosecha) {
    $errores[] = "Error en TeoCosecha: " . $conexion->error;
} else {
    $exitosas++;
}

$resultSiembra = $conexion->query($querySiembra);
if (!$resultSiembra) {
    $errores[] = "Error en Siembra: " . $conexion->error;
} else {
    $exitosas++;
}

$resultEnsarte = $conexion->query($queryEnsartes);
if (!$resultEnsarte) {
    $errores[] = "Error en Ensartes: " . $conexion->error;
} else {
    $exitosas++;
}

$resultCosecha = $conexion->query($queryCosechas);
if (!$resultCosecha) {
    $errores[] = "Error en Cosechas: " . $conexion->error;
} else {
    $exitosas++;
}

$resultEnsarte_pto = $conexion->query($queryEnsartes_pto);
if (!$resultEnsarte_pto) {
    $errores[] = "Error en Ensartes_pto: " . $conexion->error;
} else {
    $exitosas++;
}

$resultCosecha_pto = $conexion->query($queryCosechas_pto);
if (!$resultCosecha_pto) {
    $errores[] = "Error en Cosechas_pto: " . $conexion->error;
} else {
    $exitosas++;
}

// ============================================================================
// CIERRE DE CONEXIÓN
// ============================================================================
$conexion->close();

// ============================================================================
// MENSAJE DE RESULTADO
// ============================================================================
if (count($errores) > 0) {
    $mensaje = "✗ Proceso completado con errores. Consultas exitosas: $exitosas/8\n";
    $mensaje .= "Errores encontrados:\n" . implode("\n", $errores);
    error_log($mensaje);
    echo $mensaje;
    ?>
    <script>
        console.error("✗ Proceso completado con errores. Consultas exitosas: <?php echo $exitosas; ?>/8");
        <?php foreach ($errores as $error): ?>
        console.error("<?php echo addslashes($error); ?>");
        <?php endforeach; ?>
    </script>
    <?php
} else {
    $mensaje = "✓ Proceso completado exitosamente: viewsowing actualizada ($exitosas consultas ejecutadas)";
    error_log($mensaje);
    echo $mensaje . "\n";
    ?>
    <script>
        console.log("✓ Proceso completado exitosamente: viewsowing actualizada (<?php echo $exitosas; ?> consultas ejecutadas)");
    </script>
    <?php
}
?>
