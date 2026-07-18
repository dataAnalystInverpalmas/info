<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- COMPARING TIPO RE in ld_proyecciones with quipus_vs_proy ---\n";
// Let's find some records in ld_proyecciones where tipo = 'RE' and see if we can find a counterpart in quipus_vs_proy.
$res = $conn->query("SELECT * FROM ld_proyecciones WHERE tipo = 'RE' AND fecha >= '2026-03-01' LIMIT 5");
while ($row = $res->fetch_assoc()) {
    echo "\nld_proyecciones RE: Finca: {$row['finca']}, Flor: {$row['flor']}, Bloque: {$row['bloque']}, Variedad: {$row['variedad']}, Cosecha: {$row['cosecha']}, Fecha: {$row['fecha']}, Tallos: {$row['tallos']}\n";
    // Let's search in quipus_vs_proy
    // We can compute the week and year from fecha to see if it matches sem_prod.
    // E.g., for 2026-03-02, what is the yearweek?
    $date = new DateTime($row['fecha']);
    $year = $date->format('y'); // '26'
    $week = $date->format('W'); // week number
    $sem_prod = $year . $week;
    echo "Computed sem_prod: $sem_prod\n";
    
    $q_res = $conn->query("SELECT * FROM quipus_vs_proy WHERE finca = '{$row['finca']}' AND variedad = '{$row['variedad']}' AND bloque = {$row['bloque']} AND sem_prod = $sem_prod");
    if ($q_res && $q_res->num_rows > 0) {
        while ($q_row = $q_res->fetch_assoc()) {
            echo "MATCH in quipus_vs_proy: Cosecha: {$q_row['cosecha']}, sem_prod: {$q_row['sem_prod']}, inverpalmas-real: {$q_row['inverpalmas-real']}\n";
        }
    } else {
        echo "NO exact match by sem_prod. Let's do a wider search in quipus_vs_proy for this finca, bloque, variedad:\n";
        $q_res2 = $conn->query("SELECT * FROM quipus_vs_proy WHERE finca = '{$row['finca']}' AND variedad = '{$row['variedad']}' AND bloque = {$row['bloque']} LIMIT 2");
        while ($q_row = $q_res2->fetch_assoc()) {
            echo "  quipus_vs_proy: Cosecha: {$q_row['cosecha']}, sem_prod: {$q_row['sem_prod']}, inverpalmas-real: {$q_row['inverpalmas-real']}\n";
        }
    }
}

$conn->close();
