<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';

if (empty($titulo) || empty($descripcion)) {
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos']);
    exit;
}

$sql = "INSERT INTO refri_tareas (titulo, descripcion, fecha_creacion) VALUES (?, ?, NOW())";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $titulo, $descripcion);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Tarea creada exitosamente', 'id' => $conexion->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al crear la tarea: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
