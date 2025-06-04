<?php
session_start();
unset($_SESSION["usuario"]);
unset($_SESSION["role"]);
unset($_SESSION['id']);
$_SESSION['role']=0;
header("Location:index.php");
?>