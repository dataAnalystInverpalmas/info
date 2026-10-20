<?php
// Endpoint del seguimiento diario de floreros (uso móvil).
// Flujo: cada día se asigna a los tallos su causa de fin de florero. Mientras falten tallos el
// florero sigue pendiente; al completar todos se habilita la evaluación, que cierra el florero
// y vuelca causas y evaluación a las tablas del reporte (fv_causes / fv_evaluations).
if (is_file("funciones/conexion.php")) {
    include_once("funciones/conexion.php");
} else {
    include_once("../funciones/conexion.php");
}

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Sesión expirada']);
    exit;
}

require_once __DIR__ . '/_fv_daily_states.php';
fvd_ensure_tables($conexion);

$accion = $_REQUEST['accion'] ?? '';
$userId = (int)$_SESSION['id'];

function fvd_fecha($v) {
    $d = DateTime::createFromFormat('Y-m-d', (string)$v);
    return ($d && $d->format('Y-m-d') === $v) ? $v : date('Y-m-d');
}

function fvd_dias($fecha, $fechaFlorero) {
    return (int)((strtotime($fecha) - strtotime($fechaFlorero)) / 86400) + 1;
}

function fvd_florero($conexion, $fvId) {
    $stmt = $conexion->prepare(
        "SELECT fv.id, fv.fecha_florero, fv.tallos, fv.simulacion, fv.grupo_descripcion,
                v.nombre AS variedad, k.nombre AS tipo
         FROM flower_vases fv
         LEFT JOIN varieties v ON v.id = fv.variedad_id
         LEFT JOIN fv_kinds k ON k.id = fv.tipo_id
         WHERE fv.id = ?"
    );
    $stmt->bind_param('i', $fvId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row;
}

function fvd_finalizado($conexion, $fvId) {
    $stmt = $conexion->prepare("SELECT fecha_final FROM fv_daily_final WHERE fv_id = ?");
    $stmt->bind_param('i', $fvId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_row();
    $stmt->close();
    return $row ? $row[0] : null;
}

function fvd_contar_causas($conexion, $fvId) {
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM fv_daily_causes WHERE fv_id = ?");
    $stmt->bind_param('i', $fvId);
    $stmt->execute();
    $n = (int)$stmt->get_result()->fetch_row()[0];
    $stmt->close();
    return $n;
}

function fvd_causas_catalogo($conexion) {
    $r = $conexion->query("SELECT MIN(id) AS id, nombre FROM fv_causes_items GROUP BY nombre ORDER BY nombre");
    return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
}

// Ítems de evaluación agrupados por nombre: [{nombre, opciones:[{id, puntaje}]}]
function fvd_items_evaluacion($conexion) {
    $r = $conexion->query("SELECT id, nombre, puntaje FROM fv_evaluation_items ORDER BY nombre, puntaje");
    $out = [];
    if ($r) {
        foreach ($r->fetch_all(MYSQLI_ASSOC) as $row) {
            $out[$row['nombre']][] = ['id' => (int)$row['id'], 'puntaje' => $row['puntaje']];
        }
    }
    $lista = [];
    foreach ($out as $nombre => $ops) {
        $lista[] = ['nombre' => $nombre, 'opciones' => $ops];
    }
    return $lista;
}

switch ($accion) {
    case 'list':
        $fecha = fvd_fecha($_GET['fecha'] ?? date('Y-m-d'));
        $stmt = $conexion->prepare(
            "SELECT fv.id, fv.fecha_florero, fv.tallos, fv.simulacion, fv.grupo_descripcion,
                    v.nombre AS variedad, k.nombre AS tipo,
                    (SELECT COUNT(*) FROM fv_daily_causes c WHERE c.fv_id = fv.id) AS con_causa,
                    (SELECT fecha_final FROM fv_daily_final f WHERE f.fv_id = fv.id) AS fecha_final
             FROM flower_vases fv
             LEFT JOIN varieties v ON v.id = fv.variedad_id
             LEFT JOIN fv_kinds k ON k.id = fv.tipo_id
             WHERE fv.fecha_florero <= ? AND fv.fecha_florero >= DATE_SUB(?, INTERVAL 60 DAY)
             ORDER BY (fecha_final IS NOT NULL), fv.fecha_florero DESC, fv.id DESC"
        );
        $stmt->bind_param('ss', $fecha, $fecha);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        foreach ($rows as &$r) {
            $r['dia'] = fvd_dias($fecha, $r['fecha_florero']);
        }
        echo json_encode(['ok' => true, 'floreros' => $rows]);
        break;

    case 'get':
        $fvId = (int)($_GET['fv_id'] ?? 0);
        $fecha = fvd_fecha($_GET['fecha'] ?? date('Y-m-d'));
        $fv = fvd_florero($conexion, $fvId);
        if (!$fv) {
            echo json_encode(['ok' => false, 'error' => 'Florero no encontrado']);
            break;
        }
        $stmt = $conexion->prepare(
            "SELECT c.tallo_numero, c.causes_item_id, c.fecha_revision, c.dias, i.nombre AS causa
             FROM fv_daily_causes c LEFT JOIN fv_causes_items i ON i.id = c.causes_item_id
             WHERE c.fv_id = ?"
        );
        $stmt->bind_param('i', $fvId);
        $stmt->execute();
        $tallos = [];
        foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $c) {
            $tallos[$c['tallo_numero']] = $c;
        }
        $stmt->close();
        $fv['dia'] = fvd_dias($fecha, $fv['fecha_florero']);
        $completo = count($tallos) >= (int)$fv['tallos'];
        echo json_encode([
            'ok' => true,
            'florero' => $fv,
            'tallos' => (object)$tallos,
            'causas' => fvd_causas_catalogo($conexion),
            'finalizado' => fvd_finalizado($conexion, $fvId),
            'items' => $completo ? fvd_items_evaluacion($conexion) : [],
        ]);
        break;

    case 'set_cause':
        // tallos: lista separada por comas. causa_id = 0 quita la causa (el tallo vuelve a pendiente).
        $fvId = (int)($_POST['fv_id'] ?? 0);
        $fecha = fvd_fecha($_POST['fecha'] ?? '');
        $causaId = (int)($_POST['causa_id'] ?? 0);
        $lista = array_unique(array_filter(array_map('intval', explode(',', (string)($_POST['tallos'] ?? ''))), function ($t) { return $t > 0; }));
        $fv = fvd_florero($conexion, $fvId);
        if (!$fv || !$lista) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
            break;
        }
        if (fvd_finalizado($conexion, $fvId)) {
            echo json_encode(['ok' => false, 'error' => 'El florero ya fue finalizado']);
            break;
        }
        $max = (int)$fv['tallos'];
        foreach ($lista as $t) {
            if ($t > $max) {
                echo json_encode(['ok' => false, 'error' => 'Tallo fuera de rango']);
                exit;
            }
        }
        if ($causaId === 0) {
            $stmt = $conexion->prepare("DELETE FROM fv_daily_causes WHERE fv_id = ? AND tallo_numero = ?");
            $t = 0;
            $stmt->bind_param('ii', $fvId, $t);
            foreach ($lista as $t) {
                $stmt->execute();
            }
            $stmt->close();
            echo json_encode(['ok' => true]);
            break;
        }
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM fv_causes_items WHERE id = ?");
        $stmt->bind_param('i', $causaId);
        $stmt->execute();
        $existe = (int)$stmt->get_result()->fetch_row()[0] > 0;
        $stmt->close();
        $dias = fvd_dias($fecha, $fv['fecha_florero']);
        if (!$existe || $dias < 1) {
            echo json_encode(['ok' => false, 'error' => 'Causa o fecha inválida']);
            break;
        }
        $stmt = $conexion->prepare(
            "INSERT INTO fv_daily_causes (fv_id, tallo_numero, causes_item_id, fecha_revision, dias, user_id)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE causes_item_id = VALUES(causes_item_id), user_id = VALUES(user_id)"
        );
        $t = 0;
        $stmt->bind_param('iiisii', $fvId, $t, $causaId, $fecha, $dias, $userId);
        foreach ($lista as $t) {
            $stmt->execute();
        }
        $stmt->close();
        echo json_encode(['ok' => true]);
        break;

    case 'finalize':
        // items: JSON {"<nombre del ítem>": <evaluation_item_id>, ...}
        $fvId = (int)($_POST['fv_id'] ?? 0);
        $fecha = fvd_fecha($_POST['fecha'] ?? '');
        $elegidos = json_decode($_POST['items'] ?? '{}', true);
        $fv = fvd_florero($conexion, $fvId);
        if (!$fv || !is_array($elegidos)) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
            break;
        }
        if (fvd_finalizado($conexion, $fvId)) {
            echo json_encode(['ok' => false, 'error' => 'El florero ya fue finalizado']);
            break;
        }
        if (fvd_contar_causas($conexion, $fvId) < (int)$fv['tallos']) {
            echo json_encode(['ok' => false, 'error' => 'Aún faltan tallos por registrar']);
            break;
        }
        // Cada ítem de evaluación debe tener una opción válida.
        $ids = [];
        foreach (fvd_items_evaluacion($conexion) as $item) {
            $valido = null;
            foreach ($item['opciones'] as $op) {
                if (isset($elegidos[$item['nombre']]) && (int)$elegidos[$item['nombre']] === $op['id']) {
                    $valido = $op['id'];
                }
            }
            if ($valido === null) {
                echo json_encode(['ok' => false, 'error' => 'Falta evaluar: ' . $item['nombre']]);
                exit;
            }
            $ids[] = $valido;
        }

        $conexion->begin_transaction();
        try {
            // Causas: se resumen por causa y día, como en el registro de escritorio.
            $stmt = $conexion->prepare("SELECT COUNT(*) FROM fv_causes WHERE fv_id = ?");
            $stmt->bind_param('i', $fvId);
            $stmt->execute();
            $yaTiene = (int)$stmt->get_result()->fetch_row()[0] > 0;
            $stmt->close();
            if (!$yaTiene) {
                $stmt = $conexion->prepare(
                    "INSERT INTO fv_causes (fv_id, causes_item_id, dias, cantidad)
                     SELECT fv_id, causes_item_id, dias, COUNT(*) FROM fv_daily_causes
                     WHERE fv_id = ? GROUP BY causes_item_id, dias"
                );
                $stmt->bind_param('i', $fvId);
                $stmt->execute();
                $stmt->close();
            }
            $stmt = $conexion->prepare("INSERT INTO fv_evaluations (fv_id, evaluation_item_id) VALUES (?, ?)");
            $eid = 0;
            $stmt->bind_param('ii', $fvId, $eid);
            foreach ($ids as $eid) {
                $stmt->execute();
            }
            $stmt->close();
            $stmt = $conexion->prepare("INSERT INTO fv_daily_final (fv_id, fecha_final, user_id) VALUES (?, ?, ?)");
            $stmt->bind_param('isi', $fvId, $fecha, $userId);
            $stmt->execute();
            $stmt->close();
            $conexion->commit();
            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            $conexion->rollback();
            echo json_encode(['ok' => false, 'error' => 'No se pudo finalizar']);
        }
        break;

    case 'catalogs':
        $out = ['tipos' => [], 'origenes' => [], 'productos' => []];
        foreach ([['tipos', 'fv_kinds'], ['origenes', 'fv_origens']] as $p) {
            $r = $conexion->query("SELECT id, nombre FROM {$p[1]} ORDER BY nombre");
            $out[$p[0]] = $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
        }
        $r = $conexion->query("SELECT DISTINCT producto FROM varieties WHERE estado = 1 AND producto IS NOT NULL AND producto <> '' ORDER BY producto");
        $out['productos'] = $r ? array_column($r->fetch_all(MYSQLI_ASSOC), 'producto') : [];
        echo json_encode(['ok' => true] + $out);
        break;

    case 'variedades':
        $producto = $_GET['producto'] ?? '';
        $stmt = $conexion->prepare("SELECT id, nombre FROM varieties WHERE producto = ? AND estado = 1 ORDER BY nombre");
        $stmt->bind_param('s', $producto);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        echo json_encode(['ok' => true, 'variedades' => $rows]);
        break;

    case 'create':
        $fechaCorte = fvd_fecha($_POST['fecha_corte'] ?? '');
        $fechaFlorero = fvd_fecha($_POST['fecha_florero'] ?? '');
        $tipo = (int)($_POST['tipo_id'] ?? 0);
        $origen = (int)($_POST['origen_id'] ?? 0);
        $variedad = (int)($_POST['variedad_id'] ?? 0);
        $tallos = (int)($_POST['tallos'] ?? 0);
        $pmax = (int)($_POST['pmax'] ?? 0);
        $pcorte = max(1, (int)($_POST['pcorte'] ?? 1));
        $pempaque = max(1, (int)($_POST['pempaque'] ?? 1));
        $pflorero = (int)($_POST['pflorero'] ?? 0);
        $guarde = max(0, (int)($_POST['guarde'] ?? 0));
        $simulacion = !empty($_POST['simulacion']) ? 1 : 0;
        $agrupar = !empty($_POST['agrupar']);
        $obs = mb_substr(trim($_POST['comentario'] ?? ''), 0, 500);

        if ($tipo < 1 || $origen < 1 || $variedad < 1 || $tallos < 1) {
            echo json_encode(['ok' => false, 'error' => 'Faltan datos obligatorios']);
            break;
        }

        $maxG = (int)($conexion->query("SELECT COALESCE(MAX(grupo),0) FROM flower_vases")->fetch_row()[0] ?? 0);
        $grupo = ($agrupar && $maxG > 0) ? $maxG : $maxG + 1;

        $stmt = $conexion->prepare("SELECT nombre FROM varieties WHERE id = ?");
        $stmt->bind_param('i', $variedad);
        $stmt->execute();
        $vn = $stmt->get_result()->fetch_row();
        $stmt->close();
        if (!$vn) {
            echo json_encode(['ok' => false, 'error' => 'Variedad no encontrada']);
            break;
        }
        $desc = strtolower($vn[0]);

        $stmt = $conexion->prepare(
            "INSERT INTO flower_vases (fecha_corte, fecha_florero, grupo, grupo_descripcion, tipo_id, variedad_id, origen_id,
                                       tallos, pmax, pcorte, pempaque, pflorero, guarde_granel, simulacion, observacion, user_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('ssisiiiiiiiiiisi', $fechaCorte, $fechaFlorero, $grupo, $desc, $tipo, $variedad, $origen,
            $tallos, $pmax, $pcorte, $pempaque, $pflorero, $guarde, $simulacion, $obs, $userId);
        $ok = $stmt->execute();
        $newId = $conexion->insert_id;
        $stmt->close();

        if ($ok && $agrupar && $grupo === $maxG) {
            // Igual que el registro de escritorio: la descripción del grupo lista todas sus variedades.
            $stmt = $conexion->prepare(
                "UPDATE flower_vases SET grupo_descripcion = (
                    SELECT d FROM (
                        SELECT GROUP_CONCAT(v.nombre) AS d FROM flower_vases fv
                        LEFT JOIN varieties v ON v.id = fv.variedad_id WHERE fv.grupo = ?
                    ) t)
                 WHERE grupo = ?"
            );
            $stmt->bind_param('ii', $grupo, $grupo);
            $stmt->execute();
            $stmt->close();
        }
        echo json_encode($ok ? ['ok' => true, 'id' => $newId] : ['ok' => false, 'error' => 'No se pudo guardar']);
        break;

    default:
        echo json_encode(['ok' => false, 'error' => 'Acción no válida']);
}
