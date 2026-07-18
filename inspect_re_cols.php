<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- DETAILS FOR FEW RE ROWS ---\n";
$res = $conn->query("SELECT * FROM ld_proyecciones WHERE tipo = 'RE' AND fecha >= '2026-03-01' LIMIT 5");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}

$conn->close();
