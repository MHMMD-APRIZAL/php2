<?php
include 'koneksi.php';

// Set agar output berupa JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Menampilkan data
        $sql = "SELECT * FROM users";
        $result = mysqli_query($koneksi, $sql);
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($users);
        break;

    case 'POST':
        // Menambah data (ambil dari body Postman)
        $input = json_decode(file_get_contents('php://input'), true);
        $nama = $input['nama'];
        $sandi = $input['sandi'];
        $sql = "INSERT INTO users (nama, sandi, aksi) VALUES ('$nama', '$sandi', 'Dibuat via API')";
        if(mysqli_query($koneksi, $sql)) {
            echo json_encode(["status" => "success", "message" => "Data berhasil ditambah"]);
        } else {
            echo json_encode(["status" => "error", "message" => mysqli_error($koneksi)]);
        }
        break;

    case 'PUT':
        // Update data
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $nama = $input['nama'];
        $sandi = $input['sandi'];
        $sql = "UPDATE users SET nama='$nama', sandi='$sandi', aksi='Diupdate via API' WHERE id=$id";
        if(mysqli_query($koneksi, $sql)) {
            echo json_encode(["status" => "success", "message" => "Data ID $id berhasil diupdate"]);
        }
        break;

    case 'DELETE':
        // Hapus data
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
