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
        $tipo = $_POST['tipo'] ?? 'prevista';
        $descripcion = $_POST['descripcion'] ?? null;
        $proyecto_id = ($_POST['proyecto_id'] ?? '') === '' ? null : (int)$_POST['proyecto_id'];
        $responsable = $_POST['responsable'] ?: null;
        $estado = $_POST['estado'] ?? 'pendiente';
        $prioridad = $_POST['prioridad'] ?? 'media';
        $fecha_inicio = $_POST['fecha_inicio'] ?: null;
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?: null;

        $stmt = $conexion->prepare("INSERT INTO tareas (nombre, tipo, descripcion, proyecto_id, responsable, estado, prioridad, fecha_inicio, fecha_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisssss", $nombre, $tipo, $descripcion, $proyecto_id, $responsable, $estado, $prioridad, $fecha_inicio, $fecha_vencimiento);
        $ok = $stmt->execute();

        if ($ok) {
            $tarea_id = $conexion->insert_id;
            $autor = $_SESSION['usuario'] ?? 'Sistema';
            $tipo_reg = 'creacion';
            $desc = 'Tarea creada: ' . $nombre . ' [' . $tipo . ']';
            $b = $conexion->prepare("INSERT INTO bitacora (tarea_id, proyecto_id, tipo_registro, descripcion, autor) VALUES (?, ?, ?, ?, ?)");
            $b->bind_param("iisss", $tarea_id, $proyecto_id, $tipo_reg, $desc, $autor);
            $b->execute();
        }

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Creada' : $conexion->error]);
        break;

    case 'update':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0 || empty($_POST['nombre'])) {
            echo json_encode(['success' => false, 'mensaje' => 'Datos invalidos']);
            exit;
        }

        // Leer estado ANTES del cambio para generar diff
        $stmtAntes = $conexion->prepare("SELECT nombre, tipo, descripcion, proyecto_id, responsable, estado, prioridad, fecha_inicio, fecha_vencimiento FROM tareas WHERE id=? LIMIT 1");
        $stmtAntes->bind_param("i", $id);
        $stmtAntes->execute();
        $antes = $stmtAntes->get_result()->fetch_assoc() ?? [];

        $nombre = $_POST['nombre'];
        $tipo = $_POST['tipo'] ?? 'prevista';
        $descripcion = $_POST['descripcion'] ?? null;
        $proyecto_id = ($_POST['proyecto_id'] ?? '') === '' ? null : (int)$_POST['proyecto_id'];
        $responsable = $_POST['responsable'] ?: null;
        $estado = $_POST['estado'] ?? 'pendiente';
        $prioridad = $_POST['prioridad'] ?? 'media';
        $fecha_inicio = $_POST['fecha_inicio'] ?: null;
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?: null;

        $stmt = $conexion->prepare("UPDATE tareas SET nombre=?, tipo=?, descripcion=?, proyecto_id=?, responsable=?, estado=?, prioridad=?, fecha_inicio=?, fecha_vencimiento=? WHERE id=?");
        $stmt->bind_param("sssisssssi", $nombre, $tipo, $descripcion, $proyecto_id, $responsable, $estado, $prioridad, $fecha_inicio, $fecha_vencimiento, $id);
        $ok = $stmt->execute();

        if ($ok) {
            $autor = $_SESSION['usuario'] ?? 'Sistema';

            // Construir diff de campos cambiados
            $nuevos = [
                'nombre' => $nombre, 'tipo' => $tipo, 'descripcion' => $descripcion,
                'proyecto_id' => $proyecto_id, 'responsable' => $responsable,
                'estado' => $estado, 'prioridad' => $prioridad,
                'fecha_inicio' => $fecha_inicio, 'fecha_vencimiento' => $fecha_vencimiento
            ];
            $diff = [];
            foreach ($nuevos as $campo => $valNuevo) {
                $valAntes = $antes[$campo] ?? null;
                if ((string)$valAntes !== (string)$valNuevo) {
                    $diff[$campo] = ['antes' => $valAntes, 'despues' => $valNuevo];
                }
            }

            $camposTexto = empty($diff) ? 'sin cambios detectados' : implode(', ', array_keys($diff));
            $desc_reg   = 'Actualización de: ' . $camposTexto;
            $desc_antes = $antes['descripcion'] ?? null;
            $cambios_json = json_encode($diff, JSON_UNESCAPED_UNICODE);

            $b = $conexion->prepare(
                "INSERT INTO bitacora (tarea_id, proyecto_id, tipo_registro, descripcion, descripcion_antes, cambios_json, autor)
                 VALUES (?, ?, 'actualizacion', ?, ?, ?, ?)"
            );
            $b->bind_param("iissss", $id, $proyecto_id, $desc_reg, $desc_antes, $cambios_json, $autor);
            $b->execute();
        }

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Actualizada' : $conexion->error]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID invalido']);
            exit;
        }

        $stmt = $conexion->prepare("DELETE FROM tareas WHERE id=?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Eliminada' : $conexion->error]);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Accion invalida']);
        break;
}
