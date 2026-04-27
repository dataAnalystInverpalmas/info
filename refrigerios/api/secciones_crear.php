<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$id_area = $_POST['id_area'] ?? '';
$nombre = $_POST['nombre'] ?? '';

if (empty($id_area) || empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos']);
    exit;
}

$sql = "INSERT INTO refri_secciones (id_area, nombre) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("is", $id_area, $nombre);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Sección creada exitosamente', 'id' => $conexion->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
