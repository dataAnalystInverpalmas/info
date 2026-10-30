<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ComparadorTika;
use Throwable;

class ComparadorTikaController extends BaseController
{
    protected function isAuthenticated(): bool
    {
        if (parent::isAuthenticated()) {
            return true;
        }
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        $secret = getenv('API_SECRET_TOKEN') ?: '';
        if ($secret !== '' && preg_match('/^Bearer\s+(.+)$/i', $authHeader, $m)) {
            return hash_equals($secret, trim($m[1]));
        }
        return false;
    }

    /**
     * Renderiza la página principal del Comparador de Escenarios Tika dentro del layout del sitio.
     */
    public function index(): void
    {
        $this->requireAuth();

        $modelosDisponibles = ComparadorTika::listarModelos(true);
        $redDisponible = ComparadorTika::isNetworkPathAccessible();
        $rutaRed = ComparadorTika::getNetworkBasePath();

        // Modelos solicitados por URL (?modelos=547,548)
        $modelosParam = trim((string)$this->get('modelos', ''));
        $modelosSeleccionados = [];
        if ($modelosParam !== '') {
            foreach (explode(',', $modelosParam) as $part) {
                $part = trim($part);
                if ($part !== '' && ctype_digit($part) && (int)$part > 0) {
                    $modelosSeleccionados[] = (int)$part;
                }
            }
            $modelosSeleccionados = array_values(array_unique($modelosSeleccionados));
        }

        // Si no se especificaron modelos en la URL, preseleccionar hasta los 2 más recientes cargados
        if (empty($modelosSeleccionados) && !empty($modelosDisponibles)) {
            $idsCargados = array_column($modelosDisponibles, 'id');
            // Ordenar ascendente para que el menor sea base por defecto (ej. 547 y 548)
            $slice = array_slice($idsCargados, 0, min(2, count($idsCargados)));
            sort($slice, SORT_NUMERIC);
            $modelosSeleccionados = $slice;
        }

        $datosIniciales = !empty($modelosSeleccionados)
            ? ComparadorTika::obtenerDatosComparador($modelosSeleccionados)
            : null;

        $this->render('ComparadorTika/index', [
            'modelosDisponibles'   => $modelosDisponibles,
            'modelosSeleccionados' => $modelosSeleccionados,
            'datosIniciales'       => $datosIniciales,
            'redDisponible'        => $redDisponible,
            'rutaRed'              => $rutaRed,
            'abrirModalImportar'   => ($this->get('report') === 'importar_tika' || $this->get('importar') === '1'),
        ]);
    }

