<?php

function crud_get_conexion()
{
    global $conexion;
    if (isset($conexion) && $conexion instanceof \mysqli) {
        return $conexion;
    }
    if (isset($GLOBALS['conexion']) && $GLOBALS['conexion'] instanceof \mysqli) {
        return $GLOBALS['conexion'];
    }
    require_once dirname(__DIR__) . '/funciones/conexion.php';
    global $conexion;
    return $conexion ?? ($GLOBALS['conexion'] ?? null);
}

function crud_json($payload)
{
    echo json_encode($payload);
    exit;
}

function crud_bind_and_execute($stmt, $types, $params)
{
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    return $stmt->execute();
}

function crud_get_table_meta($conexion, $table)
{
    $dbRes = $conexion->query('SELECT DATABASE() AS db');
    if (!$dbRes) {
        return [null, 'No se pudo obtener DB actual: ' . $conexion->error];
    }
    $dbRow = $dbRes->fetch_assoc();
    $db = $dbRow['db'] ?? '';
    if ($db === '') {
        return [null, 'No se pudo resolver el nombre de la base de datos'];
    }

    $tableEsc = $conexion->real_escape_string($table);
    $dbEsc = $conexion->real_escape_string($db);

    $sql = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY, EXTRA
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = '$dbEsc' AND TABLE_NAME = '$tableEsc'
            ORDER BY ORDINAL_POSITION";
    $res = $conexion->query($sql);
    if (!$res) {
        return [null, 'No se pudo consultar metadatos de tabla: ' . $conexion->error];
    }

    $columns = [];
    $pk = null;
    while ($row = $res->fetch_assoc()) {
        $name = $row['COLUMN_NAME'];
        $columns[$name] = [
            'data_type' => strtolower((string)$row['DATA_TYPE']),
            'nullable' => strtoupper((string)$row['IS_NULLABLE']) === 'YES',
            'is_pk' => strtoupper((string)$row['COLUMN_KEY']) === 'PRI',
            'extra' => strtolower((string)$row['EXTRA']),
        ];
        if ($columns[$name]['is_pk'] && $pk === null) {
            $pk = $name;
        }
    }

    if (empty($columns)) {
        return [null, 'La tabla no existe o no tiene columnas'];
    }

    return [[
        'db' => $db,
        'table' => $table,
        'columns' => $columns,
        'pk' => $pk,
    ], null];
}

function crud_is_numeric_type($type)
{
    return in_array($type, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'decimal', 'float', 'double'], true);
}

