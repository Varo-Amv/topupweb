<?php
$host       = "localhost";
$user       = "root";
$pass       = "";
$db         = "dbvazatech";

$koneksi    = mysqli_connect($host,$user,$pass,$db);
if(!$koneksi){
    die("Koneksi Gagal");
}