    /**
     * GET /api/tika/modelos
     * Lista los modelos cargados en la base de datos (id, escenario, fecha y totales).
     */
    public function apiModelos(): void
    {
        $this->requireAuth();

        if (!$this->isGet()) {
            $this->jsonError('Método no permitido', 405);
        }

        try {
            $conTotales = $this->get('totales', '1') !== '0';
            $modelos = ComparadorTika::listarModelos($conTotales);
            $this->sendCompressedJson($modelos, 200);
        } catch (Throwable $e) {
            $this->jsonError('Error al listar modelos Tika: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/tika/comparador?modelos=547,499,548
     * Devuelve JSON con la estructura exacta esperada por DATOS en el comparador.
     */
    public function apiComparador(): void
    {
        $this->requireAuth();

        if (!$this->isGet()) {
            $this->jsonError('Método no permitido', 405);
        }

        $rawModelos = trim((string)$this->get('modelos', ''));
        if ($rawModelos === '') {
            $this->jsonError('Debe indicar al menos un ID de modelo en el parámetro ?modelos= (ej. ?modelos=547,548)', 400);
        }

        $parts = array_filter(array_map('trim', explode(',', $rawModelos)), static fn($v) => $v !== '');
        if (empty($parts)) {
            $this->jsonError('Parámetro ?modelos= vacío', 400);
        }

        $ids = [];
        foreach ($parts as $part) {
            if (!ctype_digit($part) || (int)$part <= 0) {
                $this->jsonError("ID de modelo inválido: '{$part}'. Todos los IDs deben ser números enteros positivos.", 400);
            }
            $idInt = (int)$part;
            if (!in_array($idInt, $ids, true)) {
                $ids[] = $idInt;
            }
        }

        try {
            $datos = ComparadorTika::obtenerDatosComparador($ids);
            $this->sendCompressedJson($datos, 200);
        } catch (Throwable $e) {
            $this->jsonError('Error al obtener datos del comparador: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/tika/importar
     * Importa uno o varios modelos desde la ruta de red o mediante subida de los 6 CSV.
     */
    public function apiImportar(): void
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->jsonError('Método no permitido. Use POST.', 405);
        }

        // Dar margen de tiempo y memoria para procesar los ~160k registros de proy1-3
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $contentLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
        if ($contentLength > 0 && empty($_POST) && empty($_FILES)) {
            $postMax = (string)ini_get('post_max_size');
            $this->jsonError(
                "El tamaño total de los archivos subidos (" . round($contentLength / 1048576, 1) . " MB) supera el límite permitido por el servidor (post_max_size = {$postMax}).",
                413
            );
        }

        $usuario = $this->userName() ?: 'sistema';
        $input = $this->jsonInput();
        $modo = (string)($this->input('modo', $input['modo'] ?? 'red'));

        try {
            if ($modo === 'archivos' || !empty($_FILES)) {
                $modeloIdRaw = trim((string)($this->input('modelo_id', $input['modelo_id'] ?? '')));
                if ($modeloIdRaw === '' || !ctype_digit($modeloIdRaw) || (int)$modeloIdRaw <= 0) {
                    $this->jsonError('Debe especificar un ID de modelo numérico válido (ej. 547).', 400);
                }
                $modeloId = (int)$modeloIdRaw;

                $filesMap = $this->extractUploadedCsvMap();
                $resultado = ComparadorTika::importFromFiles($modeloId, $filesMap, $usuario, 'upload_formulario');

                $this->jsonSuccess([
                    'importados' => [$resultado],
                    'errores'    => [],
                    'modelos'    => ComparadorTika::listarModelos(true),
                ], "Modelo {$modeloId} importado correctamente desde archivos subidos.");
            }

            // Modo ruta de red: recibir uno o varios IDs separados por coma, espacio o salto de línea
            $rawIds = (string)($this->post('ids', $input['ids'] ?? $this->post('modelos', $input['modelos'] ?? '')));
            $parts = preg_split('/[\s,;]+/', trim($rawIds)) ?: [];
            $ids = [];
            foreach ($parts as $p) {
                $p = trim($p);
                if ($p === '') {
                    continue;
                }
                if (!ctype_digit($p) || (int)$p <= 0) {
                    $this->jsonError("El ID '{$p}' no es válido. Ingrese únicamente números enteros (ej. 547, 548).", 400);
                }
                $idInt = (int)$p;
                if (!in_array($idInt, $ids, true)) {
                    $ids[] = $idInt;
                }
            }

            if (empty($ids)) {
                $this->jsonError('Ingrese al menos un ID de modelo para importar (ej. 547, 548).', 400);
            }

            $importados = [];
            $errores = [];
            foreach ($ids as $id) {
                try {
                    $importados[] = ComparadorTika::importFromNetwork($id, $usuario);
                } catch (Throwable $ex) {
                    $errores[] = [
                        'id'      => $id,
                        'message' => $ex->getMessage(),
                    ];
                }
            }

            if (empty($importados) && !empty($errores)) {
                $msg = implode(' | ', array_map(static fn($e) => "Modelo {$e['id']}: {$e['message']}", $errores));
                $this->jsonError($msg, 422, ['errores' => $errores]);
            }

            $this->jsonSuccess([
                'importados' => $importados,
                'errores'    => $errores,
                'modelos'    => ComparadorTika::listarModelos(true),
            ], count($importados) === 1
                ? "Modelo {$importados[0]['id']} importado correctamente."
                : count($importados) . ' modelos importados correctamente.');
        } catch (Throwable $e) {
            $this->jsonError('Error durante la importación: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Extrae y valida los 6 archivos CSV subidos por formulario (campos individuales o múltiples).
     *
     * @return array<string, string>
     */
    private function extractUploadedCsvMap(): array
    {
        $map = [];
        $uploadErrors = [];

        $describeUploadError = static function (int $code, string $fileName): string {
            switch ($code) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    return "{$fileName} supera el tamaño máximo permitido (" . ini_get('upload_max_filesize') . ")";
                case UPLOAD_ERR_PARTIAL:
                    return "{$fileName} se subió solo parcialmente";
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                    return "Error temporal del servidor al guardar {$fileName}";
                default:
                    return "Error al subir {$fileName} (código {$code})";
            }
        };

        // Caso 1: campos nombrados individualmente (csv_esquejes_futuros, etc.)
        $fieldMap = [
            'csv_esquejes_futuros' => 'esquejes_futuros.csv',
            'csv_proy1'            => 'proy1.csv',
            'csv_proy2'            => 'proy2.csv',
            'csv_proy3'            => 'proy3.csv',
            'csv_guarde'           => 'guarde.csv',
            'csv_salvamento'       => 'salvamento.csv',
        ];

        foreach ($fieldMap as $inputName => $csvName) {
            if (!isset($_FILES[$inputName])) {
                continue;
            }
            $err = (int)($_FILES[$inputName]['error'] ?? UPLOAD_ERR_NO_FILE);
            if ($err === UPLOAD_ERR_OK && !empty($_FILES[$inputName]['tmp_name'])) {
                $map[$csvName] = (string)$_FILES[$inputName]['tmp_name'];
            } elseif ($err !== UPLOAD_ERR_NO_FILE) {
                $orig = (string)($_FILES[$inputName]['name'] ?? $csvName);
                $uploadErrors[] = $describeUploadError($err, $orig);
            }
        }

        // Caso 2: input file múltiple (archivos[]) identificando por nombre de archivo (incluyendo alias como granel.csv -> guarde.csv)
        if (!empty($_FILES['archivos']['name']) && is_array($_FILES['archivos']['name'])) {
            $names = $_FILES['archivos']['name'];
            $tmps = $_FILES['archivos']['tmp_name'];
            $errs = $_FILES['archivos']['error'];
            foreach ($names as $i => $origName) {
                $err = (int)($errs[$i] ?? UPLOAD_ERR_NO_FILE);
                if ($err === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                if ($err !== UPLOAD_ERR_OK) {
                    $uploadErrors[] = $describeUploadError($err, (string)$origName);
                    continue;
                }
                $canonical = ComparadorTika::resolveCanonicalCsvName((string)$origName);
                if ($canonical !== null && !empty($tmps[$i])) {
                    $map[$canonical] = (string)$tmps[$i];
                }
            }
        }

        if (!empty($uploadErrors)) {
            throw new \RuntimeException('Error en archivos subidos: ' . implode('; ', $uploadErrors));
        }

        $faltantes = [];
        foreach (ComparadorTika::REQUIRED_FILES as $req) {
            if (empty($map[$req]) || !is_file($map[$req])) {
                $faltantes[] = ($req === 'guarde.csv') ? 'guarde.csv (o granel.csv)' : $req;
            }
        }

        if (!empty($faltantes)) {
            throw new \RuntimeException('Faltan los siguientes archivos CSV: ' . implode(', ', $faltantes));
        }

        return $map;
    }

    /**
     * Envía respuesta JSON aplicando compresión gzip cuando el navegador lo soporta.
     *
     * @param mixed $data
     */
    private function sendCompressedJson($data, int $statusCode = 200): void
    {
        $json = json_encode($data, self::JSON_FLAGS);
        if ($json === false) {
            $this->jsonError('Error codificando respuesta JSON', 500);
        }

        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

            $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
            $zlibActive = ini_get('zlib.output_compression');
            if (
                strpos($acceptEncoding, 'gzip') !== false
                && function_exists('gzencode')
                && (!$zlibActive || strtolower((string)$zlibActive) === 'off' || $zlibActive === '0')
            ) {
                $compressed = gzencode($json, 6);
                if ($compressed !== false) {
                    header('Content-Encoding: gzip');
                    header('Vary: Accept-Encoding');
                    header('Content-Length: ' . strlen($compressed));
                    echo $compressed;
                    exit;
                }
            }

            header('Content-Length: ' . strlen($json));
        }

        echo $json;
        exit;
    }
}

