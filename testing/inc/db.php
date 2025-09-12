<?php
$host       = "localhost";
$user       = "root";
$pass       = "";
$db         = "dbvazatech";

$pdo    = mysqli_connect($host,$user,$pass,$db);
if(!$pdo){
    die("Koneksi Gagal");
}