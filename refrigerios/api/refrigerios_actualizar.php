<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';


$id = $_POST['id'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$descripcion_categoria = $_POST['descripcion_categoria'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';


if (empty($id) || empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'Por favor completa los campos requeridos']);
    exit;
}

$sql = "UPDATE refri_refrigerios SET nombre = ?, categoria = ?, descripcion_categoria = ?, descripcion = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssssi", $nombre, $categoria, $descripcion_categoria, $descripcion, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Refrigerio actualizado exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
