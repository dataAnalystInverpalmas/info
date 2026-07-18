<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- CHECKING PT OR OTHER ROW MATCHES POST 2026-03-23 ---\n";
// Let's count how many rows in quipus_vs_proy with sem_prod in [2614, 2621] and inverpalmas-real > 0
// have a corresponding row in ld_proyecciones (matching finca, bloque, variedad, cosecha, fecha) with tipo = 'PT'.
$res = $conn->query("
    SELECT COUNT(*) as cnt
    FROM quipus_vs_proy q
    JOIN ld_proyecciones p ON p.finca = q.finca 
                          AND p.bloque = q.bloque 
                          AND p.variedad = q.variedad 
                          AND p.cosecha = q.cosecha 
                          AND p.fecha = STR_TO_DATE(CONCAT('20', FLOOR(q.sem_prod/100), ' ', q.sem_prod % 100, ' Monday'), '%Y %u %W')
    WHERE q.sem_prod >= 2614 AND q.sem_prod <= 2621 AND q.`inverpalmas-real` > 0 AND p.tipo = 'PT'
");
$row = $res->fetch_assoc();
echo "Matching 'PT' rows count: " . $row['cnt'] . "\n";

// Let's see the total positive real rows in quipus_vs_proy for those weeks:
$res2 = $conn->query("
    SELECT COUNT(*) as cnt
    FROM quipus_vs_proy 
    WHERE sem_prod >= 2614 AND sem_prod <= 2621 AND `inverpalmas-real` > 0
");
$row2 = $res2->fetch_assoc();
echo "Total positive 'inverpalmas-real' rows in quipus_vs_proy (weeks 2614-2621): " . $row2['cnt'] . "\n";

$conn->close();
