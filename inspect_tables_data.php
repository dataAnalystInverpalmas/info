<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- Row counts in ld_proyecciones ---\n";
$res = $conn->query("SELECT tipo, COUNT(*), SUM(tallos) FROM ld_proyecciones GROUP BY tipo");
while ($row = $res->fetch_row()) {
    echo "Tipo: {$row[0]} | Rows: {$row[1]} | Sum tallos: {$row[2]}\n";
}

echo "\n--- Sample from PT ---\n";
$res2 = $conn->query("SELECT * FROM ld_proyecciones WHERE tipo = 'PT' LIMIT 2");
while ($row = $res2->fetch_assoc()) {
    print_r($row);
}

echo "\n--- Sample from IN ---\n";
$res2 = $conn->query("SELECT * FROM ld_proyecciones WHERE tipo = 'IN' LIMIT 2");
while ($row = $res2->fetch_assoc()) {
    print_r($row);
}

echo "\n--- Sample from AJ ---\n";
$res2 = $conn->query("SELECT * FROM ld_proyecciones WHERE tipo = 'AJ' LIMIT 2");
while ($row = $res2->fetch_assoc()) {
    print_r($row);
}

$conn->close();
