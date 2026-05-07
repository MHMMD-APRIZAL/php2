<?php
include 'koneksi.php';

// Header agar bisa diakses Postman & Browser
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $sql = "SELECT * FROM users";
        $result = mysqli_query($koneksi, $sql);
        if ($result) {
            $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
            echo json_encode($users);
        } else {
            echo json_encode(["message" => "Tabel users belum ada. Buat tabelnya dulu di SQL Console."]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['nama']) || !isset($input['sandi'])) {
            echo json_encode(["message" => "Data nama dan sandi wajib diisi."]);
            break;
        }
        $nama = $input['nama'];
        $sandi = $input['sandi'];
        $sql = "INSERT INTO users (nama, sandi, aksi) VALUES ('$nama', '$sandi', 'Insert via Postman')";
        if(mysqli_query($koneksi, $sql)) {
            echo json_encode(["status" => "success", "message" => "Data berhasil ditambah"]);
        } else {
            echo json_encode(["status" => "error", "message" => mysqli_error($koneksi)]);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $nama = $input['nama'];
        $sandi = $input['sandi'];
        $sql = "UPDATE users SET nama='$nama', sandi='$sandi', aksi='Update via API' WHERE id=$id";
        if(mysqli_query($koneksi, $sql)) {
            echo json_encode(["status" => "success", "message" => "Data ID $id berhasil diupdate"]);
        }
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $sql = "DELETE FROM users WHERE id=$id";
        if(mysqli_query($koneksi, $sql)) {
            echo json_encode(["status" => "success", "message" => "Data ID $id berhasil dihapus"]);
        }
        break;
}

mysqli_close($koneksi);
?>
