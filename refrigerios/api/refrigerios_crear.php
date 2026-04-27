<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';


$nombre = $_POST['nombre'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$descripcion_categoria = $_POST['descripcion_categoria'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';


if (empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'Por favor ingresa el nombre']);
    exit;
}

$sql = "INSERT INTO refri_refrigerios (nombre, categoria, descripcion_categoria, descripcion) VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssss", $nombre, $categoria, $descripcion_categoria, $descripcion);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Refrigerio creado exitosamente', 'id' => $conexion->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
