<?php
// ajax/consultar_aplicaciones.php
require_once dirname(__DIR__) . '/funciones/conexion.php';

// Asegurar respuesta JSON incluso si hay fatal error (evita HTML tipo "<br><b>Fatal error...</b>")
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8', true, 500);
        echo json_encode([
            'error' => 'Fatal error',
            'message' => $err['message'],
            'file' => basename($err['file']),
            'line' => $err['line'],
        ]);
    }
});

// Parámetros del formulario
$finca = $_POST['finca'] ?? '';

// Nuevos parámetros para filtros del frontend
$tipo_filtro = $_POST['tipo_filtro'] ?? '';
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';

// Construir WHERE dinámico
$whereConditions = [];
$params = [];
$types = '';

// Filtros básicos (si se proporcionan)
if (!empty($finca)) {
    $whereConditions[] = "p.finca = ?";
    $params[] = $finca;
    $types .= 's';
}

// Filtro por tipo de aplicación
if (!empty($tipo_filtro)) {
    $whereConditions[] = "a.tipo = ?";
    $params[] = $tipo_filtro;
    $types .= 's';
}

// Incluir secciones 1 y 2 (antes solo 2)
$whereConditions[] = "aa.seccion IN (1, 2)";

// Filtrar por rango de fechas de aplicación
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    // Este filtro es más complejo porque necesita calcular la fecha de aplicación
    // Por ahora lo omitiremos, pero puedes implementarlo si es necesario
}

$whereClause = '';
if (!empty($whereConditions)) {
    $whereClause = "WHERE " . implode(" AND ", $whereConditions);
}

// Consulta SQL
$sql = "
    SELECT 
        p.finca,
        p.bloque,
        p.variedad,
        s.cod_temporada as temporada,
        a.tipo,
        a.aplicar,
        a.valor,
        p.tipo_siembra,
        p.fecha_siembra,
        s.fecha_pico,
        v.ciclo AS ciclo_variedad,
        COALESCE(pr.ciclo, v.ciclo) AS ciclo_efectivo,
        COALESCE(pr.fecha_siembra, p.fecha_siembra) AS fecha_siembra_base,
        aa.calc_conciclo,
        aa.orden,
        p.plantas,
        ROUND(p.plantas / 960, 1) AS camas_equivalentes,
        
        -- Calcular fecha de aplicación (simple por ahora, puedes complejizar)
        CASE
            WHEN p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL')
            THEN DATE_ADD(p.fecha_siembra, INTERVAL COALESCE(pr.ciclo, v.ciclo) WEEK)
            ELSE s.fecha_pico
        END AS fecha_base_aplicacion
        
    FROM informes.plane AS p
    LEFT JOIN informes.seasons AS s ON p.temporada = s.nombre
    LEFT JOIN informes.varieties AS v ON p.variedad = v.nombre
    LEFT JOIN informes.arrangements AS a ON p.finca = a.finca AND p.variedad = a.variedad
    LEFT JOIN (
        SELECT 
            variedad,
            temporada_obj,
            pico AS ciclo,
            fecha_siembra
        FROM informes.program
        GROUP BY variedad, temporada_obj, ciclo, fecha_siembra
    ) AS pr ON pr.variedad = p.variedad AND pr.temporada_obj = p.temporada
    LEFT JOIN informes.arrangement AS aa ON a.tipo = aa.tipo AND a.aplicar = aa.aplicar
    
    {$whereClause}
    
    ORDER BY p.finca, p.bloque, aa.orden, a.valor ASC
";

// Preparar y ejecutar
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die(json_encode(['error' => "Error en prepare(): " . $conexion->error]));
}

// Vincular parámetros dinámicamente
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

// Obtener resultados sin depender de mysqli_stmt::get_result()
// (get_result puede no estar disponible si no existe mysqlnd)
$stmt->store_result();
$meta = $stmt->result_metadata();
if (!$meta) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No se pudo obtener metadata del resultado']);
    exit;
}

$fields = $meta->fetch_fields();
$row = [];
$bind = [];
foreach ($fields as $field) {
    $row[$field->name] = null;
    $bind[] = &$row[$field->name];
}
call_user_func_array([$stmt, 'bind_result'], $bind);

// Agrupar para la primera tabla (backend)
// Clave: finca|bloque|variedad|temporada|aplicar
$agrupado = [];

