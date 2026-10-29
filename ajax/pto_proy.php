<?php

if (is_file("funciones/conexion.php")){
	  include ("funciones/conexion.php");
        }
	else {
	  include ("../funciones/conexion.php");
}

$conditions = ["producto IN ('CLAVEL','MINICLAVEL','CLAVEL STANDARD','CLAVEL MINIATURA')"];
$types = '';
$params = [];

// La columna `estado` no existe en todos los entornos (p.ej. pruebas): filtrar solo si existe
$colEstado = $conexion->query("SHOW COLUMNS FROM budget LIKE 'estado'");
if ($colEstado && $colEstado->num_rows > 0) {
    array_unshift($conditions, 'estado = 1');
}

if (!empty($_POST)){
	$finicial = strval($_POST['fini'] ?? '');
	$ffinal = strval($_POST['ffin'] ?? '');
    $tipo = strval($_POST['tipo'] ?? '');

    if ($tipo != ""){
        $conditions[] = 'tipo = ?';
        $types .= 's';
        $params[] = $tipo;
    } elseif ($finicial != "" && $ffinal != "") {
        $conditions[] = 'fecha_siembra BETWEEN ? AND ?';
        $types .= 'ss';
        $params[] = $finicial;
        $params[] = $ffinal;
    }
}

$where = 'WHERE ' . implode(' AND ', $conditions);

$query="
        SELECT  finca,
                bloque,
                producto,
                variedad,
                temporada,
                color, 
                fecha_siembra,
                sum(plantas) as plantas,
                tipo,
                ciclo,
                codvari,
                cod_temporada,
                siembra 
            FROM budget 
            $where 
            GROUP BY finca, bloque, producto, variedad, temporada, fecha_siembra, tipo, siembra, color, ciclo, codvari, cod_temporada 
            ORDER BY fecha_siembra ASC
";

$stmt = $conexion->prepare($query);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$response = array();

while($row = $result->fetch_assoc()){
    $response[]=$row;
}

echo json_encode($response);

$conexion->close();

 ?> 