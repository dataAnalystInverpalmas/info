<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// add-comment.php

// Recibir los datos desde la petición AJAX
$pdf_id = $_POST['pdf_id'];
$user = $_POST['user'];
$comment = $_POST['comment'];
 
// Preparar la consulta
$stmt = $db->prepare('INSERT INTO comments (pdf_id, user, comment) VALUES (?, ?, ?)');
$stmt->bind_param('iss', $pdf_id, $user, $comment);
 
// Ejecutar la consulta
$stmt->execute();
 
// Devolver la respuesta en JSON
echo json_encode(['status' => 'success']);
 
// Cerrar la conexión a la base de datos
$db->close();
 
?>
