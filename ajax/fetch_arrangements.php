<?php
include('../funciones/conexion.php');

$res = $conexion->query("SELECT id, tipo, aplicar FROM informes.arrangement ORDER BY tipo, aplicar");
echo '<option value="">Arrangement</option>';
while ($row = mysqli_fetch_assoc($res)) {
    echo '<option value="' . (int)$row['id'] . '">' . htmlspecialchars($row['tipo'] . ' - ' . $row['aplicar'], ENT_QUOTES, 'UTF-8') . '</option>';
}
