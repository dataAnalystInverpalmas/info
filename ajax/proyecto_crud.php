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

        $nombre = $_POST['nombre'];
        $categoria = $_POST['categoria'] ?: null;
        $descripcion = $_POST['descripcion'] ?? null;
        $estado = $_POST['estado'] ?? 'activo';
        $fecha_inicio = $_POST['fecha_inicio'] ?: null;
        $fecha_fin = $_POST['fecha_fin'] ?: null;

        $stmt = $conexion->prepare("INSERT INTO proyectos (nombre, categoria, descripcion, estado, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $categoria, $descripcion, $estado, $fecha_inicio, $fecha_fin);
        $ok = $stmt->execute();

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Creado' : $conexion->error]);
        break;

    case 'update':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0 || empty($_POST['nombre'])) {
            echo json_encode(['success' => false, 'mensaje' => 'Datos invalidos']);
            exit;
        }

        $nombre = $_POST['nombre'];
        $categoria = $_POST['categoria'] ?: null;
        $descripcion = $_POST['descripcion'] ?? null;
        $estado = $_POST['estado'] ?? 'activo';
        $fecha_inicio = $_POST['fecha_inicio'] ?: null;
        $fecha_fin = $_POST['fecha_fin'] ?: null;

        $stmt = $conexion->prepare("UPDATE proyectos SET nombre=?, categoria=?, descripcion=?, estado=?, fecha_inicio=?, fecha_fin=? WHERE id=?");
        $stmt->bind_param("ssssssi", $nombre, $categoria, $descripcion, $estado, $fecha_inicio, $fecha_fin, $id);
        $ok = $stmt->execute();

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Actualizado' : $conexion->error]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID invalido']);
            exit;
        }

        $stmt = $conexion->prepare("DELETE FROM proyectos WHERE id=?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Eliminado' : $conexion->error]);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Accion invalida']);
        break;
}
