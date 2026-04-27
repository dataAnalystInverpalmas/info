<?php
include('../funciones/conexion.php');
header('Content-Type: application/json; charset=utf-8');

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {

    case 'list':
        $desde = $_POST['desde'] ?? '';
        $hasta = $_POST['hasta'] ?? '';

        if (!$desde || !$hasta) {
            echo json_encode(['success' => false, 'mensaje' => 'Rango de fechas requerido']);
            exit;
        }

        $usuario_id = $_SESSION['id'] ?? null;

        $stmt = $conexion->prepare(
            "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                    t.fecha_inicio, t.fecha_vencimiento, t.responsable,
                    t.proyecto_id, p.nombre AS proyecto_nombre
             FROM tareas t
             LEFT JOIN proyectos p ON t.proyecto_id = p.id
             WHERE t.estado != 'cancelada'
               AND (t.usuario_id = ? OR t.usuario_id IS NULL)
               AND (
                    (t.fecha_inicio IS NOT NULL AND t.fecha_inicio BETWEEN ? AND ?)
                 OR (t.fecha_vencimiento IS NOT NULL AND t.fecha_vencimiento BETWEEN ? AND ?)
                 OR (t.fecha_inicio IS NOT NULL AND t.fecha_vencimiento IS NOT NULL
                     AND t.fecha_inicio <= ? AND t.fecha_vencimiento >= ?)
               )
             ORDER BY FIELD(t.prioridad,'urgente','alta','media','baja'), t.fecha_vencimiento ASC"
        );
        $stmt->bind_param("issssss", $usuario_id, $desde, $hasta, $desde, $hasta, $hasta, $desde);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(['success' => true, 'tareas' => $data]);
        break;

    case 'update_estado':
        $id     = (int)($_POST['id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        $estados_validos = ['pendiente', 'en_progreso', 'completada', 'cancelada'];

        if ($id <= 0 || !in_array($estado, $estados_validos, true)) {
            echo json_encode(['success' => false, 'mensaje' => 'Datos inválidos']);
            exit;
        }

        // Leer estado anterior
        $uid = (int)($_SESSION['id'] ?? 0);
        $stmtAntes = $conexion->prepare("SELECT estado, proyecto_id FROM tareas WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL) LIMIT 1");
        $stmtAntes->bind_param("ii", $id, $uid);
        $stmtAntes->execute();
        $antes = $stmtAntes->get_result()->fetch_assoc() ?? [];

        $stmt = $conexion->prepare("UPDATE tareas SET estado = ? WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL)");
        $stmt->bind_param("sii", $estado, $id, $uid);
        $ok = $stmt->execute();

        if ($ok && ($antes['estado'] ?? '') !== $estado) {
            $autor       = $_SESSION['usuario'] ?? 'Sistema';
            $proyecto_id = $antes['proyecto_id'] ?? null;
            $desc_reg    = 'Cambio de estado: ' . ($antes['estado'] ?? '?') . ' -> ' . $estado;

            $b = $conexion->prepare(
                "INSERT INTO bitacora (tarea_id, proyecto_id, tipo_registro, descripcion, autor)
                 VALUES (?, ?, 'actualizacion', ?, ?)"
            );
            $b->bind_param("iiss", $id, $proyecto_id, $desc_reg, $autor);
            $b->execute();
        }

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Estado actualizado' : $conexion->error]);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Acción inválida']);
        break;
}
