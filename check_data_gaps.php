<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- quipus_vs_proy range in inverpalmas-real ---\n";
$res = $conn->query("SELECT MIN(sem_prod) as min_sem, MAX(sem_prod) as max_sem, COUNT(*) as cnt 
                     FROM quipus_vs_proy 
                     WHERE `inverpalmas-real` IS NOT NULL AND `inverpalmas-real` > 0");
if ($row = $res->fetch_assoc()) {
    echo "Min sem_prod: {$row['min_sem']} | Max sem_prod: {$row['max_sem']} | Count of positive real: {$row['cnt']}\n";
}

echo "\n--- Count of non-empty inverpalmas-real in quipus_vs_proy for sem_prod >= 2613 ---\n";
$res2 = $conn->query("SELECT sem_prod, COUNT(*) as cnt, SUM(`inverpalmas-real`) as sum_real 
                      FROM quipus_vs_proy 
                      WHERE `inverpalmas-real` IS NOT NULL AND `inverpalmas-real` > 0 AND sem_prod >= 2613 
                      GROUP BY sem_prod 
                      ORDER BY sem_prod");
while ($row = $res2->fetch_assoc()) {
    echo "sem_prod: {$row['sem_prod']} | Rows: {$row['cnt']} | Total real tallos: {$row['sum_real']}\n";
}

$conn->close();
