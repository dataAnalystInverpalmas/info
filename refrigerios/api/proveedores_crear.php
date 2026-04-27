<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$nombre = $_POST['nombre'] ?? '';
$nit = $_POST['nit'] ?? '';
$descuento = isset($_POST['descuento']) ? 1 : 0;

if (empty($nombre) || empty($nit)) {
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos']);
    exit;
}

$sql = "INSERT INTO refri_proveedores (nombre, nit, descuento_administrativo) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssi", $nombre, $nit, $descuento);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Proveedor creado exitosamente', 'id' => $conexion->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
