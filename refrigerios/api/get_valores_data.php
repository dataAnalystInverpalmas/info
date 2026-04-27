<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$sql_ref = "SELECT id, nombre FROM refri_refrigerios WHERE activo = TRUE ORDER BY nombre";
$ref = $conexion->query($sql_ref)->fetch_all(MYSQLI_ASSOC);

$sql_jor = "SELECT id, nombre FROM refri_jornadas WHERE activo = TRUE ORDER BY nombre";
$jor = $conexion->query($sql_jor)->fetch_all(MYSQLI_ASSOC);

$sql_prov = "SELECT id, nombre FROM refri_proveedores WHERE activo = TRUE ORDER BY nombre";
$prov = $conexion->query($sql_prov)->fetch_all(MYSQLI_ASSOC);

echo json_encode(['refrigerios' => $ref, 'jornadas' => $jor, 'proveedores' => $prov]);
$conexion->close();
?>
