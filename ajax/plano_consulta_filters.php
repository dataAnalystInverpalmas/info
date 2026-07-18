<?php
// Devuelve listas filtradas de dropdowns subordinados según filtros previos
header('Content-Type: application/json; charset=utf-8');

if (is_file("../funciones/conexion.php")){
    include "../funciones/conexion.php";
} else {
    include "./funciones/conexion.php";
}

use App\Models\PlanoConsulta;

// Recopilar filtros disponibles
$filters = [];
if (!empty($_GET['finca'])) { $filters['finca'] = $_GET['finca']; }
if (!empty($_GET['bloque'])) { $filters['bloque'] = $_GET['bloque']; }
if (!empty($_GET['tabla'])) { $filters['tabla'] = $_GET['tabla']; }
if (!empty($_GET['nave'])) { $filters['nave'] = $_GET['nave']; }
if (!empty($_GET['tipo_siembra'])) { $filters['tipo_siembra'] = $_GET['tipo_siembra']; }
if (!empty($_GET['variedad'])) { $filters['variedad'] = $_GET['variedad']; }
if (!empty($_GET['cosecha'])) { $filters['cosecha'] = $_GET['cosecha']; }
if (!empty($_GET['semana_siembra'])) { $filters['semana_siembra'] = $_GET['semana_siembra']; }

$data = [
    'bloques' => [],
    'tablas' => [],
    'naves' => [],
    'tiposSiembra' => [],
    'variedades' => [],
    'cosechas' => [],
    'semanasSiembra' => []
];

// Construir WHERE dinámicamente basado en filtros previos
$where = "WHERE plantas > 0";
$params = [];
$types = "";

if (!empty($filters['finca'])) {
    $where .= " AND finca = ?";
    $params[] = $filters['finca'];
    $types .= "s";
}
if (!empty($filters['bloque'])) {
    $where .= " AND bloque = ?";
    $params[] = $filters['bloque'];
    $types .= "s";
}
if (!empty($filters['tabla'])) {
    $where .= " AND tabla = ?";
    $params[] = $filters['tabla'];
    $types .= "s";
}
if (!empty($filters['nave'])) {
    $where .= " AND nave = ?";
    $params[] = $filters['nave'];
    $types .= "s";
}
if (!empty($filters['tipo_siembra'])) {
    $where .= " AND tipo_siembra = ?";
    $params[] = $filters['tipo_siembra'];
    $types .= "s";
}

$conexion = \App\Helpers\Database::getConnection();

// Bloques (si finca seleccionada)
if (!empty($filters['finca'])) {
    $qb = "SELECT DISTINCT bloque FROM plane " . $where . " AND bloque IS NOT NULL AND bloque <> '' ORDER BY bloque";
    $stmt = $conexion->prepare($qb);
    if ($stmt) {
        if (!empty($params)) { $stmt->bind_param($types, ...$params); }
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($r = $result->fetch_assoc()) {
                $data['bloques'][] = $r['bloque'];
            }
        }
        $stmt->close();
    }
}

// Tablas (si finca y bloque seleccionados)
if (!empty($filters['finca']) && !empty($filters['bloque'])) {
    $qt = "SELECT DISTINCT tabla FROM plane " . $where . " AND tabla IS NOT NULL AND tabla <> '' ORDER BY tabla";
    $stmt = $conexion->prepare($qt);
    if ($stmt) {
        if (!empty($params)) { $stmt->bind_param($types, ...$params); }
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($r = $result->fetch_assoc()) {
                $data['tablas'][] = $r['tabla'];
            }
        }
        $stmt->close();
    }
}

// Naves (si finca, bloque y tabla seleccionados)
if (!empty($filters['finca']) && !empty($filters['bloque']) && !empty($filters['tabla'])) {
    $qn = "SELECT DISTINCT nave FROM plane " . $where . " AND nave IS NOT NULL AND nave <> '' ORDER BY nave";
    $stmt = $conexion->prepare($qn);
    if ($stmt) {
        if (!empty($params)) { $stmt->bind_param($types, ...$params); }
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($r = $result->fetch_assoc()) {
                $data['naves'][] = $r['nave'];
            }
        }
        $stmt->close();
    }
}

// Tipos de siembra (si ubicación seleccionada)
if (!empty($filters['finca']) && !empty($filters['bloque'])) {
    $qts = "SELECT DISTINCT tipo_siembra FROM plane " . $where . " AND tipo_siembra IS NOT NULL AND tipo_siembra <> '' ORDER BY tipo_siembra";
    $stmt = $conexion->prepare($qts);
    if ($stmt) {
        if (!empty($params)) { $stmt->bind_param($types, ...$params); }
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($r = $result->fetch_assoc()) {
                $data['tiposSiembra'][] = $r['tipo_siembra'];
            }
        }
        $stmt->close();
    }
}

// Variedades y cosechas con consultas completas (subordinadas a tipo_siembra si está seleccionado)
// Usar PlanoConsulta para getDistinctValues con filtros
$data['variedades'] = PlanoConsulta::getDistinctValues('variedad', $filters);
$data['cosechas'] = PlanoConsulta::getDistinctValues('temporada', $filters);
$data['semanasSiembra'] = PlanoConsulta::getDistinctSemanasSiembra($filters);

echo json_encode($data, JSON_UNESCAPED_UNICODE);
