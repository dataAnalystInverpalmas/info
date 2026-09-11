<?php
require_once __DIR__ . '/_crud_dynamic_common.php';
include(__DIR__ . '/../funciones/conexion.php');
header('Content-Type: application/json; charset=utf-8');

$ids = $_POST['ids'] ?? [];
if (!is_array($ids) || empty($ids)) {
    crud_json(['success' => false, 'message' => 'Seleccione al menos un registro']);
}
$idInts = array_map('intval', $ids);
$idInts = array_filter($idInts, function ($v) { return $v > 0; });
if (empty($idInts)) {
    crud_json(['success' => false, 'message' => 'Seleccione al menos un registro válido']);
}

$longitud = isset($_POST['longitud']) ? (float)str_replace(',', '.', $_POST['longitud']) : null;
if ($longitud === null || $_POST['longitud'] === '') {
    crud_json(['success' => false, 'message' => 'Ingrese un valor de longitud']);
}
$longitud = round($longitud, 2);
if ($longitud < 0) {
    crud_json(['success' => false, 'message' => 'La longitud no puede ser negativa']);
}

$idInts = array_values(array_unique($idInts));
$in = implode(',', array_fill(0, count($idInts), '?'));
$sql = 'UPDATE greenhouses SET longitud = ? WHERE id IN (' . $in . ')';
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    crud_json(['success' => false, 'message' => $conexion->error]);
}

$types = 'd' . str_repeat('i', count($idInts));
$params = array_merge([$longitud], $idInts);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    crud_json(['success' => true, 'affected' => $stmt->affected_rows, 'message' => 'Longitud']);
}
crud_json(['success' => false, 'message' => $stmt->error]);