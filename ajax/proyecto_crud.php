<?php
include('../funciones/conexion.php');
header('Content-Type: application/json; charset=utf-8');

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'create':
        if (empty($_POST['nombre'])) {
            echo json_encode(['success' => false, 'mensaje' => 'Nombre requerido']);
            exit;
        }

        $usuario_id = $_SESSION['id'] ?? null;
        $nombre = $_POST['nombre'];
        $categoria = $_POST['categoria'] ?: null;
        $descripcion = $_POST['descripcion'] ?? null;
        $estado = $_POST['estado'] ?? 'activo';
        $fecha_inicio = $_POST['fecha_inicio'] ?: null;
        $fecha_fin = $_POST['fecha_fin'] ?: null;

        $stmt = $conexion->prepare("INSERT INTO proyectos (usuario_id, nombre, categoria, descripcion, estado, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $usuario_id, $nombre, $categoria, $descripcion, $estado, $fecha_inicio, $fecha_fin);
        $ok = $stmt->execute();

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Creado' : $conexion->error]);
        break;

    case 'update':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0 || empty($_POST['nombre'])) {
            echo json_encode(['success' => false, 'mensaje' => 'Datos invalidos']);
            exit;
        }

        $usuario_id = $_SESSION['id'] ?? null;
        $nombre = $_POST['nombre'];
        $categoria = $_POST['categoria'] ?: null;
        $descripcion = $_POST['descripcion'] ?? null;
        $estado = $_POST['estado'] ?? 'activo';
        $fecha_inicio = $_POST['fecha_inicio'] ?: null;
        $fecha_fin = $_POST['fecha_fin'] ?: null;

        $stmt = $conexion->prepare("UPDATE proyectos SET nombre=?, categoria=?, descripcion=?, estado=?, fecha_inicio=?, fecha_fin=? WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
        $stmt->bind_param("ssssssii", $nombre, $categoria, $descripcion, $estado, $fecha_inicio, $fecha_fin, $id, $usuario_id);
        $ok = $stmt->execute();

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Actualizado' : $conexion->error]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID invalido']);
            exit;
        }

        $usuario_id = $_SESSION['id'] ?? null;
        $stmt = $conexion->prepare("DELETE FROM proyectos WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
        $stmt->bind_param("ii", $id, $usuario_id);
        $ok = $stmt->execute();

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Eliminado' : $conexion->error]);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Accion invalida']);
        break;
}
