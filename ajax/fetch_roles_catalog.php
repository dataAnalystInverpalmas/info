<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';
header('Content-Type: text/html; charset=utf-8');

$res = $conexion->query("SELECT id, nombre FROM roles_catalog WHERE activo = 1 ORDER BY id ASC");
echo '<option value="">-- Seleccionar Rol --</option>';
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $id = (int)$r['id'];
        $nombre = htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8');
        echo '<option value="' . $id . '">' . $id . ' - ' . $nombre . '</option>';
    }
}
