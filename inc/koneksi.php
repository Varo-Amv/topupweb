<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // lempar exception
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbvazatech";

$koneksi = mysqli_connect($host, $user, $pass, $db);
$koneksi->set_charset('utf8mb4');
