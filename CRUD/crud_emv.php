<?php

if (is_file("funciones/conexion.php")) {
    include("funciones/conexion.php");
} else {
    include("../funciones/conexion.php");
}

header('Content-Type: application/json; charset=utf-8');

$opcion     = isset($_POST['opcion'])     ? $_POST['opcion']     : '';
$id         = isset($_POST['id'])         ? intval($_POST['id']) : 0;
$entrada_id = isset($_POST['entrada_id']) ? intval($_POST['entrada_id']) : 0;

switch ($opcion) {

    // ---------------------------------------------------------------
    // 1 – INSERT cabecera
    // ---------------------------------------------------------------
    case '1':
        $fecha     = isset($_POST['fecha'])     ? $_POST['fecha']     : '';
        $maquila   = isset($_POST['maquila'])   ? $_POST['maquila']   : '';
        $proveedor = isset($_POST['proveedor']) ? $_POST['proveedor'] : '';
        $remision  = isset($_POST['remision'])  ? $_POST['remision']  : '';
        $destino   = isset($_POST['destino'])   ? $_POST['destino']   : '';
        $material  = isset($_POST['material'])  ? $_POST['material']  : '';

        $stmt = $conexion->prepare(
            "INSERT INTO entrada_material_vegetal (fecha, maquila, proveedor, remision, destino, material)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssss", $fecha, $maquila, $proveedor, $remision, $destino, $material);
        $stmt->execute();
        $new_id = $conexion->insert_id;
        $stmt->close();
        echo json_encode(['status' => 'ok', 'id' => $new_id]);
        break;

    // ---------------------------------------------------------------
    // 2 – UPDATE cabecera
    // ---------------------------------------------------------------
    case '2':
        $fecha     = isset($_POST['fecha'])     ? $_POST['fecha']     : '';
        $maquila   = isset($_POST['maquila'])   ? $_POST['maquila']   : '';
        $proveedor = isset($_POST['proveedor']) ? $_POST['proveedor'] : '';
        $remision  = isset($_POST['remision'])  ? $_POST['remision']  : '';
        $destino   = isset($_POST['destino'])   ? $_POST['destino']   : '';
        $material  = isset($_POST['material'])  ? $_POST['material']  : '';

        $stmt = $conexion->prepare(
            "UPDATE entrada_material_vegetal
             SET fecha=?, maquila=?, proveedor=?, remision=?, destino=?, material=?
             WHERE id=?"
        );
        $stmt->bind_param("ssssssi", $fecha, $maquila, $proveedor, $remision, $destino, $material, $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'ok']);
        break;

    // ---------------------------------------------------------------
    // 3 – DELETE cabecera (+ sus detalles)
    // ---------------------------------------------------------------
    case '3':
        $stmt = $conexion->prepare("DELETE FROM entrada_material_vegetal_detalle WHERE entrada_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conexion->prepare("DELETE FROM entrada_material_vegetal WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'ok']);
        break;

    // ---------------------------------------------------------------
    // 4 – LIST cabeceras (filtro de fechas)
    // ---------------------------------------------------------------
    case '4':
        $fi = isset($_POST['fecha_ini']) ? $_POST['fecha_ini'] : '';
        $ff = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';

        $stmt = $conexion->prepare(
            "SELECT id, fecha, maquila, proveedor, remision, destino, material
             FROM entrada_material_vegetal
             WHERE fecha BETWEEN ? AND ?
             ORDER BY fecha DESC, id DESC"
        );
        $stmt->bind_param("ss", $fi, $ff);
        $stmt->execute();
        $res  = $stmt->get_result();
        $data = [];
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        echo json_encode($data);
        break;

    // ---------------------------------------------------------------
    // 5 – LIST detalles por entrada_id
    // ---------------------------------------------------------------
    case '5':
        $stmt = $conexion->prepare(
            "SELECT id, entrada_id, variedad, cantidad_recibida,
                    facturado, reposicion, excedente, obsequio, adicional,
                    raiz, observacion
             FROM entrada_material_vegetal_detalle
             WHERE entrada_id=?
             ORDER BY id ASC"
        );
        $stmt->bind_param("i", $entrada_id);
        $stmt->execute();
        $res  = $stmt->get_result();
        $data = [];
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        echo json_encode($data);
        break;

    // ---------------------------------------------------------------
    // 6 – DELETE detalle
    // ---------------------------------------------------------------
    case '6':
        $stmt = $conexion->prepare("DELETE FROM entrada_material_vegetal_detalle WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'ok']);
        break;

    // ---------------------------------------------------------------
    // 7 – INSERT detalle
    //   cantidad_recibida = facturado + reposicion + excedente + obsequio + adicional
    // ---------------------------------------------------------------
    case '7':
        $variedad   = isset($_POST['variedad'])   ? $_POST['variedad']   : '';
        $facturado  = isset($_POST['facturado'])  ? intval($_POST['facturado'])  : 0;
        $reposicion = isset($_POST['reposicion']) ? intval($_POST['reposicion']) : 0;
        $excedente  = isset($_POST['excedente'])  ? intval($_POST['excedente'])  : 0;
        $obsequio   = isset($_POST['obsequio'])   ? intval($_POST['obsequio'])   : 0;
        $adicional  = isset($_POST['adicional'])  ? intval($_POST['adicional'])  : 0;
        $cantidad_recibida = $facturado + $reposicion + $excedente + $obsequio + $adicional;
        $raiz       = isset($_POST['raiz'])       ? intval($_POST['raiz'])       : 0;
        $observacion= isset($_POST['observacion'])? $_POST['observacion']        : '';

        // types: i s i i i i i i i s  (10 params)
        $stmt = $conexion->prepare(
            "INSERT INTO entrada_material_vegetal_detalle
             (entrada_id, variedad, cantidad_recibida, facturado, reposicion, excedente, obsequio, adicional, raiz, observacion)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "isiiiiiiis",
            $entrada_id, $variedad, $cantidad_recibida,
            $facturado, $reposicion, $excedente, $obsequio, $adicional,
            $raiz, $observacion
        );
        $stmt->execute();
        $new_id = $conexion->insert_id;
        $stmt->close();
        echo json_encode(['status' => 'ok', 'id' => $new_id]);
        break;

    // ---------------------------------------------------------------
    // breeders – lista de proveedores para el <select>
    // ---------------------------------------------------------------
    case 'breeders':
        $res  = $conexion->query("SELECT nombre FROM breeders ORDER BY nombre ASC");
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row['nombre'];
            }
        }
        echo json_encode($data);
        break;

    // ---------------------------------------------------------------
    // variedades – lista de variedades para el <datalist>
    // ---------------------------------------------------------------
    case 'variedades':
        $res  = $conexion->query("SELECT nombre FROM ld_variedades ORDER BY nombre ASC");
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row['nombre'];
            }
        }
        echo json_encode($data);
        break;

    default:
        echo json_encode(['status' => 'error', 'msg' => 'Opcion invalida']);
        break;
}
