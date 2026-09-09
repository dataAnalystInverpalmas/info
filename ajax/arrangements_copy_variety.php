<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$variedadOrigen = trim($_POST['variedad_origen'] ?? '');
$variedadDestino = trim($_POST['variedad_destino'] ?? '');

if ($variedadOrigen === '' || $variedadDestino === '') {
    echo json_encode(['success' => false, 'message' => 'Debe enviar variedad origen y variedad destino']);
    exit;
}

if (mb_strtolower($variedadOrigen, 'UTF-8') === mb_strtolower($variedadDestino, 'UTF-8')) {
    echo json_encode(['success' => false, 'message' => 'La variedad origen y destino deben ser diferentes']);
    exit;
}

$conexion->begin_transaction();

try {
    $sqlSource = "SELECT finca, tipo, aplicar, medidat, valor FROM informes.arrangements WHERE variedad = ?";
    $stmtSource = $conexion->prepare($sqlSource);
    if (!$stmtSource) {
        throw new Exception($conexion->error);
    }
    $stmtSource->bind_param('s', $variedadOrigen);
    $stmtSource->execute();
    $resSource = $stmtSource->get_result();

    $sourceRows = [];
    while ($row = $resSource->fetch_assoc()) {
        $sourceRows[] = $row;
    }

    if (count($sourceRows) === 0) {
        $conexion->rollback();
        echo json_encode(['success' => false, 'message' => 'No existen registros para la variedad origen']);
        exit;
    }

    $sqlExists = "SELECT 1 FROM informes.arrangements WHERE variedad = ? AND finca = ? AND tipo = ? AND aplicar = ? LIMIT 1";
    $stmtExists = $conexion->prepare($sqlExists);
    if (!$stmtExists) {
        throw new Exception($conexion->error);
    }

    $sqlInsert = "INSERT INTO informes.arrangements (variedad, finca, tipo, aplicar, medidat, valor) VALUES (?,?,?,?,?,?)";
    $stmtInsert = $conexion->prepare($sqlInsert);
    if (!$stmtInsert) {
        throw new Exception($conexion->error);
    }

    $copiedCount = 0;
    $skippedCount = 0;

    foreach ($sourceRows as $row) {
        $finca = (string)($row['finca'] ?? '');
        $tipo = (string)($row['tipo'] ?? '');
        $aplicar = (string)($row['aplicar'] ?? '');
        $medidat = (string)($row['medidat'] ?? '');
        $valor = is_numeric($row['valor'] ?? null) ? (float)$row['valor'] : 0.0;

        $stmtExists->bind_param('ssss', $variedadDestino, $finca, $tipo, $aplicar);
        $stmtExists->execute();
        $resExists = $stmtExists->get_result();

        if ($resExists && $resExists->num_rows > 0) {
            $skippedCount++;
            continue;
        }

        $stmtInsert->bind_param('sssssd', $variedadDestino, $finca, $tipo, $aplicar, $medidat, $valor);
        if (!$stmtInsert->execute()) {
            throw new Exception($stmtInsert->error);
        }

        $copiedCount++;
    }

    $conexion->commit();

    echo json_encode([
        'success' => true,
        'copied_count' => $copiedCount,
        'skipped_count' => $skippedCount,
        'source_count' => count($sourceRows)
    ]);
} catch (Throwable $e) {
    $conexion->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
