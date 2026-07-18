<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- WEEK 2621 IN ld_proyecciones (2026-05-18 to 2026-05-24) ---\n";
$res = $conn->query("SELECT tipo, SUM(tallos) as total FROM ld_proyecciones WHERE fecha BETWEEN '2026-05-18' AND '2026-05-24 23:59:59' GROUP BY tipo");
while ($row = $res->fetch_assoc()) {
    echo "Tipo: " . $row['tipo'] . " | Total: " . $row['total'] . "\n";
}

$conn->close();
