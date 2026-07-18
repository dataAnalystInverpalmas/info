<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- MATCHING MULTIPLE TYPES TO quipus_vs_proy columns ---\n";
// Let's grab a combination of finca, bloque, variedad, cosecha, fecha for a specific week:
// e.g. finca 001, bloque 16, variedad FRONTERA, cosecha NO2547, date 2026-03-02 (sem_prod 2610)
$finca = '001';
$bloque = 16;
$variedad = 'FRONTERA';
$cosecha = 'NO2547';
$fecha = '2026-03-02';
$sem_prod = 2610;

echo "--- quipus_vs_proy row: ---\n";
$res_q = $conn->query("SELECT * FROM quipus_vs_proy WHERE finca='$finca' AND bloque=$bloque AND variedad='$variedad' AND cosecha='$cosecha' AND sem_prod=$sem_prod");
if ($row_q = $res_q->fetch_assoc()) {
    print_r($row_q);
}

echo "\n--- ld_proyecciones rows: ---\n";
$res_l = $conn->query("SELECT * FROM ld_proyecciones WHERE finca='$finca' AND bloque=$bloque AND variedad='$variedad' AND cosecha='$cosecha' AND fecha='$fecha'");
while ($row_l = $res_l->fetch_assoc()) {
    echo "Tipo: {$row_l['tipo']} | Tallos: {$row_l['tallos']} | Matas: {$row_l['matas']} | Edad: {$row_l['edad']} | Color/NColor: {$row_l['ncolor']}\n";
}

$conn->close();
