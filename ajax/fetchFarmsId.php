<?php
//lamar conexion
include ('../funciones/conexion.php');

$sql = $conexion->query("select id, nombre from farms order by nombre");
?>
<option value="">Finca</option>
<?php
while ($row = mysqli_fetch_array($sql)) {
    echo "<option value=\"" . htmlspecialchars($row['id']) . "\">" . htmlspecialchars($row['nombre']) . "</option>";
}
?>