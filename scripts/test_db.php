<?php
error_reporting(E_ALL);
$c = new mysqli('localhost','root','','informes');
if ($c->connect_error) {
    echo "FAIL: " . $c->connect_error . "\n";
} else {
    echo "OK local root\n";
    $c->close();
}

$c2 = new mysqli('db','inverpalmas','Inver2020!','informes',3306);
if ($c2->connect_error) {
    echo "FAIL db: " . $c2->connect_error . "\n";
} else {
    echo "OK db host\n";
    $c2->close();
}

$c3 = new mysqli('localhost','inverpalmas','Inver2020!','informes',3306);
if ($c3->connect_error) {
    echo "FAIL local inverpalmas: " . $c3->connect_error . "\n";
} else {
    echo "OK local inverpalmas\n";
    $c3->close();
}
