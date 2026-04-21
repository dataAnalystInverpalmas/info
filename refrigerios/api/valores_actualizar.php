<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';


$id = $_POST['id'] ?? '';
$refrigerio_id = $_POST['refrigerio_id'] ?? '';
$proveedor_id = $_POST['proveedor_id'] ?? '';
$valor = $_POST['valor'] ?? '';

if (empty($id) || empty($refrigerio_id) || empty($proveedor_id) || empty($valor)) {
    echo json_encode(['success' => false, 'message' => 'Por favor completa todos los campos']);
    exit;
}

$sql = "UPDATE refri_valores SET refrigerio_id = ?, proveedor_id = ?, valor = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("iidi", $refrigerio_id, $proveedor_id, $valor, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Valor actualizado exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conexion->error]);
}

$stmt->close();
$conexion->close();
?>
