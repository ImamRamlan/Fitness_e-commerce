<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

// Koneksi ke database
include 'koneksi.php';

// Set title halaman
$title = "Tambah Lokasi";
include 'include/header.php';
include 'include/navbar.php';

// Ambil data dari tabel pilih_lokasi
$query = "SELECT * FROM pilih_lokasi";
$result = $koneksi->query($query);
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Lokasi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tambah Lokasi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h5>Form Tambah Lokasi</h5>
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="proses_pilih_lokasi.php" method="POST">
                                <div class="form-group">
                                    <label for="titik_lokasi">Titik Lokasi:</label>
                                    <textarea class="form-control" name="titik_lokasi" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Tambah Lokasi</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar Lokasi -->
            <div class="row">
                <div class="col-12">
                    <h5>Daftar Lokasi yang Tersedia</h5>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Titik Lokasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Jika ada data lokasi
                                    if ($result->num_rows > 0) {
                                        // Menampilkan setiap data lokasi
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['titik_lokasi']) . "</td>";
                                           
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center'>Tidak ada data lokasi.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include 'include/footer.php'; 
?>
