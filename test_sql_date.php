<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

$sem_prod = 2610;
$query = "SELECT STR_TO_DATE(CONCAT(2000 + FLOOR($sem_prod/100), ' ', $sem_prod % 100, ' 1'), '%x %v %w') as dt1,
                 STR_TO_DATE(CONCAT(2000 + FLOOR($sem_prod/100), sprintf('%02d', $sem_prod % 100), ' Monday'), '%X%V %W') as dt2";

$res = $conn->query("SELECT STR_TO_DATE(CONCAT('20', FLOOR($sem_prod/100), ' ', $sem_prod % 100, ' Monday'), '%Y %u %W') as dt1");
$row = $res->fetch_assoc();
echo "STR_TO_DATE 2610 -> Monday: " . $row['dt1'] . "\n";

$conn->close();