function crud_bind_type_for($type)
{
    if (in_array($type, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'], true)) {
        return 'i';
    }
    if (in_array($type, ['decimal', 'float', 'double'], true)) {
        return 'd';
    }
    return 's';
}

function crud_normalize_value($raw, $colMeta)
{
    if ($raw === '' || $raw === null) {
        return $colMeta['nullable'] ? null : '';
    }

    $type = $colMeta['data_type'];
    if (in_array($type, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'], true)) {
        return (int)$raw;
    }
    if (in_array($type, ['decimal', 'float', 'double'], true)) {
        return (float)$raw;
    }
    return (string)$raw;
}

function crud_selectable_columns($meta)
{
    $cols = array_keys($meta['columns']);
    // NUNCA exponer la columna de contraseña en listados ni consultas
    if ($meta['table'] === 'users') {
        $cols = array_values(array_filter($cols, function ($col) {
            return $col !== 'password';
        }));
    }
    return $cols;
}

function crud_editable_columns($meta)
{
    $out = [];
    foreach ($meta['columns'] as $name => $info) {
        $extra = $info['extra'];
        if (strpos($extra, 'auto_increment') !== false) {
            continue;
        }
        if (strpos($extra, 'generated') !== false) {
            continue;
        }
        $out[] = $name;
    }
    return $out;
}

function crud_create_endpoint($table)
{
    $conexion = crud_get_conexion();
    header('Content-Type: application/json; charset=utf-8');

    // Validación de seguridad para tablas críticas
    if ($table === 'users' || $table === 'roles') {
        \App\Helpers\Auth::requireAdmin();
    } else {
        \App\Helpers\Auth::requireAuth();
    }

    list($meta, $err) = crud_get_table_meta($conexion, $table);
    if ($err) {
        crud_json(['success' => false, 'message' => $err]);
    }

    $editable = crud_editable_columns($meta);
    $cols = [];
    $types = '';
    $params = [];

    foreach ($editable as $col) {
        if (!array_key_exists($col, $_POST)) {
            continue;
        }
        $raw = $_POST[$col];

        // Hashear contraseña si es creación de usuario
        if ($table === 'users' && $col === 'password') {
            if (trim((string)$raw) === '') {
                continue; // no insertar contraseña vacía
            }
            $val = password_hash(trim((string)$raw), PASSWORD_DEFAULT);
        } else {
            $val = crud_normalize_value($raw, $meta['columns'][$col]);
        }

        $cols[] = $col;
        $types .= crud_bind_type_for($meta['columns'][$col]['data_type']);
        $params[] = $val;
    }

    if (empty($cols)) {
        crud_json(['success' => false, 'message' => 'No se recibieron columnas para insertar']);
    }

    $dbTable = $meta['db'] . '.' . $meta['table'];
    $placeholders = implode(',', array_fill(0, count($cols), '?'));
    $sql = 'INSERT INTO ' . $dbTable . ' (' . implode(', ', $cols) . ') VALUES (' . $placeholders . ')';
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        crud_json(['success' => false, 'message' => $conexion->error]);
    }

    $ok = crud_bind_and_execute($stmt, $types, $params);
    if ($ok) {
        crud_json(['success' => true, 'id' => $conexion->insert_id]);
    }
    if ($conexion->errno === 1062 || $stmt->errno === 1062) {
        crud_json(['success' => false, 'message' => 'El nombre de usuario o correo ya existe. Por favor elija uno diferente.']);
    }
    crud_json(['success' => false, 'message' => $stmt->error]);
}

function crud_list_endpoint($table)
{
    $conexion = crud_get_conexion();
    header('Content-Type: application/json; charset=utf-8');

    if ($table === 'users' || $table === 'roles') {
        \App\Helpers\Auth::requireAdmin();
    } else {
        \App\Helpers\Auth::requireAuth();
    }

    list($meta, $err) = crud_get_table_meta($conexion, $table);
    if ($err) {
        echo json_encode(['data' => [], 'message' => $err]);
        exit;
    }

    $cols = crud_selectable_columns($meta);
    $dbTable = $meta['db'] . '.' . $meta['table'];
    $sql = 'SELECT ' . implode(', ', $cols) . ' FROM ' . $dbTable . ' WHERE 1=1';

    $types = '';
    $params = [];
    foreach ($cols as $col) {
        if (!isset($_GET[$col]) || trim((string)$_GET[$col]) === '') {
            continue;
        }
        $raw = trim((string)$_GET[$col]);
        $metaCol = $meta['columns'][$col];

        if (crud_is_numeric_type($metaCol['data_type'])) {
            $sql .= ' AND ' . $col . ' = ?';
            $types .= crud_bind_type_for($metaCol['data_type']);
            $params[] = crud_normalize_value($raw, $metaCol);
        } else {
            $sql .= ' AND ' . $col . ' LIKE ?';
            $types .= 's';
            $params[] = '%' . $raw . '%';
        }
    }

    if ($meta['pk']) {
        $sql .= ' ORDER BY ' . $meta['pk'] . ' DESC';
    }

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['data' => [], 'message' => $conexion->error]);
        exit;
    }

    crud_bind_and_execute($stmt, $types, $params);
    $res = $stmt->get_result();

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode(['data' => $data]);
}