while ($stmt->fetch()) {
    // Copiar la fila (bind_result reutiliza el mismo arreglo)
    $rowData = [];
    foreach ($row as $k => $v) {
        $rowData[$k] = $v;
    }

    // Calcular fecha de aplicación usando función PHP
    $fechaAplicacion = calcularFechaAplicacion(
        $rowData['tipo_siembra'],
        $rowData['fecha_siembra_base'],
        $rowData['fecha_pico'],
        $rowData['ciclo_efectivo'],
        $rowData['calc_conciclo'],
        $rowData['valor']
    );
    
    // Semana ISO (año ISO + semana ISO)
    $semanaIso = date('oW', strtotime($fechaAplicacion));

    // Filtro por rango de fechas (estricto)
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $fechaAplicacionCmp = date('Y-m-d', strtotime($fechaAplicacion));
        $fechaInicioCmp = date('Y-m-d', strtotime($fecha_inicio));
        $fechaFinCmp = date('Y-m-d', strtotime($fecha_fin));

        if ($fechaAplicacionCmp < $fechaInicioCmp || $fechaAplicacionCmp > $fechaFinCmp) {
            continue;
        }
    }

    $key = implode('|', [
        (string)($rowData['finca'] ?? ''),
        (string)($rowData['bloque'] ?? ''),
        (string)($rowData['variedad'] ?? ''),
        (string)($rowData['temporada'] ?? ''),
        (string)($rowData['aplicar'] ?? ''),
    ]);

    if (!isset($agrupado[$key])) {
        $agrupado[$key] = [
            'finca' => $rowData['finca'] ?? '',
            'bloque' => $rowData['bloque'] ?? '',
            'variedad' => $rowData['variedad'] ?? '',
            'temporada' => $rowData['temporada'] ?? '',
            'aplicar' => $rowData['aplicar'] ?? '',
            // tipo/semana se consolidan (si hay mezcla: VARIOS)
            '_tipo_set' => [],
            '_semana_set' => [],
            'tipo' => $rowData['tipo'] ?? '',
            'semana_anio' => $semanaIso,
            // métricas
            'plantas' => 0,
            'camas' => 0,
            'camas_real' => 0,
        ];
    }

    $tipoRow = (string)($rowData['tipo'] ?? '');
    if ($tipoRow !== '') {
        $agrupado[$key]['_tipo_set'][$tipoRow] = true;
    }
    $agrupado[$key]['_semana_set'][(string)$semanaIso] = true;

    $plantas = (int)($rowData['plantas'] ?? 0);
    $agrupado[$key]['plantas'] += $plantas;
    $agrupado[$key]['camas'] = $agrupado[$key]['plantas'] / 960;
    $agrupado[$key]['camas_real'] = (int)round($agrupado[$key]['camas'], 0);
}

// Normalizar salida para el frontend (primera tabla)
$data = array_values($agrupado);
foreach ($data as &$r) {
    $tipos = array_keys($r['_tipo_set'] ?? []);
    $semanas = array_keys($r['_semana_set'] ?? []);

    $r['tipo'] = (count($tipos) === 1) ? $tipos[0] : ((count($tipos) > 1) ? 'VARIOS' : ($r['tipo'] ?? ''));
    $r['semana_anio'] = (count($semanas) === 1) ? $semanas[0] : ((count($semanas) > 1) ? 'VARIAS' : ($r['semana_anio'] ?? ''));

    unset($r['_tipo_set'], $r['_semana_set']);
}
unset($r);

// Cerrar conexiones
$stmt->close();
$conexion->close();

// Devolver resultados
header('Content-Type: application/json; charset=utf-8');
if (ob_get_length()) {
    ob_clean();
}
echo json_encode($data);

// Función para calcular fecha de aplicación
function calcularFechaAplicacion($tipoSiembra, $fechaSiembra, $fechaPico, $ciclo, $calcConCiclo, $valor) {
    // Fecha base según tipo de siembra
    if (in_array($tipoSiembra, ['REEMPLAZO', 'ADICIONAL'])) {
        $fechaBase = date('Y-m-d', strtotime($fechaSiembra . " +{$ciclo} weeks"));
    } else {
        $fechaBase = $fechaPico;
    }
    
    // Aplicar ajuste según calc_conciclo
    switch($calcConCiclo) {
        case 0:
        case 3:
        case 4:
        case 5:
            $fechaAjustada = date('Y-m-d', strtotime($fechaBase . " -{$ciclo} weeks"));
            break;
        case 2:
            $fechaAjustada = date('Y-m-d', strtotime($fechaBase . " +{$ciclo} weeks"));
            break;
        case 1:
            $fechaAjustada = $fechaSiembra;
            break;
        case 6:
        case 7:
        case 8:
            $fechaAjustada = $fechaBase;
            break;
        default:
            $fechaAjustada = $fechaBase;
    }
    
    // Aplicar días específicos
    $multiplicador = ($calcConCiclo == 6) ? -1 : 1;
    $diasAjuste = $valor * $multiplicador;
    
    return date('Y-m-d', strtotime($fechaAjustada . " {$diasAjuste} days"));
}