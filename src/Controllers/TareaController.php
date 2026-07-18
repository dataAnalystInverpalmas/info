<?php
namespace App\Controllers;

use App\Models\Tarea;
use App\Models\Proyecto;

class TareaController {
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    private static function columnExists($conexion, string $table, string $column): bool {
        $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ss", $table, $column);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return ((int)($row['total'] ?? 0)) > 0;
    }

    private static function siguienteOrdenTarea($conexion, $proyecto_id) {
        if (!self::columnExists($conexion, 'tareas', 'orden_ejecucion')) {
            return null;
        }

        if ($proyecto_id === null || $proyecto_id === '') {
            $result = $conexion->query("SELECT COALESCE(MAX(orden_ejecucion), 0) + 1 AS siguiente FROM tareas WHERE proyecto_id IS NULL");
            $row = $result ? $result->fetch_assoc() : null;
            return isset($row['siguiente']) ? max(1, (int)$row['siguiente']) : 1;
        }

        $stmt = $conexion->prepare("SELECT COALESCE(MAX(orden_ejecucion), 0) + 1 AS siguiente FROM tareas WHERE proyecto_id = ?");
        if (!$stmt) {
            return 1;
        }

        $stmt->bind_param("i", $proyecto_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return isset($row['siguiente']) ? max(1, (int)$row['siguiente']) : 1;
    }

    public function index() {
        $usuario_id = $_SESSION['id'] ?? null;
        $tareas = Tarea::getAll($usuario_id);
        $proyectos = Proyecto::getAll($usuario_id);
        extract(['tareas' => $tareas, 'proyectos' => $proyectos]);
        require_once __DIR__ . '/../Views/Tareas/index.php';
    }

    // ── Métodos estáticos para AJAX ───────────────────────────

    public static function listar() {
        $usuario_id = $_SESSION['id'] ?? null;
        return Tarea::getAll($usuario_id);
    }

    public static function obtener($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        return Tarea::getById((int)$id, $usuario_id);
    }

    public static function pendientes() {
        $usuario_id = $_SESSION['id'] ?? null;
        return Tarea::getPendientes($usuario_id);
    }

    public static function imprevistas() {
        $usuario_id = $_SESSION['id'] ?? null;
        return Tarea::getImprevistas($usuario_id);
    }

    public static function proximas() {
        $usuario_id = $_SESSION['id'] ?? null;
        return Tarea::getProximas($usuario_id, 7);
    }

    public static function obtenerPorProyecto($proyecto_id) {
        $usuario_id = $_SESSION['id'] ?? null;
        return Tarea::getByProyecto((int)$proyecto_id, $usuario_id);
    }

    public static function cambiarEstado($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        if (empty($usuario_id)) {
            return ['success' => false, 'mensaje' => 'Sesion no valida. Inicia sesion nuevamente.'];
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $estado = $input['estado'] ?? '';
        $estados_validos = ['pendiente', 'en_progreso', 'completada', 'cancelada'];

        if (!in_array($estado, $estados_validos, true)) {
            return ['success' => false, 'mensaje' => 'Estado no valido'];
        }

        $conexion = \App\Helpers\Database::getConnection();

        $stmtAntes = $conexion->prepare("SELECT proyecto_id, estado, porcentaje_avance FROM tareas WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL) LIMIT 1");
        if (!$stmtAntes) {
            return ['success' => false, 'mensaje' => 'Error de consulta'];
        }
        $stmtAntes->bind_param("ii", $id, $usuario_id);
        $stmtAntes->execute();
        $antes = $stmtAntes->get_result()->fetch_assoc();
        if (!$antes) {
            return ['success' => false, 'mensaje' => 'Tarea no encontrada'];
        }

        $porcentaje = ($estado === 'completada') ? 100 : (($estado === 'en_progreso') ? 25 : (($estado === 'pendiente') ? 0 : (int)$antes['porcentaje_avance']));

        $stmt = $conexion->prepare("UPDATE tareas SET estado = ?, porcentaje_avance = ? WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL)");
        if (!$stmt) {
            return ['success' => false, 'mensaje' => 'Error de consulta'];
        }
        $stmt->bind_param("siii", $estado, $porcentaje, $id, $usuario_id);
        $ok = $stmt->execute();

        if ($ok && $antes['estado'] !== $estado) {
            $autor = $_SESSION['usuario'] ?? 'Sistema';
            $descripcion = 'Cambio de estado: ' . $antes['estado'] . ' -> ' . $estado;
            $stmtBit = $conexion->prepare("INSERT INTO bitacora (tarea_id, proyecto_id, tipo_registro, descripcion, autor) VALUES (?, ?, 'cambio_estado', ?, ?)");
            if ($stmtBit) {
                $stmtBit->bind_param("iiss", $id, $antes['proyecto_id'], $descripcion, $autor);
                $stmtBit->execute();
            }
        }

        return ['success' => $ok, 'mensaje' => $ok ? 'Estado actualizado' : $conexion->error, 'porcentaje_avance' => $porcentaje];
    }

    public static function crear() {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $nombre = trim($input['nombre'] ?? '');
        $tipo = $input['tipo'] ?? 'prevista';
        $descripcion = trim($input['descripcion'] ?? '');
        $proyecto_id = isset($input['proyecto_id']) && $input['proyecto_id'] !== '' ? (int)$input['proyecto_id'] : null;
        $orden_ejecucion = isset($input['orden_ejecucion']) && $input['orden_ejecucion'] !== ''
            ? max(1, (int)$input['orden_ejecucion'])
            : null;
        $responsable = trim((string)($input['responsable'] ?? ''));
        $responsable = $responsable !== '' ? $responsable : null;
        $quien_solicita = trim((string)($input['quien_solicita'] ?? ''));
        $quien_solicita = $quien_solicita !== '' ? $quien_solicita : null;
        $estado = $input['estado'] ?? 'pendiente';
        $porcentaje_avance = isset($input['porcentaje_avance']) ? max(0, min(100, (int)$input['porcentaje_avance'])) : null;
        if ($porcentaje_avance === null) {
            $porcentaje_avance = ($estado === 'completada') ? 100 : (($estado === 'en_progreso') ? 25 : 0);
        }
        $prioridad = $input['prioridad'] ?? 'media';
        $fecha_inicio = trim((string)($input['fecha_inicio'] ?? ''));
        $fecha_inicio = $fecha_inicio !== '' ? $fecha_inicio : null;
        $fecha_vencimiento = trim((string)($input['fecha_vencimiento'] ?? ''));
        $fecha_vencimiento = $fecha_vencimiento !== '' ? $fecha_vencimiento : null;

        if (empty($nombre)) {
            return ['success' => false, 'mensaje' => 'Nombre requerido'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        if (empty($usuario_id)) {
            return ['success' => false, 'mensaje' => 'Sesion no valida. Inicia sesion nuevamente.'];
        }
        $conexion = \App\Helpers\Database::getConnection();
        $tieneOrden = self::columnExists($conexion, 'tareas', 'orden_ejecucion');
        if ($orden_ejecucion === null) {
            $orden_ejecucion = self::siguienteOrdenTarea($conexion, $proyecto_id);
        }
        $tieneQuienSolicita = self::columnExists($conexion, 'tareas', 'quien_solicita');

        if ($tieneOrden && $tieneQuienSolicita) {
            $stmt = $conexion->prepare("INSERT INTO tareas (usuario_id, nombre, tipo, descripcion, proyecto_id, orden_ejecucion, responsable, quien_solicita, estado, porcentaje_avance, prioridad, fecha_inicio, fecha_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) return ['success' => false, 'mensaje' => $conexion->error ?: 'Error de consulta'];
            $stmt->bind_param("isssiisssisss", $usuario_id, $nombre, $tipo, $descripcion, $proyecto_id, $orden_ejecucion, $responsable, $quien_solicita, $estado, $porcentaje_avance, $prioridad, $fecha_inicio, $fecha_vencimiento);
        } elseif ($tieneOrden) {
            $stmt = $conexion->prepare("INSERT INTO tareas (usuario_id, nombre, tipo, descripcion, proyecto_id, orden_ejecucion, responsable, estado, porcentaje_avance, prioridad, fecha_inicio, fecha_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) return ['success' => false, 'mensaje' => $conexion->error ?: 'Error de consulta'];
            $stmt->bind_param("isssiississs", $usuario_id, $nombre, $tipo, $descripcion, $proyecto_id, $orden_ejecucion, $responsable, $estado, $porcentaje_avance, $prioridad, $fecha_inicio, $fecha_vencimiento);
        } else {
            $stmt = $conexion->prepare("INSERT INTO tareas (usuario_id, nombre, tipo, descripcion, proyecto_id, responsable, estado, porcentaje_avance, prioridad, fecha_inicio, fecha_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) return ['success' => false, 'mensaje' => $conexion->error ?: 'Error de consulta'];
            $stmt->bind_param("isssississs", $usuario_id, $nombre, $tipo, $descripcion, $proyecto_id, $responsable, $estado, $porcentaje_avance, $prioridad, $fecha_inicio, $fecha_vencimiento);
        }
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

        return ['success' => $ok, 'mensaje' => $ok ? 'Creada' : $conexion->error, 'id' => $conexion->insert_id];
    }

    public static function actualizar($id) {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $nombre = trim($input['nombre'] ?? '');
        $tipo = $input['tipo'] ?? 'prevista';
        $descripcion = trim($input['descripcion'] ?? '');
        $proyecto_id = isset($input['proyecto_id']) && $input['proyecto_id'] !== '' ? (int)$input['proyecto_id'] : null;
        $orden_ejecucion = isset($input['orden_ejecucion']) && $input['orden_ejecucion'] !== ''
            ? max(1, (int)$input['orden_ejecucion'])
            : null;
        $responsable = trim((string)($input['responsable'] ?? ''));
        $responsable = $responsable !== '' ? $responsable : null;
        $quien_solicita = trim((string)($input['quien_solicita'] ?? ''));
        $quien_solicita = $quien_solicita !== '' ? $quien_solicita : null;
        $estado = $input['estado'] ?? 'pendiente';
        $porcentaje_enviado = isset($input['porcentaje_avance']);
        $prioridad = $input['prioridad'] ?? 'media';
        $fecha_inicio = trim((string)($input['fecha_inicio'] ?? ''));
        $fecha_inicio = $fecha_inicio !== '' ? $fecha_inicio : null;
        $fecha_vencimiento = trim((string)($input['fecha_vencimiento'] ?? ''));
        $fecha_vencimiento = $fecha_vencimiento !== '' ? $fecha_vencimiento : null;

        if (empty($nombre)) {
            return ['success' => false, 'mensaje' => 'Nombre requerido'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        if (empty($usuario_id)) {
            return ['success' => false, 'mensaje' => 'Sesion no valida. Inicia sesion nuevamente.'];
        }

        $conexion = \App\Helpers\Database::getConnection();
        $tieneOrden = self::columnExists($conexion, 'tareas', 'orden_ejecucion');
        $tieneQuienSolicita = self::columnExists($conexion, 'tareas', 'quien_solicita');

        $quienSolicitaSelect = $tieneQuienSolicita ? 'quien_solicita' : 'NULL AS quien_solicita';
        $ordenSelect = $tieneOrden ? 'orden_ejecucion' : 'NULL AS orden_ejecucion';
        $stmtAntes = $conexion->prepare("SELECT nombre, tipo, descripcion, proyecto_id, {$ordenSelect}, responsable, {$quienSolicitaSelect}, estado, porcentaje_avance, prioridad, fecha_inicio, fecha_vencimiento FROM tareas WHERE id=? AND (usuario_id=? OR usuario_id IS NULL) LIMIT 1");
        if (!$stmtAntes) {
            return ['success' => false, 'mensaje' => $conexion->error ?: 'Error de consulta'];
        }
        $stmtAntes->bind_param("ii", $id, $usuario_id);
        $stmtAntes->execute();
        $antes = $stmtAntes->get_result()->fetch_assoc() ?? [];

        $porcentaje_avance = $porcentaje_enviado ? max(0, min(100, (int)$input['porcentaje_avance'])) : ($antes['porcentaje_avance'] ?? 0);
        if (($antes['estado'] ?? '') !== $estado && !$porcentaje_enviado) {
            $porcentaje_avance = ($estado === 'pendiente') ? 0 : (($estado === 'en_progreso') ? 25 : (($estado === 'completada') ? 100 : $porcentaje_avance));
        }

        if ($tieneOrden && $orden_ejecucion === null) {
            $orden_ejecucion = isset($antes['orden_ejecucion']) && $antes['orden_ejecucion'] !== null
                ? (int)$antes['orden_ejecucion']
                : self::siguienteOrdenTarea($conexion, $proyecto_id);
        }

        if ($tieneOrden && $tieneQuienSolicita) {
            $stmt = $conexion->prepare("UPDATE tareas SET nombre=?, tipo=?, descripcion=?, proyecto_id=?, orden_ejecucion=?, responsable=?, quien_solicita=?, estado=?, porcentaje_avance=?, prioridad=?, fecha_inicio=?, fecha_vencimiento=? WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
            $stmt->bind_param("sssiisssisssii", $nombre, $tipo, $descripcion, $proyecto_id, $orden_ejecucion, $responsable, $quien_solicita, $estado, $porcentaje_avance, $prioridad, $fecha_inicio, $fecha_vencimiento, $id, $usuario_id);
        } elseif ($tieneOrden) {
            $stmt = $conexion->prepare("UPDATE tareas SET nombre=?, tipo=?, descripcion=?, proyecto_id=?, orden_ejecucion=?, responsable=?, estado=?, porcentaje_avance=?, prioridad=?, fecha_inicio=?, fecha_vencimiento=? WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
            $stmt->bind_param("sssiississsii", $nombre, $tipo, $descripcion, $proyecto_id, $orden_ejecucion, $responsable, $estado, $porcentaje_avance, $prioridad, $fecha_inicio, $fecha_vencimiento, $id, $usuario_id);
        } elseif ($tieneQuienSolicita) {
            $stmt = $conexion->prepare("UPDATE tareas SET nombre=?, tipo=?, descripcion=?, proyecto_id=?, responsable=?, quien_solicita=?, estado=?, porcentaje_avance=?, prioridad=?, fecha_inicio=?, fecha_vencimiento=? WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
            $stmt->bind_param("sssisssisssii", $nombre, $tipo, $descripcion, $proyecto_id, $responsable, $quien_solicita, $estado, $porcentaje_avance, $prioridad, $fecha_inicio, $fecha_vencimiento, $id, $usuario_id);
        } else {
            $stmt = $conexion->prepare("UPDATE tareas SET nombre=?, tipo=?, descripcion=?, proyecto_id=?, responsable=?, estado=?, porcentaje_avance=?, prioridad=?, fecha_inicio=?, fecha_vencimiento=? WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
            $stmt->bind_param("sssississsii", $nombre, $tipo, $descripcion, $proyecto_id, $responsable, $estado, $porcentaje_avance, $prioridad, $fecha_inicio, $fecha_vencimiento, $id, $usuario_id);
        }

        $ok = $stmt && $stmt->execute();

        if ($ok) {
            $autor = $_SESSION['usuario'] ?? 'Sistema';
            $nuevos = ['nombre' => $nombre, 'tipo' => $tipo, 'descripcion' => $descripcion, 'proyecto_id' => $proyecto_id, 'orden_ejecucion' => $orden_ejecucion, 'responsable' => $responsable, 'quien_solicita' => $quien_solicita, 'porcentaje_avance' => $porcentaje_avance, 'prioridad' => $prioridad, 'fecha_inicio' => $fecha_inicio, 'fecha_vencimiento' => $fecha_vencimiento];
            $diff = [];
            foreach ($nuevos as $campo => $valNuevo) {
                $valAntes = $antes[$campo] ?? null;
                if ((string)$valAntes !== (string)$valNuevo) {
                    $diff[$campo] = ['antes' => $valAntes, 'despues' => $valNuevo];
                }
            }
            $camposTexto = empty($diff) ? 'sin cambios detectados' : implode(', ', array_keys($diff));
            $desc_reg = 'Actualización de: ' . $camposTexto;
            $desc_antes = $antes['descripcion'] ?? null;
            $cambios_json = empty($diff) ? null : json_encode($diff, JSON_UNESCAPED_UNICODE);
            $b = $conexion->prepare("INSERT INTO bitacora (tarea_id, proyecto_id, tipo_registro, descripcion, descripcion_antes, cambios_json, autor) VALUES (?, ?, 'actualizacion', ?, ?, ?, ?)");
            if ($b) {
                $b->bind_param("iissss", $id, $proyecto_id, $desc_reg, $desc_antes, $cambios_json, $autor);
                $b->execute();
            }
        }

        return ['success' => $ok, 'mensaje' => $ok ? 'Actualizada' : ($stmt ? $stmt->error : $conexion->error), 'porcentaje_avance' => $porcentaje_avance, 'filas_afectadas' => $conexion->affected_rows];
    }

    public static function eliminar($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        if (empty($usuario_id)) {
            return ['success' => false, 'mensaje' => 'Sesion no valida. Inicia sesion nuevamente.'];
        }
        $conexion = \App\Helpers\Database::getConnection();
        $stmt = $conexion->prepare("DELETE FROM tareas WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $stmt->bind_param("ii", $id, $usuario_id);
        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Eliminada' : $conexion->error];
    }

    public static function reordenar() {
        $usuario_id = $_SESSION['id'] ?? null;
        if (empty($usuario_id)) {
            return ['success' => false, 'mensaje' => 'Sesion no valida. Inicia sesion nuevamente.'];
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $tareas = $input['tareas'] ?? [];

        if (empty($tareas) || !is_array($tareas)) {
            return ['success' => false, 'mensaje' => 'Datos de reordenamiento inválidos'];
        }

        $conexion = \App\Helpers\Database::getConnection();
        $tieneOrden = self::columnExists($conexion, 'tareas', 'orden_ejecucion');

        if (!$tieneOrden) {
            return ['success' => false, 'mensaje' => 'Campo orden_ejecucion no existe'];
        }

        try {
            $conexion->begin_transaction();

            foreach ($tareas as $tarea) {
                $id = (int)($tarea['id'] ?? 0);
                $orden = (int)($tarea['orden_ejecucion'] ?? 0);

                if ($id <= 0 || $orden <= 0) {
                    continue;
                }

                $stmt = $conexion->prepare("UPDATE tareas SET orden_ejecucion = ? WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL)");
                if (!$stmt) {
                    throw new \Exception('Error en prepare: ' . $conexion->error);
                }

                $stmt->bind_param("iii", $orden, $id, $usuario_id);
                if (!$stmt->execute()) {
                    throw new \Exception('Error en execute: ' . $stmt->error);
                }
            }

            $conexion->commit();
            return ['success' => true, 'mensaje' => 'Tareas reordenadas exitosamente'];
        } catch (\Exception $e) {
            $conexion->rollback();
            return ['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()];
        }
    }

    public static function handleRequest(array $server, array $query): void {
        self::sendJsonHeader();

        try {
            $metodo = $server['REQUEST_METHOD'] ?? 'GET';
            $accion = $query['accion'] ?? null;
            $id = $query['id'] ?? null;
            $proyecto_id = $query['proyecto_id'] ?? null;

            switch ($metodo) {
                case 'GET':
                    if ($accion === 'pendientes') {
                        self::respond(self::pendientes());
                        return;
                    }
                    if ($accion === 'imprevistas') {
                        self::respond(self::imprevistas());
                        return;
                    }
                    if ($accion === 'proximas') {
                        self::respond(self::proximas());
                        return;
                    }
                    if ($accion === 'por_proyecto' && $proyecto_id) {
                        self::respond(self::obtenerPorProyecto($proyecto_id));
                        return;
                    }

                    self::respond($id ? self::obtener($id) : self::listar());
                    return;

                case 'POST':
                    if ($accion === 'reordenar') {
                        self::respond(self::reordenar());
                        return;
                    }

                    self::respond(self::crear());
                    return;

                case 'PUT':
                    if (!$id) {
                        self::respond(['error' => 'ID es requerido']);
                        return;
                    }

                    if ($accion === 'cambiar_estado') {
                        self::respond(self::cambiarEstado($id));
                        return;
                    }

                    self::respond(self::actualizar($id));
                    return;

                case 'DELETE':
                    if (!$id) {
                        self::respond(['error' => 'ID es requerido']);
                        return;
                    }

                    self::respond(self::eliminar($id));
                    return;

                default:
                    self::respond(['error' => 'Método no permitido']);
                    return;
            }
        } catch (\Throwable $exception) {
            http_response_code(500);
            self::respond(['error' => $exception->getMessage()]);
        }
    }

    public static function handleLegacyCrudRequest(array $post): void {
        self::sendJsonHeader();

        $accion = $post['accion'] ?? '';

        switch ($accion) {
            case 'create':
                self::respond(self::crear());
                return;

            case 'update':
                $id = (int)($post['id'] ?? 0);
                if ($id <= 0) {
                    self::respond(['success' => false, 'mensaje' => 'ID inválido']);
                    return;
                }

                self::respond(self::actualizar($id));
                return;

            case 'delete':
                $id = (int)($post['id'] ?? 0);
                if ($id <= 0) {
                    self::respond(['success' => false, 'mensaje' => 'ID inválido']);
                    return;
                }

                self::respond(self::eliminar($id));
                return;

            default:
                self::respond(['success' => false, 'mensaje' => 'Acción inválida']);
                return;
        }
    }

    private static function sendJsonHeader(): void {
        header('Content-Type: application/json; charset=utf-8');
    }

    private static function respond($payload): void {
        echo json_encode($payload, self::JSON_FLAGS);
    }
}
