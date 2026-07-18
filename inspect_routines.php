<?php
$host = '172.10.18.128';
$username = 'root';
$password = 'AdmSys2014';
$database = 'informes';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("mysqli connect error: " . $conn->connect_error . "\n");
}

echo "--- PROCEDURES & FUNCTIONS ---\n";
$res = $conn->query("SHOW PROCEDURE STATUS WHERE Db = '$database'");
while ($row = $res->fetch_assoc()) {
    echo "Procedure: " . $row['Name'] . "\n";
}

$res = $conn->query("SHOW FUNCTION STATUS WHERE Db = '$database'");
while ($row = $res->fetch_assoc()) {
    echo "Function: " . $row['Name'] . "\n";
}

echo "\n--- TRIGGERS ---\n";
$res = $conn->query("SHOW TRIGGERS");
while ($row = $res->fetch_assoc()) {
    echo "Trigger: " . $row['Trigger'] . " on Table: " . $row['Table'] . " (Event: " . $row['Event'] . ")\n";
}

echo "\n--- EVENTS ---\n";
$res = $conn->query("SHOW EVENTS");
while ($row = $res->fetch_assoc()) {
    echo "Event: " . $row['Name'] . "\n";
}

$conn->close();
