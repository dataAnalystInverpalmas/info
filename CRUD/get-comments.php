<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Recibir el ID del PDF desde la petición AJAX
$pdf_id = $_GET['pdf_id'];
 
// Preparar la consulta
$stmt = $db->prepare('SELECT * FROM comments WHERE pdf_id = ?');
$stmt->bind_param('i', $pdf_id);
 
// Ejecutar la consulta
$stmt->execute();
 
// Obtener los resultados
$result = $stmt->get_result();
 
// Devolver los resultados en un arreglo
$comments = [];
while ($row = $result->fetch_assoc()) {
  $comments[] = $row;
}
 
// Devolver los resultados en JSON
echo json_encode($comments);
 
// Cerrar la conexión a la base de datos
$db->close();
 
?>
