<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

$weeks = [2601, 2602, 2609, 2610, 2613, 2614, 2621];
foreach ($weeks as $w) {
    // MySQL conversion
    $res = $conn->query("SELECT STR_TO_DATE(CONCAT('20', FLOOR($w/100), ' ', $w % 100, ' Monday'), '%Y %u %W') as dt1");
    $row = $res->fetch_assoc();
    $sql_dt = $row['dt1'];
    
    // PHP conversion
    $year = 2000 + floor($w / 100);
    $week = $w % 100;
    $dt = new DateTime();
    $dt->setISODate($year, $week, 1);
    $php_dt = $dt->format('Y-m-d');
    
    echo "Week: $w | MySQL: $sql_dt | PHP: $php_dt | Match: " . ($sql_dt === $php_dt ? "YES" : "NO") . "\n";
}

$conn->close();
