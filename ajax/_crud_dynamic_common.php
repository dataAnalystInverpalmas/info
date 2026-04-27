<?php

function crud_json($payload)
{
    echo json_encode($payload);
    exit;
}

function crud_bind_and_execute($stmt, $types, $params)
{
    if ($types !== '') {
        $bind = array_merge([$types], $params);
        $refs = [];
        foreach ($bind as $k => $v) {
            $refs[$k] = &$bind[$k];
        }
        call_user_func_array([$stmt, 'bind_param'], $refs);
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
    return array_keys($meta['columns']);
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
    include(__DIR__ . '/../funciones/conexion.php');
    header('Content-Type: application/json; charset=utf-8');

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
        $val = crud_normalize_value($_POST[$col], $meta['columns'][$col]);
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
    crud_json(['success' => false, 'message' => $stmt->error]);
}

function crud_list_endpoint($table)
{
    include(__DIR__ . '/../funciones/conexion.php');
    header('Content-Type: application/json; charset=utf-8');

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
    include(__DIR__ . '/../funciones/conexion.php');
    header('Content-Type: application/json; charset=utf-8');

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
    include(__DIR__ . '/../funciones/conexion.php');
    header('Content-Type: application/json; charset=utf-8');

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
        $setParts[] = $col . ' = ?';
        $types .= crud_bind_type_for($meta['columns'][$col]['data_type']);
        $params[] = crud_normalize_value($_POST[$col], $meta['columns'][$col]);
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
    crud_json(['success' => false, 'message' => $stmt->error]);
}

function crud_delete_endpoint($table)
{
    include(__DIR__ . '/../funciones/conexion.php');
    header('Content-Type: application/json; charset=utf-8');

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
