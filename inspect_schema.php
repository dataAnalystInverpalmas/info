<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- TABLES LIST ---\n";
$res = $conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) {
    if (strpos($row[0], 'proy') !== false || strpos($row[0], 'quipus') !== false) {
        echo "Table: " . $row[0] . "\n";
    }
}

echo "\n--- quipus_vs_proy COLUMNS ---\n";
$res = $conn->query("SHOW COLUMNS FROM quipus_vs_proy");
while ($row = $res->fetch_assoc()) {
    echo "Field: " . $row['Field'] . " | Type: " . $row['Type'] . "\n";
}

echo "\n--- COMPARE COUNT quipus_vs_proy vs ld_proyecciones RE ---\n";
// Let's see some sample rows from both.
echo "--- Sample from ld_proyecciones where tipo = 'RE' ---\n";
$res = $conn->query("SELECT * FROM ld_proyecciones WHERE tipo = 'RE' LIMIT 3");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}

echo "--- Sample from quipus_vs_proy ---\n";
$res = $conn->query("SELECT * FROM quipus_vs_proy LIMIT 3");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}

$conn->close();
