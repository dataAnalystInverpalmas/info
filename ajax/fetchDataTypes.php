<?php
//lamar conexion
include ('../funciones/conexion.php');

$tblCheck = $conexion->query("SHOW TABLES LIKE 'data_types'");
echo '<option value="">Tipo</option>';
if ($tblCheck && $tblCheck->num_rows > 0) {
    $sql = $conexion->query("select distinct name from data_types");
    while($row = mysqli_fetch_array($sql)) {
        echo "<option>" . htmlspecialchars($row['name']) . "</option>";
    }
}

?>