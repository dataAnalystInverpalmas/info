<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';


$id = $_POST['id'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$hora = $_POST['hora'] ?? '';

if (empty($id) || empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'Por favor completa los campos requeridos']);
    exit;
}

$sql = "UPDATE refri_jornadas SET nombre = ?, hora = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssi", $nombre, $hora, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Jornada actualizada exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
