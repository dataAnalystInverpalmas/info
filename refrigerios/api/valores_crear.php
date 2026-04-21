<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';


$refrigerio_id = $_POST['refrigerio_id'] ?? '';
$proveedor_id = $_POST['proveedor_id'] ?? '';
$valor = $_POST['valor'] ?? '';

if (empty($refrigerio_id) || empty($proveedor_id) || empty($valor)) {
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos']);
    exit;
}

$sql = "INSERT INTO refri_valores (refrigerio_id, proveedor_id, valor) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iid", $refrigerio_id, $proveedor_id, $valor);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Valor creado exitosamente', 'id' => $conexion->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
