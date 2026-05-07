<?php
// Set header agar output berupa JSON dan mengizinkan akses CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include koneksi database
include 'koneksi.php';

// Menangkap metode request (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // ========================================
        // LOGIKA GET (READ DATA)
        // ========================================
        if (isset($_GET['id'])) {
            // Ambil 1 data berdasarkan ID
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM users WHERE id = $id";
            $result = mysqli_query($koneksi, $sql);
            
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                echo json_encode([
                    "status" => 200,
                    "message" => "Berhasil mengambil data user",
                    "data" => $row
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status" => 404,
                    "message" => "Data tidak ditemukan",
                    "data" => null
                ]);
            }
        } else {
            // Ambil semua data
            $sql = "SELECT * FROM users";
            $result = mysqli_query($koneksi, $sql);
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            echo json_encode([
                "status" => 200,
                "message" => "Berhasil mengambil semua data",
                "data" => $data
            ]);
        }
        break;

    case 'POST':
        // ========================================
        // LOGIKA POST (CREATE DATA)
        // ========================================
        // Bisa menerima JSON dari raw body atau Form Data biasa
        $data = json_decode(file_get_contents("php://input"), true);
        $nama = isset($data['nama']) ? $data['nama'] : (isset($_POST['nama']) ? $_POST['nama'] : null);
        $sandi = isset($data['sandi']) ? $data['sandi'] : (isset($_POST['sandi']) ? $_POST['sandi'] : null);

        if (!empty($nama) && !empty($sandi)) {
            // Escape string untuk mencegah SQL Injection basic
            $nama = mysqli_real_escape_string($koneksi, $nama);
            $sandi = mysqli_real_escape_string($koneksi, $sandi);

            $sql = "INSERT INTO users (nama, sandi) VALUES ('$nama', '$sandi')";
            if (mysqli_query($koneksi, $sql)) {
                http_response_code(201);
                echo json_encode([
                    "status" => 201,
                    "message" => "Data berhasil ditambahkan"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "status" => 500,
                    "message" => "Gagal menambahkan data: " . mysqli_error($koneksi)
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "Data 'nama' dan 'sandi' tidak boleh kosong"
            ]);
        }
        break;

    case 'PUT':
        // ========================================
        // LOGIKA PUT (UPDATE DATA)
        // ========================================
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Cek ID dari JSON body atau URL query parameter
        $id = isset($data['id']) ? intval($data['id']) : (isset($_GET['id']) ? intval($_GET['id']) : null);
        
        if ($id) {
            $nama = isset($data['nama']) ? $data['nama'] : null;
            $sandi = isset($data['sandi']) ? $data['sandi'] : null;

            if ($nama || $sandi) {
                $updateFields = [];
                if ($nama) {
                    $nama = mysqli_real_escape_string($koneksi, $nama);
                    $updateFields[] = "nama = '$nama'";
                }
                if ($sandi) {
                    $sandi = mysqli_real_escape_string($koneksi, $sandi);
                    $updateFields[] = "sandi = '$sandi'";
                }

                $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = $id";
                if (mysqli_query($koneksi, $sql)) {
                    // Cek apakah ada baris yang benar-benar diupdate
                    if (mysqli_affected_rows($koneksi) > 0) {
                        echo json_encode([
                            "status" => 200,
                            "message" => "Data berhasil diupdate"
                        ]);
                    } else {
                        echo json_encode([
                            "status" => 200,
                            "message" => "Tidak ada perubahan data atau ID tidak ditemukan"
                        ]);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode([
                        "status" => 500,
                        "message" => "Gagal mengupdate data: " . mysqli_error($koneksi)
                    ]);
                }
            } else {
                http_response_code(400);
                echo json_encode([
                    "status" => 400,
                    "message" => "Data 'nama' atau 'sandi' yang ingin diupdate tidak boleh kosong"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "Parameter 'id' tidak boleh kosong"
            ]);
        }
        break;

    case 'DELETE':
        // ========================================
        // LOGIKA DELETE (HAPUS DATA)
        // ========================================
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Cek ID dari JSON body atau URL query parameter
        $id = isset($data['id']) ? intval($data['id']) : (isset($_GET['id']) ? intval($_GET['id']) : null);

        if ($id) {
            $sql = "DELETE FROM users WHERE id = $id";
            if (mysqli_query($koneksi, $sql)) {
                if (mysqli_affected_rows($koneksi) > 0) {
                    echo json_encode([
                        "status" => 200,
                        "message" => "Data berhasil dihapus"
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        "status" => 404,
                        "message" => "Gagal dihapus: ID tidak ditemukan"
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode([
                    "status" => 500,
                    "message" => "Gagal menghapus data: " . mysqli_error($koneksi)
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "Parameter 'id' tidak boleh kosong"
            ]);
        }
        break;

    default:
        // ========================================
        // METODE TIDAK DIKENAL
        // ========================================
        http_response_code(405);
        echo json_encode([
            "status" => 405,
            "message" => "Method tidak diizinkan. Gunakan GET, POST, PUT, atau DELETE."
        ]);
        break;
}
?>
