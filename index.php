<?php
ob_start();
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    // Jika belum login, arahkan ke halaman login
    header('Location: login.php');
    exit();
}

// Ambil data pengguna dari sesi
$username = $_SESSION['username'];
$nama_lengkap = $_SESSION['nama_lengkap'];
$user_id = $_SESSION['user_id'];
$session_token = $_SESSION['session_token'];

// Ambil data yang relevan dari database
include 'koneksi.php'; // pastikan file koneksi.php sudah ada

// Ambil total produk
$query_produk = "SELECT COUNT(*) AS total_produk FROM tbl_produk_222247";
$result_produk = mysqli_query($koneksi, $query_produk);
$data_produk = mysqli_fetch_assoc($result_produk);
$total_produk = $data_produk['total_produk'];

// Ambil transaksi terakhir pengguna
$query_transaksi = "
    SELECT COUNT(*) AS total_transaksi 
    FROM tbl_pembayaran_222247 pb 
    JOIN tbl_pesanan_222247 ps ON pb.222247_id_pesanan = ps.222247_id_pesanan 
    WHERE ps.222247_id_pengguna = '$user_id'
";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);
$data_transaksi = mysqli_fetch_assoc($result_transaksi);
$total_transaksi = $data_transaksi['total_transaksi'];

// Set title halaman
$title = "Menu Sistem Informasi Alat Fitnes dan Suplement";
include 'include/header.php';
include 'include/navbar.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Menu Sistem Informasi Alat Fitnes dan Suplement</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Menu</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="row">
                <!-- Kartu Produk -->
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $total_produk; ?></h3>
                            <p>Total Produk Tersedia</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <a href="produk.php" class="small-box-footer">Lihat Produk <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- Kartu Riwayat Transaksi -->
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $total_transaksi; ?></h3>
                            <p>Transaksi Terakhir</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <a href="riwayat_pembayaran.php" class="small-box-footer">Lihat Riwayat <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- Kartu Pembayaran -->
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>Bayar</h3>
                            <p>Fitur Pembayaran</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <a href="pembayaran.php" class="small-box-footer">Lakukan Pembayaran <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <!-- Kartu Profil -->
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>Profil</h3>
                            <p>Kelola Profil Anda</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <a href="profil.php" class="small-box-footer">Lihat Profil <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Menampilkan informasi pengguna -->
            <div class="row mt-3">
                <div class="col-12">
                    <h4>Selamat datang, <?php echo htmlspecialchars($nama_lengkap); ?> (<?php echo htmlspecialchars($username); ?>)</h4>
                </div>
                <!-- Menampilkan informasi lokasi -->


            </div>
            <div class="row mt-3">

                <div class="col-12 text-center">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3973.7079632388995!2d119.44446607379243!3d-5.150624594826644!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbee300384275ab%3A0xf3b314735999f32c!2sKos%20Raldi%20(Porens)!5e0!3m2!1sen!2sid!4v1733052916863!5m2!1sen!2sid"
                        width="100%"
                        height="400"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>
                    <div class="col-12 text-center">
                        <h3>Lokasi Kami</h3>
                        <p>Kunjungi kami di lokasi berikut:</p>
                    </div>
                    <div class="mt-3">
                        <a href="https://maps.app.goo.gl/5vjWoahb9YEioBWA9" target="_blank" class="btn btn-primary">
                            Lihat di Google Maps
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>