function crud_get_endpoint($table)
{
    $conexion = crud_get_conexion();
    header('Content-Type: application/json; charset=utf-8');

    if ($table === 'users' || $table === 'roles') {
        \App\Helpers\Auth::requireAdmin();
    } else {
        \App\Helpers\Auth::requireAuth();
    }

    list($meta, $err) = crud_get_table_meta($conexion, $table);
    if ($err) {
        crud_json(['success' => false, 'message' => $err]);
    }

    $cols = crud_selectable_columns($meta);
    $dbTable = $meta['db'] . '.' . $meta['table'];
    $types = '';
    $params = [];

    if ($meta['pk'] && isset($_GET['id']) && (int)$_GET['id'] > 0) {
        $sql = 'SELECT ' . implode(', ', $cols) . ' FROM ' . $dbTable . ' WHERE ' . $meta['pk'] . ' = ? LIMIT 1';
        $types = 'i';
        $params[] = (int)$_GET['id'];
    } else {
        $where = [];
        foreach ($cols as $col) {
            if (!array_key_exists($col, $_GET)) {
                continue;
            }
            $where[] = $col . ' = ?';
            $types .= crud_bind_type_for($meta['columns'][$col]['data_type']);
            $params[] = crud_normalize_value($_GET[$col], $meta['columns'][$col]);
        }

        if (empty($where)) {
            crud_json(['success' => false, 'message' => 'Debe enviar id o filtros para buscar']);
        }

        $sql = 'SELECT ' . implode(', ', $cols) . ' FROM ' . $dbTable . ' WHERE ' . implode(' AND ', $where) . ' LIMIT 1';
    }

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        crud_json(['success' => false, 'message' => $conexion->error]);
    }

    crud_bind_and_execute($stmt, $types, $params);
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if ($row) {
        crud_json(['success' => true, 'data' => $row]);
    }
    crud_json(['success' => false, 'message' => 'No encontrado']);
}

function crud_update_endpoint($table)
{
    $conexion = crud_get_conexion();
    header('Content-Type: application/json; charset=utf-8');

    if ($table === 'users' || $table === 'roles') {
        \App\Helpers\Auth::requireAdmin();
    } else {
        \App\Helpers\Auth::requireAuth();
    }

    list($meta, $err) = crud_get_table_meta($conexion, $table);
    if ($err) {
        crud_json(['success' => false, 'message' => $err]);
    }

    $editable = crud_editable_columns($meta);
    $dbTable = $meta['db'] . '.' . $meta['table'];

    $setParts = [];
    $types = '';
    $params = [];

    foreach ($editable as $col) {
        if (!array_key_exists($col, $_POST)) {
            continue;
        }
        if ($meta['pk'] && $col === $meta['pk']) {
            continue;
        }

        $raw = $_POST[$col];

        // Manejo especial de contraseña en usuarios
        if ($table === 'users' && $col === 'password') {
            // Si viene vacío en un update, NO sobreescribir la contraseña existente
            if (trim((string)$raw) === '') {
                continue;
            }
            $val = password_hash(trim((string)$raw), PASSWORD_DEFAULT);
        } else {
            $val = crud_normalize_value($raw, $meta['columns'][$col]);
        }

        $setParts[] = $col . ' = ?';
        $types .= crud_bind_type_for($meta['columns'][$col]['data_type']);
        $params[] = $val;
    }

    if (empty($setParts)) {
        crud_json(['success' => false, 'message' => 'No se recibieron columnas para actualizar']);
    }

    $whereParts = [];

    if ($meta['pk'] && isset($_POST['id']) && (int)$_POST['id'] > 0) {
        $whereParts[] = $meta['pk'] . ' = ?';
        $types .= 'i';
        $params[] = (int)$_POST['id'];
    } else {
        foreach ($editable as $col) {
            $oldKey = 'old_' . $col;
            if (!array_key_exists($oldKey, $_POST)) {
                continue;
            }
            $whereParts[] = $col . ' = ?';
            $types .= crud_bind_type_for($meta['columns'][$col]['data_type']);
            $params[] = crud_normalize_value($_POST[$oldKey], $meta['columns'][$col]);
        }
    }

    if (empty($whereParts)) {
        crud_json(['success' => false, 'message' => 'Debe enviar id o claves old_* para actualizar']);
    }

    $sql = 'UPDATE ' . $dbTable . ' SET ' . implode(', ', $setParts) . ' WHERE ' . implode(' AND ', $whereParts);
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        crud_json(['success' => false, 'message' => $conexion->error]);
    }

    $ok = crud_bind_and_execute($stmt, $types, $params);
    if ($ok) {
        crud_json(['success' => true]);
    }
    if ($conexion->errno === 1062 || $stmt->errno === 1062) {
        crud_json(['success' => false, 'message' => 'El nombre de usuario o correo ya existe. Por favor elija uno diferente.']);
    }
    crud_json(['success' => false, 'message' => $stmt->error]);
}

