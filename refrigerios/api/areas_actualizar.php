<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$id = $_POST['id'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$finca = $_POST['finca'] ?? '';

if (empty($id) || empty($nombre) || empty($finca)) {
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos']);
    exit;
}

$sql = "UPDATE refri_areas SET nombre = ?, finca = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssi", $nombre, $finca, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Área actualizada exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
