<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';


$sql = "SELECT v.id, v.refrigerio_id, v.proveedor_id, v.valor, r.nombre as refrigerio, p.nombre as proveedor
    FROM refri_valores v
    JOIN refri_refrigerios r ON v.refrigerio_id = r.id
    JOIN refri_proveedores p ON v.proveedor_id = p.id
    WHERE v.activo = TRUE
    ORDER BY p.nombre, r.nombre";

$resultado = $conexion->query($sql);
$valores = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $valores[] = $fila;
    }
    echo json_encode(['success' => true, 'data' => $valores]);
} else {
    echo json_encode(['success' => true, 'data' => []]);
}

$conexion->close();
?>
