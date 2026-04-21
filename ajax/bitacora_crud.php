<?php
include('../funciones/conexion.php');
header('Content-Type: application/json; charset=utf-8');

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'create':
        if (empty($_POST['descripcion'])) {
            echo json_encode(['success' => false, 'mensaje' => 'Descripcion requerida']);
            exit;
        }

        $tarea_id = ($_POST['tarea_id'] ?? '') === '' ? null : (int)$_POST['tarea_id'];
        $tipo_registro = $_POST['tipo_registro'] ?? 'nota';
        $descripcion = $_POST['descripcion'];
        $autor = $_POST['autor'] ?? ($_SESSION['usuario'] ?? 'Sistema');

        $stmt = $conexion->prepare("INSERT INTO bitacora (tarea_id, tipo_registro, descripcion, autor) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $tarea_id, $tipo_registro, $descripcion, $autor);
        $ok = $stmt->execute();

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Creado' : $conexion->error]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID invalido']);
            exit;
        }

        $stmt = $conexion->prepare("DELETE FROM bitacora WHERE id=?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Eliminado' : $conexion->error]);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Accion invalida']);
        break;
}
