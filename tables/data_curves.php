<?php
//lamar conexion
if (is_file("funciones/conexion.php")){
	include ("funciones/conexion.php");
	}
	else {
	include ("../funciones/conexion.php");
}

//query to get data from the table
$query = sprintf("SELECT variedad, edad, fmata FROM table_curves ORDER BY id");

//execute query
$result = $conexion->query($query);

//loop through the returned data
$data = array();
foreach ($result as $row) {
  $data[] = $row;
}

//free memory associated with result
$result->close();

//close connection
$conexion->close();

//now print the data
print json_encode($data);

?>