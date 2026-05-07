<?php
include 'koneksi.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    // ---- GET: Mengambil Data ----
    case 'GET':
        $sql = "SELECT * FROM users";
        $result = mysqli_query($koneksi, $sql);
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($users);
        break;

    // ---- POST: Tambah Data ----
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $nama = $input['nama'];
        $sandi = $input['sandi'];
        $sql = "INSERT INTO users (nama, sandi, aksi) VALUES ('$nama', '$sandi', 'Insert via API')";
        
        if(mysqli_query($koneksi, $sql)) {
            echo json_encode(["message" => "Data berhasil ditambah"]);
        } else {
            echo json_encode(["message" => "Gagal: " . mysqli_error($koneksi)]);
        }
        break;

    // ---- PUT: Update Data ----
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $nama = $input['nama'];
        $sandi = $input['sandi'];
        $sql = "UPDATE users SET nama='$nama', sandi='$sandi', aksi='Update via API' WHERE id=$id";
        
        if(mysqli_query($koneksi, $sql)) {
            echo json_encode(["message" => "Data berhasil diupdate"]);
        }
        break;

    // ---- DELETE: Hapus Data ----
    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $sql = "DELETE FROM users WHERE id=$id";
        
        if(mysqli_query($koneksi, $sql)) {
            echo json_encode(["message" => "Data berhasil dihapus"]);
        }
        break;
}
?>
