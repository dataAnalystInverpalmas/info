<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\TikaCalendario;
use App\Helpers\Auth;

class TikaCalendarioController
{
    private TikaCalendario $model;

    public function __construct()
    {
        $this->model = new TikaCalendario();
    }

    /**
     * Muestra la vista principal del CRUD.
     */
    public function index(): void
    {
        Auth::start();
        $anios = $this->model->getAniosRegistrados();
        $modelos = $this->model->getModelosRegistrados();
        $kpis = $this->model->getKpis();

        require_once dirname(__DIR__) . '/Views/TikaCalendario/index.php';
    }

    /**
     * Maneja las peticiones AJAX del CRUD.
     */
    public function handleAjax(): void
    {
        Auth::start();
        header('Content-Type: application/json; charset=utf-8');

        $action = trim((string)($_REQUEST['action'] ?? 'listar'));

        try {
            switch ($action) {
                case 'listar':
                    $filters = [
                        'anio'           => $_REQUEST['anio'] ?? '',
                        'modelo_tika_id' => $_REQUEST['modelo_tika_id'] ?? '',
                        'search'         => $_REQUEST['search'] ?? '',
                    ];
                    $data = $this->model->getList($filters);

                    echo json_encode([
                        'success' => true,
                        'data'    => $data,
                        'total'   => count($data)
                    ], JSON_UNESCAPED_UNICODE);
                    break;

                case 'guardar':
                    $post = $_POST;
                    $res = $this->model->save([
                        'id'             => !empty($post['id']) ? (int)$post['id'] : 0,
                        'semana'         => !empty($post['semana']) ? (int)$post['semana'] : 0,
                        'anio'           => !empty($post['anio']) ? (int)$post['anio'] : 0,
                        'modelo_tika_id' => isset($post['modelo_tika_id']) && $post['modelo_tika_id'] !== '' ? (int)$post['modelo_tika_id'] : null,
                        'fecha_inicio'   => !empty($post['fecha_inicio']) ? trim((string)$post['fecha_inicio']) : null,
                        'fecha_fin'      => !empty($post['fecha_fin']) ? trim((string)$post['fecha_fin']) : null,
                        'observaciones'  => trim((string)($post['observaciones'] ?? '')),
                    ]);
                    echo json_encode($res, JSON_UNESCAPED_UNICODE);
                    break;

                case 'guardar_modelo_inline':
                    $id = (int)($_POST['id'] ?? 0);
                    $modeloId = isset($_POST['modelo_tika_id']) && $_POST['modelo_tika_id'] !== '' ? (int)$_POST['modelo_tika_id'] : null;
                    if ($id <= 0) {
                        echo json_encode(['success' => false, 'error' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
                        break;
                    }
                    $ok = $this->model->updateModelo($id, $modeloId);
                    echo json_encode(['success' => $ok], JSON_UNESCAPED_UNICODE);
                    break;

                case 'generar_anio':
                    $anio = (int)($_POST['anio'] ?? 0);
                    $semanas = (int)($_POST['semanas'] ?? 52);
                    $modeloId = isset($_POST['modelo_tika_id']) && $_POST['modelo_tika_id'] !== '' ? (int)$_POST['modelo_tika_id'] : null;
                    $sobrescribir = !empty($_POST['sobrescribir']);

                    if ($anio <= 0) {
                        echo json_encode(['success' => false, 'error' => 'Debe indicar un año válido (ej. 2030)'], JSON_UNESCAPED_UNICODE);
                        break;
                    }

                    $res = $this->model->generarAnio($anio, $semanas, $modeloId, $sobrescribir);
                    echo json_encode($res, JSON_UNESCAPED_UNICODE);
                    break;

                case 'asignar_por_anio':
                    $anio = (int)($_POST['anio'] ?? 0);
                    $modeloId = isset($_POST['modelo_tika_id']) && $_POST['modelo_tika_id'] !== '' ? (int)$_POST['modelo_tika_id'] : null;
                    if ($anio <= 0) {
                        echo json_encode(['success' => false, 'error' => 'Año inválido'], JSON_UNESCAPED_UNICODE);
                        break;
                    }
                    $afectadas = $this->model->assignModeloByAnio($anio, $modeloId);
                    echo json_encode(['success' => true, 'afectadas' => $afectadas], JSON_UNESCAPED_UNICODE);
                    break;

                case 'asignacion_masiva':
                    $ids = $_POST['ids'] ?? [];
                    if (!is_array($ids)) {
                        $ids = explode(',', (string)$ids);
                    }
                    $modeloId = isset($_POST['modelo_tika_id']) && $_POST['modelo_tika_id'] !== '' ? (int)$_POST['modelo_tika_id'] : null;
                    $afectadas = $this->model->bulkAssignModelo($ids, $modeloId);
                    echo json_encode(['success' => true, 'afectadas' => $afectadas], JSON_UNESCAPED_UNICODE);
                    break;

                case 'sincronizar_dates':
                    $desde = (int)($_POST['desde'] ?? 2401);
                    $hasta = (int)($_POST['hasta'] ?? 2952);
                    $res = $this->model->sincronizarDesdeDates($desde, $hasta);
                    echo json_encode($res, JSON_UNESCAPED_UNICODE);
                    break;

                case 'eliminar_semana':
                    $id = (int)($_POST['id'] ?? 0);
                    if ($id <= 0) {
                        echo json_encode(['success' => false, 'error' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
                        break;
                    }
                    $ok = $this->model->delete($id);
                    echo json_encode(['success' => $ok], JSON_UNESCAPED_UNICODE);
                    break;

                case 'eliminar_anio':
                    $anio = (int)($_POST['anio'] ?? 0);
                    if ($anio <= 0) {
                        echo json_encode(['success' => false, 'error' => 'Año inválido'], JSON_UNESCAPED_UNICODE);
                        break;
                    }
                    $afectadas = $this->model->deleteByAnio($anio);
                    echo json_encode(['success' => true, 'afectadas' => $afectadas], JSON_UNESCAPED_UNICODE);
                    break;

                case 'kpis':
                    $kpis = $this->model->getKpis();
                    $anios = $this->model->getAniosRegistrados();
                    echo json_encode([
                        'success' => true,
                        'kpis'    => $kpis,
                        'anios'   => $anios
                    ], JSON_UNESCAPED_UNICODE);
                    break;

                default:
                    echo json_encode(['success' => false, 'error' => 'Acción no soportada'], JSON_UNESCAPED_UNICODE);
                    break;
            }
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
