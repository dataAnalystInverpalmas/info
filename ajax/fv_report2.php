<?php
// Datos crudos para el reporte "Perfil de Variedades" (floreros).
// Devuelve floreros, causas de fin y evaluaciones del rango; toda la agregación e interacción
// se hace en el navegador para que los filtros respondan al instante.
if (is_file("funciones/conexion.php")) {
    include_once("funciones/conexion.php");
} else {
    include_once("../funciones/conexion.php");
}

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Sesión expirada']);
    exit;
}

function fvr_fecha($v, $def) {
    $d = DateTime::createFromFormat('Y-m-d', (string)$v);
    return ($d && $d->format('Y-m-d') === $v) ? $v : $def;
}

$fini = fvr_fecha($_GET['fini'] ?? '', date('Y-m-d', strtotime('-365 days')));
$ffin = fvr_fecha($_GET['ffin'] ?? '', date('Y-m-d'));

$stmt = $conexion->prepare(
    "SELECT f.id, f.fecha_corte, f.fecha_florero, f.tallos, f.pmax, f.pcorte, f.pempaque, f.pflorero,
            f.guarde_granel, f.simulacion, f.grupo_descripcion, f.observacion,
            v.nombre AS variedad, v.producto, k.nombre AS tipo, o.nombre AS origen
     FROM flower_vases f
     LEFT JOIN varieties v ON v.id = f.variedad_id
     LEFT JOIN fv_kinds k ON k.id = f.tipo_id
     LEFT JOIN fv_origens o ON o.id = f.origen_id
     WHERE f.fecha_florero BETWEEN ? AND ?
     ORDER BY f.fecha_florero, f.id"
);
$stmt->bind_param('ss', $fini, $ffin);
$stmt->execute();
$floreros = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conexion->prepare(
    "SELECT c.fv_id, c.causes_item_id, ci.nombre AS causa, c.dias, c.cantidad
     FROM fv_causes c
     INNER JOIN flower_vases f ON f.id = c.fv_id
     LEFT JOIN fv_causes_items ci ON ci.id = c.causes_item_id
     WHERE f.fecha_florero BETWEEN ? AND ?"
);
$stmt->bind_param('ss', $fini, $ffin);
$stmt->execute();
$causas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conexion->prepare(
    "SELECT e.fv_id, ei.nombre AS item, ei.puntaje
     FROM fv_evaluations e
     INNER JOIN flower_vases f ON f.id = e.fv_id
     INNER JOIN fv_evaluation_items ei ON ei.id = e.evaluation_item_id
     WHERE f.fecha_florero BETWEEN ? AND ?"
);
$stmt->bind_param('ss', $fini, $ffin);
$stmt->execute();
$evals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode([
    'ok' => true,
    'fini' => $fini,
    'ffin' => $ffin,
    'floreros' => $floreros,
    'causas' => $causas,
    'evals' => $evals,
]);
