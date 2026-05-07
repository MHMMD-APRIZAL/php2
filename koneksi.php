<?php
$host = getenv('MYSQLHOST') ?: 'localhost';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$port = getenv('MYSQLPORT') ?: 3306;

$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Koneksi Gagal: " . mysqli_connect_error()]);
    exit;
}
?>
