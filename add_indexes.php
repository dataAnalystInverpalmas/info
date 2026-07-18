<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "Adding indexes to ld_proyecciones...\n";
$start = microtime(true);

$q1 = "ALTER TABLE ld_proyecciones ADD INDEX idx_lookup (finca(3), bloque, variedad(30), cosecha(10), fecha, tipo(2))";
if ($conn->query($q1)) {
    echo "Successfully indexed ld_proyecciones! Elapsed: " . round(microtime(true) - $start, 2) . "s\n";
} else {
    echo "Error indexing ld_proyecciones: " . $conn->error . "\n";
}

echo "Adding indexes to quipus_vs_proy...\n";
$start = microtime(true);
$q2 = "ALTER TABLE quipus_vs_proy ADD INDEX idx_q_lookup (finca(3), bloque, variedad(30), cosecha(10), sem_prod)";
if ($conn->query($q2)) {
    echo "Successfully indexed quipus_vs_proy! Elapsed: " . round(microtime(true) - $start, 2) . "s\n";
} else {
    echo "Error indexing quipus_vs_proy: " . $conn->error . "\n";
}

$conn->close();
