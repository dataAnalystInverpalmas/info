<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$accion = $_REQUEST['accion'] ?? '';
$UPLOAD_DIR_BASE = __DIR__ . '/../archivos/imagenes_tareas/';
$URL_BASE        = ($_GLOBALS['src'] ?? '') . '/archivos/imagenes_tareas/';

// Tipos MIME permitidos (validamos con finfo, no solo extensión)
$MIME_PERMITIDOS = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$EXT_PERMITIDAS  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$TAMANO_MAX      = 5 * 1024 * 1024; // 5 MB

switch ($accion) {

    // ── Listar imágenes de una tarea ─────────────────────────────────────────
    case 'list':
        $tarea_id = (int)($_GET['tarea_id'] ?? 0);
        if ($tarea_id <= 0) { echo json_encode(['success' => false, 'mensaje' => 'tarea_id requerido']); exit; }

        $stmt = $conexion->prepare(
            "SELECT id, ruta_relativa, nombre_original, mime, size_bytes, subido_por, created_at
             FROM tarea_imagenes WHERE tarea_id=? AND estado='activo' ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $tarea_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $imgs = [];
        while ($r = $res->fetch_assoc()) {
            $r['url'] = ($_GLOBALS['src'] ?? '') . '/' . $r['ruta_relativa'];
            $imgs[] = $r;
        }
        echo json_encode(['success' => true, 'data' => $imgs]);
        break;

    // ── Subir imagen ─────────────────────────────────────────────────────────
    case 'upload':
        $tarea_id = (int)($_POST['tarea_id'] ?? 0);
        if ($tarea_id <= 0) { echo json_encode(['success' => false, 'mensaje' => 'tarea_id requerido']); exit; }
        if (empty($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'mensaje' => 'No se recibió archivo válido']); exit;
        }

        $file = $_FILES['imagen'];

        // Validar tamaño
        if ($file['size'] > $TAMANO_MAX) {
            echo json_encode(['success' => false, 'mensaje' => 'Archivo supera el límite de 5 MB']); exit;
        }

        // Validar extensión
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $EXT_PERMITIDAS, true)) {
            echo json_encode(['success' => false, 'mensaje' => 'Extensión no permitida']); exit;
        }

        // Validar MIME real con finfo (no confiar en $_FILES['type'])
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($file['tmp_name']);
        if (!in_array($mimeReal, $MIME_PERMITIDOS, true)) {
            echo json_encode(['success' => false, 'mensaje' => 'Tipo de archivo no permitido']); exit;
        }

        // Crear carpeta destino
        $carpeta = $UPLOAD_DIR_BASE . $tarea_id . '/';
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        // Nombre único + hash para detectar duplicados
        $hash    = hash_file('sha256', $file['tmp_name']);
        $nombreFinal = $hash . '_' . time() . '.' . $ext;
        $rutaFisica  = $carpeta . $nombreFinal;
        $rutaRelativa = 'archivos/imagenes_tareas/' . $tarea_id . '/' . $nombreFinal;

        if (!move_uploaded_file($file['tmp_name'], $rutaFisica)) {
            echo json_encode(['success' => false, 'mensaje' => 'Error al mover el archivo']); exit;
        }

        $nombre_original = basename($file['name']);
        $size            = (int)$file['size'];
        $subido_por      = $_SESSION['usuario'] ?? 'Sistema';

        $stmt = $conexion->prepare(
            "INSERT INTO tarea_imagenes (tarea_id, ruta_relativa, nombre_original, mime, size_bytes, hash_sha256, subido_por)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("isssiss", $tarea_id, $rutaRelativa, $nombre_original, $mimeReal, $size, $hash, $subido_por);
        $ok = $stmt->execute();

        if ($ok) {
            // Registrar en bitácora
            $img_id = $conexion->insert_id;
            $desc_b = 'Imagen adjunta: ' . $nombre_original;
            $b = $conexion->prepare(
                "INSERT INTO bitacora (tarea_id, tipo_registro, descripcion, autor) VALUES (?, 'imagen', ?, ?)"
            );
            $b->bind_param("iss", $tarea_id, $desc_b, $subido_por);
            $b->execute();

            $url = ($_GLOBALS['src'] ?? '') . '/' . $rutaRelativa;
            echo json_encode(['success' => true, 'id' => $img_id, 'url' => $url, 'ruta' => $rutaRelativa]);
        } else {
            // Revertir archivo si falla la BD
            @unlink($rutaFisica);
            echo json_encode(['success' => false, 'mensaje' => $conexion->error]);
        }
        break;

    // ── Eliminar imagen (soft delete) ────────────────────────────────────────
    case 'delete':
        $id       = (int)($_POST['id'] ?? 0);
        $tarea_id = (int)($_POST['tarea_id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success' => false, 'mensaje' => 'ID requerido']); exit; }

        // Verificar que la imagen pertenece a la tarea (si se envió tarea_id)
        $stmt = $conexion->prepare("SELECT ruta_relativa, tarea_id FROM tarea_imagenes WHERE id=? AND estado='activo' LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $img = $stmt->get_result()->fetch_assoc();
        if (!$img) { echo json_encode(['success' => false, 'mensaje' => 'Imagen no encontrada']); exit; }
        if ($tarea_id > 0 && (int)$img['tarea_id'] !== $tarea_id) {
            echo json_encode(['success' => false, 'mensaje' => 'Imagen no pertenece a la tarea']); exit;
        }

        $upd = $conexion->prepare("UPDATE tarea_imagenes SET estado='eliminado' WHERE id=?");
        $upd->bind_param("i", $id);
        $ok = $upd->execute();

        if ($ok) {
            $subido_por = $_SESSION['usuario'] ?? 'Sistema';
            $desc_b = 'Imagen eliminada: ' . basename($img['ruta_relativa']);
            $tid = (int)$img['tarea_id'];
            $b = $conexion->prepare(
                "INSERT INTO bitacora (tarea_id, tipo_registro, descripcion, autor) VALUES (?, 'imagen', ?, ?)"
            );
            $b->bind_param("iss", $tid, $desc_b, $subido_por);
            $b->execute();
        }

        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Eliminada' : $conexion->error]);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Acción inválida']);
        break;
}
