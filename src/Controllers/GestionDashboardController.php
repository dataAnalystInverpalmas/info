<?php
namespace App\Controllers;

use App\Models\Bitacora;
use App\Models\Proyecto;
use App\Models\Tarea;

class GestionDashboardController {

    public function index() {
        $usuario_id = $_SESSION['id'] ?? null;
        $proyectos = Proyecto::getAll($usuario_id);
        $tareas = Tarea::getAll($usuario_id);
        $registros = Bitacora::getAll($usuario_id);

        $dashboard = $this->buildDashboardData($proyectos, $tareas, $registros);

        extract([
            'dashboard' => $dashboard,
            'proyectos' => $proyectos,
            'tareas' => $tareas,
            'registros' => $registros,
        ]);

        require_once __DIR__ . '/../Views/GestionDashboard/index.php';
    }

    private function buildDashboardData(array $proyectos, array $tareas, array $registros): array {
        $hoy = new \DateTimeImmutable('today');

        $resumen = [
            'total_proyectos' => count($proyectos),
            'proyectos_activos' => 0,
            'avance_promedio' => 0,
            'total_tareas' => count($tareas),
            'tareas_pendientes' => 0,
            'tareas_en_progreso' => 0,
            'tareas_completadas' => 0,
            'tareas_canceladas' => 0,
            'tareas_atrasadas' => 0,
            'tareas_sin_proyecto' => 0,
            'tareas_proximas' => 0,
            'imprevistas' => 0,
        ];

        $sumaAvance = 0;
        $proyectosConAvance = 0;
        $proyectosMapa = [];

        foreach ($proyectos as $proyecto) {
            $proyectosMapa[(int)$proyecto->id] = [
                'id' => (int)$proyecto->id,
                'nombre' => (string)$proyecto->nombre,
                'categoria' => (string)($proyecto->categoria ?? ''),
                'estado' => (string)($proyecto->estado ?? 'activo'),
                'avance' => (int)($proyecto->avance_proyecto ?? 0),
                'total' => 0,
                'pendientes' => 0,
                'en_progreso' => 0,
                'completadas' => 0,
                'canceladas' => 0,
                'atrasadas' => 0,
                'proximas' => 0,
            ];

            if (($proyecto->estado ?? '') === 'activo') {
                $resumen['proyectos_activos']++;
            }

            $sumaAvance += (int)($proyecto->avance_proyecto ?? 0);
            $proyectosConAvance++;
        }

        foreach ($tareas as $tarea) {
            $estado = (string)($tarea->estado ?? 'pendiente');
            $proyectoId = isset($tarea->proyecto_id) ? (int)$tarea->proyecto_id : 0;
            $fechaVencimiento = trim((string)($tarea->fecha_vencimiento ?? ''));
            $esAtrasada = false;
            $esProxima = false;

            if ($fechaVencimiento !== '' && $estado !== 'completada' && $estado !== 'cancelada') {
                try {
                    $vence = new \DateTimeImmutable($fechaVencimiento);
                    if ($vence < $hoy) {
                        $esAtrasada = true;
                    } elseif ($vence <= $hoy->modify('+7 days')) {
                        $esProxima = true;
                    }
                } catch (\Exception $exception) {
                }
            }

            switch ($estado) {
                case 'en_progreso':
                    $resumen['tareas_en_progreso']++;
                    break;
                case 'completada':
                    $resumen['tareas_completadas']++;
                    break;
                case 'cancelada':
                    $resumen['tareas_canceladas']++;
                    break;
                default:
                    $resumen['tareas_pendientes']++;
                    break;
            }

            if (($tarea->tipo ?? '') === 'imprevista') {
                $resumen['imprevistas']++;
            }

            if ($proyectoId <= 0) {
                $resumen['tareas_sin_proyecto']++;
            }

            if ($esAtrasada) {
                $resumen['tareas_atrasadas']++;
            }

            if ($esProxima) {
                $resumen['tareas_proximas']++;
            }

            if ($proyectoId > 0 && isset($proyectosMapa[$proyectoId])) {
                $proyectosMapa[$proyectoId]['total']++;
                if ($estado === 'en_progreso') {
                    $proyectosMapa[$proyectoId]['en_progreso']++;
                } elseif ($estado === 'completada') {
                    $proyectosMapa[$proyectoId]['completadas']++;
                } elseif ($estado === 'cancelada') {
                    $proyectosMapa[$proyectoId]['canceladas']++;
                } else {
                    $proyectosMapa[$proyectoId]['pendientes']++;
                }

                if ($esAtrasada) {
                    $proyectosMapa[$proyectoId]['atrasadas']++;
                }
                if ($esProxima) {
                    $proyectosMapa[$proyectoId]['proximas']++;
                }
            }
        }

        $resumen['avance_promedio'] = $proyectosConAvance > 0
            ? (int)round($sumaAvance / $proyectosConAvance)
            : 0;

        $proyectosDestacados = array_values($proyectosMapa);
        usort($proyectosDestacados, static function (array $left, array $right): int {
            $scoreLeft = ($left['pendientes'] * 3) + ($left['en_progreso'] * 2) + ($left['atrasadas'] * 4);
            $scoreRight = ($right['pendientes'] * 3) + ($right['en_progreso'] * 2) + ($right['atrasadas'] * 4);

            if ($scoreLeft === $scoreRight) {
                return strcmp($left['nombre'], $right['nombre']);
            }

            return $scoreRight <=> $scoreLeft;
        });
        $proyectosDestacados = array_slice($proyectosDestacados, 0, 6);

        $actividadReciente = array_slice($registros, 0, 8);

        $alertas = [];
        foreach ($tareas as $tarea) {
            $estado = (string)($tarea->estado ?? 'pendiente');
            $fechaVencimiento = trim((string)($tarea->fecha_vencimiento ?? ''));
            if ($fechaVencimiento === '' || $estado === 'completada' || $estado === 'cancelada') {
                continue;
            }
            try {
                $vence = new \DateTimeImmutable($fechaVencimiento);
            } catch (\Exception $exception) {
                continue;
            }

            if ($vence < $hoy || $vence <= $hoy->modify('+7 days')) {
                $alertas[] = [
                    'nombre' => (string)$tarea->nombre,
                    'proyecto' => (string)($tarea->proyecto_nombre ?? 'Sin proyecto'),
                    'estado' => $estado,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'atrasada' => $vence < $hoy,
                ];
            }
        }

        usort($alertas, static function (array $left, array $right): int {
            if ($left['atrasada'] !== $right['atrasada']) {
                return $left['atrasada'] ? -1 : 1;
            }
            return strcmp($left['fecha_vencimiento'], $right['fecha_vencimiento']);
        });

        return [
            'resumen' => $resumen,
            'proyectos_destacados' => $proyectosDestacados,
            'actividad_reciente' => $actividadReciente,
            'alertas' => array_slice($alertas, 0, 8),
        ];
    }
}