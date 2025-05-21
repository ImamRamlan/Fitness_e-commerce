<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

// Koneksi ke database
include 'koneksi.php';

// Cek apakah form sudah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $titik_lokasi = trim($_POST['titik_lokasi']);
    
    // Cek jika titik lokasi kosong
    if (!empty($titik_lokasi)) {
        // Siapkan query untuk menambahkan lokasi
        $query = "INSERT INTO pilih_lokasi (titik_lokasi) VALUES (?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("s", $titik_lokasi);
        
        // Eksekusi query
        if ($stmt->execute()) {
            // Redirect ke halaman lokasi setelah berhasil
            header("Location: titik_lokasi.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat menambahkan lokasi.";
        }
        
        // Tutup statement
        $stmt->close();
    } else {
        echo "Titik lokasi tidak boleh kosong.";
    }
}

// Tutup koneksi
$koneksi->close();
?>
