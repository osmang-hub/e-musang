<?php
$host     = "localhost";
$user     = "root";
$password = "";
$db_name  = "db_emusang";

$conn = mysqli_connect($host, $user, $password, $db_name);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
