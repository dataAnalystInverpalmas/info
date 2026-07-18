<?php
include('../funciones/conexion.php');

$res = $conexion->query("SELECT id, nombre FROM breeders ORDER BY nombre ASC");
echo '<option value="">Casa comercial</option>';
while ($row = mysqli_fetch_assoc($res)) {
    echo '<option value="' . (int)$row['id'] . '">' . htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') . '</option>';
}
