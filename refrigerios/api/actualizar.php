<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$id = $_POST['id'] ?? '';
$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';

if (empty($id) || empty($titulo) || empty($descripcion)) {
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos']);
    exit;
}

$sql = "UPDATE refri_tareas SET titulo = ?, descripcion = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssi", $titulo, $descripcion, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Tarea actualizada exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la tarea: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
