<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../funciones/conexion.php');
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if (!in_array($accion, ['export_excel', 'export_pdf'], true)) {
    header('Content-Type: application/json; charset=utf-8');
}

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
        echo json_encode(['success' => true, 'tareas' => $data]);
        break;

    case 'export_excel':
        $desde = $_GET['desde'] ?? $_POST['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? $_POST['hasta'] ?? '';

        if (!$desde || !$hasta) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'mensaje' => 'Rango de fechas requerido']);
            exit;
        }

        $usuario_id = $_SESSION['id'] ?? null;
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

        $spreadsheet = new Spreadsheet();
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

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;

    case 'export_pdf':
        $desde = $_GET['desde'] ?? $_POST['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? $_POST['hasta'] ?? '';

        if (!$desde || !$hasta) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'mensaje' => 'Rango de fechas requerido']);
            exit;
        }

        $usuario_id = $_SESSION['id'] ?? null;
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
            .board{width:100%; border-collapse:separate; border-spacing:6px 0; table-layout:fixed;}
            .head{font-size:10px; font-weight:700; text-align:center; padding:6px; border:1px solid #d8dee8; background:#f4f6fb;}
            .cell{vertical-align:top; border:1px solid #e5e7eb; background:#fafafa; padding:6px;}
            .card{border:1px solid #d9dde4; border-left:3px solid #4b5563; border-radius:4px; background:#fff; padding:5px; margin-bottom:6px;}
            .t{font-weight:700; font-size:10px; margin-bottom:3px;}
            .m{font-size:9px; color:#555;}
            .a{float:right; font-size:9px; font-weight:700; color:#1f2937;}
            .prio-urgente{border-left-color:#dc3545}.prio-alta{border-left-color:#fd7e14}.prio-media{border-left-color:#ffc107}.prio-baja{border-left-color:#6c757d}
        </style></head><body>';
        $html .= '<div class="title">Tablero Kanban</div>';
        $html .= '<div class="sub">Periodo: ' . $esc($desde) . ' a ' . $esc($hasta) . '</div>';
        $html .= '<table class="board"><tr>';
        $html .= '<td class="head">Pendiente (' . count($cols['pendiente']) . ')</td>';
        $html .= '<td class="head">En Progreso (' . count($cols['en_progreso']) . ')</td>';
        $html .= '<td class="head">Completada (' . count($cols['completada']) . ')</td>';
        $html .= '<td class="head">Cancelada (' . count($cols['cancelada']) . ')</td>';
        $html .= '</tr><tr>';

        foreach (['pendiente', 'en_progreso', 'completada', 'cancelada'] as $estadoCol) {
            $html .= '<td class="cell">';
            foreach ($cols[$estadoCol] as $t) {
                $prio = $t['prioridad'] ?? 'baja';
                $html .= '<div class="card prio-' . $esc($prio) . '">';
                $html .= '<div class="t">' . $esc($t['nombre'] ?? '') . '<span class="a">' . (int)($t['porcentaje_avance'] ?? 0) . '%</span></div>';
                $html .= '<div class="m">' . $esc($t['tipo'] ?? 'prevista') . '</div>';
                if (!empty($t['proyecto_nombre'])) {
                    $html .= '<div class="m">Proyecto: ' . $esc($t['proyecto_nombre']) . '</div>';
                }
                if (!empty($t['responsable'])) {
                    $html .= '<div class="m">Resp.: ' . $esc($t['responsable']) . '</div>';
                }
                if (!empty($t['fecha_vencimiento'])) {
                    $html .= '<div class="m">Vence: ' . $esc($t['fecha_vencimiento']) . '</div>';
                }
                $html .= '</div>';
            }
            $html .= '</td>';
        }

        $html .= '</tr></table></body></html>';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $mpdf = new Mpdf(['format' => 'A4', 'orientation' => 'P']);
        $mpdf->WriteHTML($html);
        $filename = 'tablero_kanban_' . $desde . '_a_' . $hasta . '.pdf';
        $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
        exit;

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
        $stmtAntes = $conexion->prepare("SELECT estado, proyecto_id, porcentaje_avance FROM tareas WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL) LIMIT 1");
        $stmtAntes->bind_param("ii", $id, $uid);
        $stmtAntes->execute();
        $antes = $stmtAntes->get_result()->fetch_assoc() ?? [];

        $porcentaje_avance = isset($antes['porcentaje_avance']) ? (int)$antes['porcentaje_avance'] : 0;
        if (($antes['estado'] ?? '') !== $estado) {
            if ($estado === 'pendiente') {
                $porcentaje_avance = 0;
            } elseif ($estado === 'en_progreso') {
                $porcentaje_avance = 25;
            } elseif ($estado === 'completada') {
                $porcentaje_avance = 100;
            }
        }

        $stmt = $conexion->prepare("UPDATE tareas SET estado = ?, porcentaje_avance = ? WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL)");
        $stmt->bind_param("siii", $estado, $porcentaje_avance, $id, $uid);
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

        echo json_encode([
            'success' => $ok,
            'mensaje' => $ok ? 'Estado actualizado' : $conexion->error,
            'porcentaje_avance' => $porcentaje_avance
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Acción inválida']);
        break;
}
