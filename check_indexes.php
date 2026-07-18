<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- ld_proyecciones indexes ---\n";
$res = $conn->query("SHOW INDEX FROM ld_proyecciones");
while($row = $res->fetch_assoc()) {
    print_r($row);
}

echo "--- quipus_vs_proy indexes ---\n";
$res2 = $conn->query("SHOW INDEX FROM quipus_vs_proy");
while($row = $res2->fetch_assoc()) {
    print_r($row);
}

$conn->close();
