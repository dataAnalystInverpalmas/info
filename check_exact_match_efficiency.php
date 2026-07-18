<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

$sql = "
    SELECT 
        q.finca, 
        q.flor, 
        q.bloque, 
        q.cosecha, 
        q.variedad, 
        q.sem_prod, 
        q.`inverpalmas-real` as real_tallos, 
        q.color,
        STR_TO_DATE(CONCAT('20', FLOOR(q.sem_prod/100), ' ', q.sem_prod % 100, ' Monday'), '%Y %u %W') as computed_fecha,
        MIN(p_meta.matas) as matas,
        MIN(p_meta.edad) as edad,
        MIN(p_meta.ncolor) as ncolor,
        MIN(p_meta.finca) as meta_finca
    FROM quipus_vs_proy q
    LEFT JOIN ld_proyecciones p ON p.finca = q.finca 
                                AND p.bloque = q.bloque 
                                AND p.variedad = q.variedad 
                                AND p.cosecha = q.cosecha 
                                AND p.fecha = STR_TO_DATE(CONCAT('20', FLOOR(q.sem_prod/100), ' ', q.sem_prod % 100, ' Monday'), '%Y %u %W')
                                AND p.tipo = 'RE'
    LEFT JOIN ld_proyecciones p_meta ON p_meta.finca = q.finca 
                                AND p_meta.bloque = q.bloque 
                                AND p_meta.variedad = q.variedad 
                                AND p_meta.cosecha = q.cosecha 
                                AND p_meta.fecha = STR_TO_DATE(CONCAT('20', FLOOR(q.sem_prod/100), ' ', q.sem_prod % 100, ' Monday'), '%Y %u %W')
                                AND p_meta.tipo IN ('PT', 'IN', 'AJ')
    WHERE q.`inverpalmas-real` IS NOT NULL 
      AND q.`inverpalmas-real` > 0 
      AND p.finca IS NULL
    GROUP BY q.finca, q.flor, q.bloque, q.cosecha, q.variedad, q.sem_prod, q.`inverpalmas-real`, q.color, computed_fecha
";

$res = $conn->query($sql);
$total = 0;
$exact = 0;
while($row = $res->fetch_assoc()) {
    $total++;
    if ($row['meta_finca'] !== null) {
        $exact++;
    }
}
echo "Grouped missing: $total | Exact date matches: $exact\n";

$conn->close();
