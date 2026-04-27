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
        $proveedor = isset($_POST['proveedor']) ? intval($_POST['proveedor']) : 0;
        $remision  = isset($_POST['remision'])  ? $_POST['remision']  : '';
        $destino   = isset($_POST['destino'])   ? intval($_POST['destino'])   : 0;
        $material  = isset($_POST['material'])  ? $_POST['material']  : '';

        $stmt = $conexion->prepare(
            "INSERT INTO entrada_material_vegetal (fecha, maquila, proveedor, remision, destino, material)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssisis", $fecha, $maquila, $proveedor, $remision, $destino, $material);
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
        $proveedor = isset($_POST['proveedor']) ? intval($_POST['proveedor']) : 0;
        $remision  = isset($_POST['remision'])  ? $_POST['remision']  : '';
        $destino   = isset($_POST['destino'])   ? intval($_POST['destino'])   : 0;
        $material  = isset($_POST['material'])  ? $_POST['material']  : '';

        $stmt = $conexion->prepare(
            "UPDATE entrada_material_vegetal
             SET fecha=?, maquila=?, proveedor=?, remision=?, destino=?, material=?
             WHERE id=?"
        );
        $stmt->bind_param("ssisisi", $fecha, $maquila, $proveedor, $remision, $destino, $material, $id);
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
            "SELECT e.id, e.fecha, e.maquila,
                    e.proveedor AS proveedor_id,
                    COALESCE(bp.nombre, e.proveedor) AS proveedor,
                    e.remision,
                    e.destino AS destino_id,
                    COALESCE(bd.nombre, e.destino) AS destino,
                    e.material
             FROM entrada_material_vegetal e
             LEFT JOIN breeders bp ON bp.id = e.proveedor
             LEFT JOIN breeders bd ON bd.id = e.destino
             WHERE e.fecha BETWEEN ? AND ?
             ORDER BY e.fecha DESC, e.id DESC"
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
            "SELECT d.id, d.entrada_id,
                    d.variedad AS variedad_codigo,
                    COALESCE(v.nombre, d.variedad) AS variedad,
                    d.cantidad_recibida,
                    d.facturado, d.reposicion, d.excedente, d.obsequio, d.adicional,
                    d.raiz, d.observacion
             FROM entrada_material_vegetal_detalle d
             LEFT JOIN ld_variedades v ON v.codigo = d.variedad
             WHERE d.entrada_id=?
             ORDER BY d.id ASC"
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
    // breeders – lista de proveedores/destinos para los <select>
    // ---------------------------------------------------------------
    case 'breeders':
        $res  = $conexion->query("SELECT id, nombre FROM breeders ORDER BY nombre ASC");
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = ['id' => $row['id'], 'nombre' => $row['nombre']];
            }
        }
        echo json_encode($data);
        break;

    // ---------------------------------------------------------------
    // variedades – lista de variedades para el <select>
    // ---------------------------------------------------------------
    case 'variedades':
        $res  = $conexion->query("SELECT codigo, nombre, codflor FROM ld_variedades ORDER BY nombre ASC");
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = ['codigo' => $row['codigo'], 'nombre' => $row['nombre'], 'codflor' => $row['codflor']];
            }
        }
        echo json_encode($data);
        break;

    default:
        echo json_encode(['status' => 'error', 'msg' => 'Opcion invalida']);
        break;
}
