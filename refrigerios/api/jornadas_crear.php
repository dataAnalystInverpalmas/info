<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';


$nombre = $_POST['nombre'] ?? '';
$hora = $_POST['hora'] ?? '';

if (empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'Por favor ingresa el nombre']);
    exit;
}

$sql = "INSERT INTO refri_jornadas (nombre, hora) VALUES (?, ?)";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en prepare: ' . $conexion->error]);
    exit;
}
$stmt->bind_param("ss", $nombre, $hora);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Jornada creada exitosamente', 'id' => $conexion->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
