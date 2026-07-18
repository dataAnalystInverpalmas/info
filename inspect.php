<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- TIPO RANGES IN ld_proyecciones ---\n";
$res = $conn->query("SELECT tipo, MIN(fecha) as min_f, MAX(fecha) as max_f, COUNT(*) as cnt FROM ld_proyecciones GROUP BY tipo");
while ($row = $res->fetch_assoc()) {
    echo "Tipo: " . $row['tipo'] . " | Min: " . $row['min_f'] . " | Max: " . $row['max_f'] . " | Count: " . $row['cnt'] . "\n";
}

$conn->close();
