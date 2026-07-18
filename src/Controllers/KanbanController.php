<?php
namespace App\Controllers;

class KanbanController {

    public function index() {
        require_once __DIR__ . '/../Views/Kanban/index.php';
    }

    // ── Métodos estáticos para AJAX ───────────────────────────

    public static function listar($desde, $hasta) {
        if (!$desde || !$hasta) {
            return ['success' => false, 'mensaje' => 'Rango de fechas requerido'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        $stmt = $conexion->prepare(
            "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                    t.porcentaje_avance, t.fecha_inicio, t.fecha_vencimiento, t.responsable,
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
        return ['success' => true, 'tareas' => $data];
    }

    public static function actualizarEstado($id, $estado) {
        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();

        // Leer estado anterior
        $stmtAntes = $conexion->prepare("SELECT estado, proyecto_id, porcentaje_avance FROM tareas WHERE id=? AND (usuario_id=? OR usuario_id IS NULL) LIMIT 1");
        $stmtAntes->bind_param("ii", $id, $usuario_id);
        $stmtAntes->execute();
        $antes = $stmtAntes->get_result()->fetch_assoc();
        if (!$antes) return ['success' => false, 'mensaje' => 'Tarea no encontrada'];

        $porcentaje_avance = $antes['porcentaje_avance'];
        if ($antes['estado'] !== $estado) {
            if ($estado === 'pendiente') $porcentaje_avance = 0;
            elseif ($estado === 'en_progreso') $porcentaje_avance = 25;
            elseif ($estado === 'completada') $porcentaje_avance = 100;
        }

        $stmt = $conexion->prepare("UPDATE tareas SET estado=?, porcentaje_avance=?, fecha_actualizacion=NOW() WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
        $stmt->bind_param("siii", $estado, $porcentaje_avance, $id, $usuario_id);
        $ok = $stmt->execute();

        if ($ok && $antes['estado'] !== $estado) {
            $autor = $_SESSION['usuario'] ?? 'Sistema';
            $desc = 'Cambio de estado: ' . $antes['estado'] . ' -> ' . $estado;
            $b = $conexion->prepare("INSERT INTO bitacora (tarea_id, proyecto_id, tipo_registro, descripcion, autor) VALUES (?, ?, 'actualizacion', ?, ?)");
            $b->bind_param("iiss", $id, $antes['proyecto_id'], $desc, $autor);
            $b->execute();
        }

        return ['success' => $ok, 'mensaje' => $ok ? 'Estado actualizado' : $conexion->error, 'porcentaje_avance' => $porcentaje_avance];
    }

    public static function exportExcel($desde, $hasta) {
        if (!$desde || !$hasta) {
            return ['success' => false, 'mensaje' => 'Rango de fechas requerido'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        $desdeTs = $desde . ' 00:00:00';
        $hastaTs = $hasta . ' 23:59:59';

        $stmt = $conexion->prepare(
            "SELECT DATE(t.fecha_inicio) AS fecha_inicio,
                DATE(t.fecha_vencimiento) AS fecha_vencimiento,
                t.fecha_actualizacion,
                    COALESCE(p.nombre, 'Sin proyecto') AS proyecto,
                    t.nombre AS tarea,
                    t.descripcion,
                    t.responsable,
                    t.quien_solicita,
                          t.porcentaje_avance,
                    t.estado
             FROM tareas t
             LEFT JOIN proyectos p ON t.proyecto_id = p.id
             WHERE t.estado != 'cancelada'
               AND (t.usuario_id = ? OR t.usuario_id IS NULL)
               AND t.fecha_actualizacion BETWEEN ? AND ?
             ORDER BY t.fecha_actualizacion DESC, t.id DESC"
        );
        $stmt->bind_param("iss", $usuario_id, $desdeTs, $hastaTs);
        $stmt->execute();
        $result = $stmt->get_result();

        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte Kanban');

        $headers = [
            'Fecha Inicio',
            'Fecha Vencimiento',
            'Fecha Actualizacion',
            'Proyecto',
            'Tarea',
            'Descripcion',
            'Responsable',
            'Quien Solicita',
            '% Avance',
            'Estado'
        ];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $col++;
        }

        $rowNum = 2;
        while ($row = $result->fetch_assoc()) {
            $sheet->setCellValue('A' . $rowNum, $row['fecha_inicio'] ?? '');
            $sheet->setCellValue('B' . $rowNum, $row['fecha_vencimiento'] ?? '');
            $sheet->setCellValue('C' . $rowNum, $row['fecha_actualizacion'] ?? '');
            $sheet->setCellValue('D' . $rowNum, $row['proyecto'] ?? '');
            $sheet->setCellValue('E' . $rowNum, $row['tarea'] ?? '');
            $sheet->setCellValue('F' . $rowNum, $row['descripcion'] ?? '');
            $sheet->setCellValue('G' . $rowNum, $row['responsable'] ?? '');
            $sheet->setCellValue('H' . $rowNum, $row['quien_solicita'] ?? '');
            $sheet->setCellValue('I' . $rowNum, $row['porcentaje_avance'] ?? 0);
            $sheet->setCellValue('J' . $rowNum, $row['estado'] ?? '');
            $rowNum++;
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = 'reporte_kanban_' . $desde . '_a_' . $hasta . '.xlsx';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public static function exportPdf($desde, $hasta) {
        if (!$desde || !$hasta) {
            return ['success' => false, 'mensaje' => 'Rango de fechas requerido'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        $stmt = $conexion->prepare(
            "SELECT t.id, t.nombre, t.tipo, t.estado, t.prioridad, t.porcentaje_avance,
                    t.fecha_vencimiento, t.responsable, p.nombre AS proyecto_nombre
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

        $cols = [
            'pendiente' => [],
            'en_progreso' => [],
            'completada' => [],
            'cancelada' => []
        ];
        while ($row = $result->fetch_assoc()) {
            $estado = $row['estado'] ?? 'pendiente';
            if (!isset($cols[$estado])) {
                $estado = 'pendiente';
            }
            $cols[$estado][] = $row;
        }

        $esc = function ($v) {
            return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
        };

        $html = '<html><head><style>
            body{font-family: sans-serif; font-size:10px; color:#222;}
            .title{font-size:14px; font-weight:700; margin-bottom:4px;}
            .sub{font-size:10px; color:#666; margin-bottom:10px;}
            .columna{float:left; width:23%; margin-right:2%; min-height:200px; border:1px solid #ccc; padding:5px;}
            .columna h3{font-size:12px; margin:0 0 5px 0; padding:3px; background:#f0f0f0;}
            .tarea{margin-bottom:8px; padding:4px; border:1px solid #eee; background:#fafafa;}
            .tarea .nombre{font-weight:700; font-size:11px;}
            .tarea .meta{font-size:9px; color:#666;}
            .clear{clear:both;}
        </style></head><body>
        <div class="title">Kanban Reporte</div>
        <div class="sub">Desde: ' . $esc($desde) . ' Hasta: ' . $esc($hasta) . '</div>';

        $estadoLabels = [
            'pendiente' => 'Pendiente',
            'en_progreso' => 'En Progreso',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada'
        ];

        foreach ($cols as $estado => $tareas) {
            $label = $estadoLabels[$estado] ?? $estado;
            $html .= '<div class="columna"><h3>' . $esc($label) . ' (' . count($tareas) . ')</h3>';
            foreach ($tareas as $t) {
                $html .= '<div class="tarea">
                    <div class="nombre">' . $esc($t['nombre']) . '</div>
                    <div class="meta">Proyecto: ' . $esc($t['proyecto_nombre'] ?? 'N/A') . '</div>
                    <div class="meta">Responsable: ' . $esc($t['responsable'] ?? 'N/A') . '</div>
                    <div class="meta">Vence: ' . $esc($t['fecha_vencimiento'] ?? 'N/A') . '</div>
                    <div class="meta">Avance: ' . $esc($t['porcentaje_avance'] ?? 0) . '%</div>
                </div>';
            }
            $html .= '</div>';
        }

        $html .= '<div class="clear"></div></body></html>';

        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $filename = 'kanban_' . $desde . '_a_' . $hasta . '.pdf';
        $mpdf->Output($filename, 'D');
        exit;
    }

    public static function updatePorcentaje($id, $porcentaje) {
        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();

        $porcentaje = (int)$porcentaje;
        if ($porcentaje < 0 || $porcentaje > 100) {
            return ['success' => false, 'mensaje' => 'Porcentaje inválido (0-100)'];
        }

        $stmtAntes = $conexion->prepare("SELECT porcentaje_avance, proyecto_id FROM tareas WHERE id=? AND (usuario_id=? OR usuario_id IS NULL) LIMIT 1");
        $stmtAntes->bind_param("ii", $id, $usuario_id);
        $stmtAntes->execute();
        $antes = $stmtAntes->get_result()->fetch_assoc();
        if (!$antes) return ['success' => false, 'mensaje' => 'Tarea no encontrada'];

        $stmt = $conexion->prepare("UPDATE tareas SET porcentaje_avance=?, fecha_actualizacion=NOW() WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
        $stmt->bind_param("iii", $porcentaje, $id, $usuario_id);
        $ok = $stmt->execute();

        if ($ok && $antes['porcentaje_avance'] != $porcentaje) {
            $autor = $_SESSION['usuario'] ?? 'Sistema';
            $desc = 'Cambio de porcentaje: ' . $antes['porcentaje_avance'] . '% -> ' . $porcentaje . '%';
            $b = $conexion->prepare("INSERT INTO bitacora (tarea_id, proyecto_id, tipo_registro, descripcion, autor) VALUES (?, ?, 'actualizacion', ?, ?)");
            $b->bind_param("iiss", $id, $antes['proyecto_id'], $desc, $autor);
            $b->execute();
        }

        return ['success' => $ok, 'mensaje' => $ok ? 'Porcentaje actualizado' : $conexion->error];
    }
}
