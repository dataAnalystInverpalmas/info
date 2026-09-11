<?php
namespace App\Controllers;

use App\Models\Proyecto;

class ProyectoController {
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public function index() {
        $usuario_id = $_SESSION['id'] ?? null;
        $proyectos = Proyecto::getAll($usuario_id);
        $categorias = Proyecto::getCategorias($usuario_id);
        extract(['proyectos' => $proyectos, 'categorias' => $categorias]);
        require_once __DIR__ . '/../Views/Proyectos/index.php';
    }

    // ── Métodos estáticos para AJAX ───────────────────────────

    public static function listar() {
        $usuario_id = $_SESSION['id'] ?? null;
        return Proyecto::getAll($usuario_id);
    }

    public static function obtener($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        return Proyecto::getById($id, $usuario_id);
    }

    public static function estadisticas($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        $stats = Proyecto::getEstadisticas((int)$id, $usuario_id);
        if (!$stats) {
            return ['success' => false, 'mensaje' => 'Proyecto no encontrado'];
        }
        return ['success' => true, 'data' => $stats];
    }

    public static function crear() {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $nombre = trim($input['nombre'] ?? '');
        $datos = self::datosProyecto($input);

        if (empty($nombre)) {
            return ['success' => false, 'mensaje' => 'Nombre requerido'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        $stmt = $conexion->prepare("INSERT INTO proyectos (nombre, tipo, area_solicitante, problema_negocio, descripcion, objetivo_alcance, responsable_proyecto, categoria, prioridad, estado, fecha_inicio, fecha_fin, entregable_periodo, riesgo_principal, proximo_hito, criterio_exito, stakeholders, link_evidencias, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $valores = array_merge([$nombre], $datos, [$usuario_id]);
        $stmt->bind_param(str_repeat('s', count($valores) - 1) . 'i', ...$valores);
        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Proyecto creado' : $conexion->error, 'id' => $conexion->insert_id];
    }

    public static function actualizar($id) {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $nombre = trim($input['nombre'] ?? '');
        $datos = self::datosProyecto($input);

        if (empty($nombre)) {
            return ['success' => false, 'mensaje' => 'Nombre requerido'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        $stmt = $conexion->prepare("UPDATE proyectos SET nombre=?, tipo=?, area_solicitante=?, problema_negocio=?, descripcion=?, objetivo_alcance=?, responsable_proyecto=?, categoria=?, prioridad=?, estado=?, fecha_inicio=?, fecha_fin=?, entregable_periodo=?, riesgo_principal=?, proximo_hito=?, criterio_exito=?, stakeholders=?, link_evidencias=? WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $valores = array_merge([$nombre], $datos, [$id, $usuario_id]);
        $stmt->bind_param(str_repeat('s', count($valores) - 2) . 'ii', ...$valores);
        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Proyecto actualizado' : $conexion->error];
    }

    private static function datosProyecto(array $input): array {
        $valor = static function ($nombre) use ($input) {
            $resultado = trim((string)($input[$nombre] ?? ''));
            return $resultado === '' ? null : $resultado;
        };
        $estado = $input['estado'] ?? 'activo';
        if (!in_array($estado, ['activo', 'pausado', 'completado', 'cancelado'], true)) {
            $estado = 'activo';
        }
        $prioridad = $input['prioridad'] ?? 'media';
        if (!in_array($prioridad, ['baja', 'media', 'alta', 'urgente'], true)) {
            $prioridad = 'media';
        }
        return [
            $valor('tipo'), $valor('area_solicitante'), $valor('problema_negocio'), $valor('descripcion'),
            $valor('objetivo_alcance'), $valor('responsable_proyecto'), $valor('categoria'), $prioridad,
            $estado, $valor('fecha_inicio'), $valor('fecha_fin'), $valor('entregable_periodo'),
            $valor('riesgo_principal'), $valor('proximo_hito'), $valor('criterio_exito'),
            $valor('stakeholders'), $valor('link_evidencias')
        ];
    }

    public static function eliminar($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        $stmt = $conexion->prepare("DELETE FROM proyectos WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $stmt->bind_param("ii", $id, $usuario_id);
        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Proyecto eliminado' : $conexion->error];
    }

    public static function exportarExcel(): void {
        $plantilla = dirname(__DIR__, 2) . '/Gestion_Proyectos_TI_Hilder.xlsx';
        if (!is_file($plantilla)) {
            http_response_code(404);
            echo 'No se encontró la plantilla de gestión de proyectos.';
            return;
        }

        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

        $usuario_id = $_SESSION['id'] ?? null;
        $proyectos  = Proyecto::getParaExportarExcel($usuario_id);
        $tareas     = array_filter(
            \App\Models\Tarea::getParaExportarExcel($usuario_id),
            fn($t) => !empty($t->proyecto_id)
        );
        $riesgos    = \App\Models\ProyectoRiesgo::getParaExportarExcel($usuario_id);
        $solicitudes = \App\Models\SolicitudExtra::getAll($usuario_id);
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($plantilla);

        self::limpiarFilas($spreadsheet->getSheetByName('Proyectos'), 4, 48, 'R');
        self::limpiarFilas($spreadsheet->getSheetByName('Cronograma'), 4, 32, 'N');
        self::limpiarFilas($spreadsheet->getSheetByName('Riesgos_Bloqueos'), 4, 32, 'L');
        self::limpiarFilas($spreadsheet->getSheetByName('Solicitudes_Extra'), 4, 76, 'J');

        $fila = 4;
        foreach ($proyectos as $proyecto) {
            $hoja = $spreadsheet->getSheetByName('Proyectos');
            $hoja->fromArray([
                $proyecto->id,
                $proyecto->nombre,
                $proyecto->tipo ?? 'Proyecto',
                $proyecto->area_solicitante ?? $proyecto->categoria ?? '',
                $proyecto->problema_negocio ?? $proyecto->descripcion ?? '',
                $proyecto->objetivo_alcance ?? $proyecto->descripcion ?? '',
                $proyecto->responsable_proyecto ?? $_SESSION['usuario'] ?? '',
                ucfirst($proyecto->prioridad ?? 'media'),
                null,
                null,
                ((int)($proyecto->avance_proyecto ?? 0)) / 100,
                self::estadoProyectoExcel($proyecto->estado ?? ''),
                $proyecto->entregable_periodo ?? '',
                $proyecto->riesgo_principal ?? '',
                $proyecto->proximo_hito ?? '',
                $proyecto->criterio_exito ?? '',
                $proyecto->stakeholders ?? '',
                $proyecto->link_evidencias ?? ''
            ], null, 'A' . $fila);
            self::asignarFecha($hoja, 'I' . $fila, $proyecto->fecha_inicio ?? null);
            self::asignarFecha($hoja, 'J' . $fila, $proyecto->fecha_fin ?? null);
            $fila++;
        }
        self::aplicarEstiloTabla($spreadsheet->getSheetByName('Proyectos'), 4, $fila - 1, 'R');
        self::corregirFormatoCondicional($spreadsheet->getSheetByName('Proyectos'));
        if ($fila - 1 >= 4) {
            $spreadsheet->getSheetByName('Proyectos')->getStyle('K4:K' . ($fila - 1))->getNumberFormat()->setFormatCode('0%');
        }

        $fila = 4;
        foreach ($tareas as $tarea) {
            $hoja = $spreadsheet->getSheetByName('Cronograma');
            $hoja->fromArray([
                $tarea->proyecto_nombre ?? 'Sin proyecto',
                $tarea->etapa_fase ?? '',
                $tarea->orden_ejecucion ?? '',
                $tarea->nombre,
                $tarea->responsable ?? '',
                null,
                null,
                null,
                ((int)($tarea->porcentaje_avance ?? 0)) / 100,
                self::estadoTareaExcel($tarea->estado ?? ''),
                $tarea->entregable_concreto ?? $tarea->descripcion ?? '',
                $tarea->evidencia_soporte ?? '',
                $tarea->observaciones ?? '',
                $tarea->dependencia ?? ''
            ], null, 'A' . $fila);
            self::asignarFecha($hoja, 'F' . $fila, $tarea->fecha_inicio ?? null);
            self::asignarFecha($hoja, 'G' . $fila, $tarea->fecha_vencimiento ?? null);
            self::asignarFecha($hoja, 'H' . $fila, ($tarea->estado ?? '') === 'completada' ? ($tarea->fecha_actualizacion ?? null) : null);
            $fila++;
        }
        self::aplicarEstiloTabla($spreadsheet->getSheetByName('Cronograma'), 4, $fila - 1, 'N', 131.25);
        self::corregirFormatoCondicional($spreadsheet->getSheetByName('Cronograma'));
        if ($fila - 1 >= 4) {
            $spreadsheet->getSheetByName('Cronograma')->getStyle('I4:I' . ($fila - 1))->getNumberFormat()->setFormatCode('0%');
        }

        self::actualizarResumenDashboard($spreadsheet, count($proyectos));
        self::corregirFormatoCondicional($spreadsheet->getSheetByName('Dashboard'));

        $fila = 4;
        foreach ($riesgos as $r) {
            $hoja = $spreadsheet->getSheetByName('Riesgos_Bloqueos');
            $hoja->fromArray([
                $r->proyecto_nombre ?? '',
                ucfirst($r->probabilidad ?? ''),
                $r->descripcion,
                ucfirst($r->probabilidad ?? ''),
                ucfirst($r->impacto ?? ''),
                $r->responsable ?? '',
                '',
                $r->plan_mitigacion ?? '',
                null,
                null,
                self::estadoRiesgoExcel($r->estado ?? ''),
                ''
            ], null, 'A' . $fila);
            self::asignarFecha($hoja, 'I' . $fila, null);
            self::asignarFecha($hoja, 'J' . $fila, $r->fecha_compromiso ?? null);
            $fila++;
        }
        self::aplicarEstiloTabla($spreadsheet->getSheetByName('Riesgos_Bloqueos'), 4, $fila - 1, 'L');
        self::corregirFormatoCondicional($spreadsheet->getSheetByName('Riesgos_Bloqueos'));

        $fila = 4;
        foreach ($solicitudes as $s) {
            $hoja = $spreadsheet->getSheetByName('Solicitudes_Extra');
            $hoja->fromArray([
                null,
                $s->solicitante ?? '',
                $s->area ?? '',
                $s->descripcion,
                $s->tipo ?? '',
                $s->tiempo_invertido_horas ?? '',
                $s->responsable ?? '',
                self::estadoTareaExcel($s->estado ?? ''),
                $s->se_convirtio_en_proyecto ? 'Sí' : 'No',
                $s->observaciones ?? ''
            ], null, 'A' . $fila);
            self::asignarFecha($hoja, 'A' . $fila, $s->fecha ?? null);
            $fila++;
        }
        self::aplicarEstiloTabla($spreadsheet->getSheetByName('Solicitudes_Extra'), 4, $fila - 1, 'J');
        self::corregirFormatoCondicional($spreadsheet->getSheetByName('Solicitudes_Extra'));

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $nombre = 'gestion_proyectos_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Cache-Control: max-age=0');

        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    private static function estadoRiesgoExcel(string $estado): string {
        $estados = [
            'abierto' => 'Abierto',
            'en_seguimiento' => 'En seguimiento',
            'mitigado' => 'Mitigado',
            'cerrado' => 'Cerrado'
        ];
        return $estados[$estado] ?? $estado;
    }

    private static function limpiarFilas($hoja, int $inicio, int $fin, string $ultimaColumna): void {
        if (!$hoja) {
            return;
        }

        foreach ($hoja->getMergeCells() as $rango) {
            $piezas = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::splitRange($rango);
            $celdaInicio = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($piezas[0][0]);
            $celdaFin    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($piezas[0][1]);
            $filaInicio  = (int)$celdaInicio[1];
            $filaFin     = (int)$celdaFin[1];

            if ($filaInicio >= $inicio && $filaFin <= $fin) {
                $hoja->unmergeCells($rango);
            }
        }

        for ($fila = $inicio; $fila <= $fin; $fila++) {
            for ($columna = 'A'; $columna <= $ultimaColumna; $columna++) {
                $hoja->setCellValue($columna . $fila, null);
            }
        }
    }

    private static function aplicarEstiloTabla($hoja, int $inicio, int $fin, string $ultimaColumna, ?float $alturaFila = null): void {
        if (!$hoja || $fin < $inicio) {
            return;
        }

        for ($fila = $inicio; $fila <= $fin; $fila++) {
            $rango = 'A' . $fila . ':' . $ultimaColumna . $fila;
            $hoja->getStyle($rango)->applyFromArray([
                'font' => [
                    'name' => 'Arial',
                    'size' => 9
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'BFBFBF']
                    ]
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => (($fila - $inicio) % 2 === 0) ? 'FFFFFFFF' : 'FFF2F2F2']
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ]);
            if ($alturaFila !== null) {
                $hoja->getRowDimension($fila)->setRowHeight($alturaFila);
            }
        }
    }

    private static function corregirFormatoCondicional($hoja): void {
        if (!$hoja) {
            return;
        }

        foreach ($hoja->getConditionalStylesCollection() as $reglas) {
            foreach ($reglas as $regla) {
                $estilo = $regla->getStyle();
                if (!$estilo) {
                    continue;
                }

                $relleno = $estilo->getFill();
                $color = $relleno->getEndColor()->getARGB();
                if (!preg_match('/^FF[0-9A-Fa-f]{6}$/', (string)$color)) {
                    $color = $relleno->getStartColor()->getARGB();
                }
                if (!preg_match('/^FF[0-9A-Fa-f]{6}$/', (string)$color)) {
                    continue;
                }

                $relleno->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $relleno->getStartColor()->setARGB($color);
                $relleno->getEndColor()->setARGB($color);
                $estilo->getFont()->setSize(9);
            }
        }
    }

    private static function asignarFecha($hoja, string $celda, ?string $fecha): void {
        if (!$hoja || !$fecha) {
            return;
        }

        try {
            $valor = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(new \DateTimeImmutable($fecha));
            $hoja->setCellValue($celda, $valor);
            $hoja->getStyle($celda)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        } catch (\Throwable $exception) {
            $hoja->setCellValue($celda, $fecha);
        }
    }

    private static function estadoProyectoExcel(string $estado): string {
        $estados = [
            'activo' => 'En curso',
            'pausado' => 'En pausa',
            'completado' => 'Finalizado',
            'cancelado' => 'Cancelado'
        ];
        return $estados[$estado] ?? $estado;
    }

    private static function estadoTareaExcel(string $estado): string {
        $estados = [
            'pendiente' => 'No iniciado',
            'en_progreso' => 'En curso',
            'completada' => 'Finalizado',
            'cancelada' => 'Cancelado'
        ];
        return $estados[$estado] ?? $estado;
    }

    private static function actualizarResumenDashboard($spreadsheet, int $totalProyectos): void {
        $hoja = $spreadsheet->getSheetByName('Dashboard');
        if (!$hoja) {
            return;
        }

        $ultimaFila = max(48, $totalProyectos + 3);
        $hoja->setCellValue('A7', '=COUNTA(Proyectos!B4:B' . $ultimaFila . ')');
        $hoja->setCellValue('C7', '=COUNTIF(Proyectos!L4:L' . $ultimaFila . ',"En curso")');
        $hoja->setCellValue('E7', '=COUNTIF(Proyectos!L4:L' . $ultimaFila . ',"Finalizado")');
        $hoja->setCellValue('G7', '=COUNTIF(Proyectos!L4:L' . $ultimaFila . ',"En riesgo")+COUNTIF(Proyectos!L4:L' . $ultimaFila . ',"Bloqueado")');
        $hoja->setCellValue('I7', '=COUNTIF(Proyectos!L4:L' . $ultimaFila . ',"En pausa")');
        $hoja->setCellValue('K7', '=IFERROR(AVERAGE(Proyectos!K4:K' . $ultimaFila . '),0)');
    }

    public static function handleRequest(array $server, array $query): void {
        try {
            $metodo = $server['REQUEST_METHOD'] ?? 'GET';
            $accion = $query['accion'] ?? null;
            $id = $query['id'] ?? null;

            if ($metodo === 'GET' && $accion === 'exportar_excel') {
                self::exportarExcel();
                return;
            }

            self::sendJsonHeader();

            switch ($metodo) {
                case 'GET':
                    if ($accion === 'estadisticas' && $id) {
                        self::respond(self::estadisticas($id));
                        return;
                    }

                    self::respond($id ? self::obtener($id) : self::listar());
                    return;

                case 'POST':
                    self::respond(self::crear());
                    return;

                case 'PUT':
                    if (!$id) {
                        self::respond(['error' => 'ID es requerido']);
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