function crud_delete_endpoint($table)
{
    $conexion = crud_get_conexion();
    header('Content-Type: application/json; charset=utf-8');

    if ($table === 'users' || $table === 'roles') {
        \App\Helpers\Auth::requireAdmin();
    } else {
        \App\Helpers\Auth::requireAuth();
    }

    if ($table === 'users' && isset($_POST['id']) && (int)$_POST['id'] === \App\Helpers\Auth::id()) {
        crud_json(['success' => false, 'message' => 'No puede eliminar su propia cuenta de usuario en sesión']);
    }

    list($meta, $err) = crud_get_table_meta($conexion, $table);
    if ($err) {
        crud_json(['success' => false, 'message' => $err]);
    }

    $editable = crud_editable_columns($meta);
    $dbTable = $meta['db'] . '.' . $meta['table'];

    $whereParts = [];
    $types = '';
    $params = [];

    if ($meta['pk'] && isset($_POST['id']) && (int)$_POST['id'] > 0) {
        $whereParts[] = $meta['pk'] . ' = ?';
        $types .= 'i';
        $params[] = (int)$_POST['id'];
    } else {
        foreach ($editable as $col) {
            if (!array_key_exists($col, $_POST)) {
                continue;
            }
            $whereParts[] = $col . ' = ?';
            $types .= crud_bind_type_for($meta['columns'][$col]['data_type']);
            $params[] = crud_normalize_value($_POST[$col], $meta['columns'][$col]);
        }
    }

    if (empty($whereParts)) {
        crud_json(['success' => false, 'message' => 'Debe enviar id o columnas para eliminar']);
    }

    $sql = 'DELETE FROM ' . $dbTable . ' WHERE ' . implode(' AND ', $whereParts);
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        crud_json(['success' => false, 'message' => $conexion->error]);
    }

    $ok = crud_bind_and_execute($stmt, $types, $params);
    if ($ok) {
        crud_json(['success' => true]);
    }
    crud_json(['success' => false, 'message' => $stmt->error]);
}

// ── Funciones de utilidad adicionales ───────────────────────────

/**
 * Sanitiza y valida input de POST/GET
 */
function crud_sanitize_input($input, $type = 'string') {
    if ($input === null || $input === '') {
        return null;
    }
    switch ($type) {
        case 'int':
            return filter_var($input, FILTER_VALIDATE_INT) !== false ? (int)$input : null;
        case 'float':
            return filter_var($input, FILTER_VALIDATE_FLOAT) !== false ? (float)$input : null;
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL) ? trim($input) : null;
        case 'string':
        default:
            return trim(strip_tags($input));
    }
}

/**
 * Valida campos requeridos
 */
function crud_validate_required($data, $required_fields) {
    $errors = [];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
            $errors[] = "Campo '$field' es requerido";
        }
    }
    return $errors;
}

/**
 * Genera respuesta JSON estandarizada
 */
function crud_response($success, $message = '', $data = null) {
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    exit;
}
