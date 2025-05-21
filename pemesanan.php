<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

// Ambil data pengguna dari sesi
$user_id = $_SESSION['user_id'];
$nama_lengkap = $_SESSION['nama_lengkap'];

// Koneksi ke database
include 'koneksi.php';

// Cek apakah ID produk ada di URL
if (!isset($_GET['id'])) {
    echo "Produk tidak ditemukan.";
    exit();
}

$id_produk = intval($_GET['id']);

// Ambil data produk berdasarkan ID
$query_produk = "SELECT * FROM tbl_produk_222247 WHERE 222247_id_produk = ?";
$stmt = mysqli_prepare($koneksi, $query_produk);
mysqli_stmt_bind_param($stmt, 'i', $id_produk);
mysqli_stmt_execute($stmt);
$result_produk = mysqli_stmt_get_result($stmt);

if ($result_produk && mysqli_num_rows($result_produk) > 0) {
    $produk = mysqli_fetch_assoc($result_produk);
} else {
    echo "Produk tidak ditemukan.";
    exit();
}
?>

<?php include 'include/header.php'; ?>
<?php include 'include/navbar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Pesan Produk</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pesan Produk</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="row">
                <!-- Informasi Produk -->
                <div class="col-lg-6 col-12">
                    <div class="card">
                        <img src="pegawai/produk/<?php echo htmlspecialchars($produk['222247_foto']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produk['222247_nama_produk']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($produk['222247_nama_produk']); ?></h5>
                            <p class="card-text">
                                Kategori: <?php echo htmlspecialchars($produk['222247_kategori']); ?><br>
                                Harga: Rp<?php echo number_format($produk['222247_harga'], 2, ',', '.'); ?><br>
                                Stok: <?php echo htmlspecialchars($produk['222247_stok']); ?><br>
                                Deskripsi: <?php echo htmlspecialchars($produk['222247_deskripsi']); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Pemesanan -->
                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Form Pemesanan</h5>
                            <form action="proses_pengguna/p_pemesanan.php" method="POST">
                                <input type="hidden" name="id_produk" value="<?php echo $id_produk; ?>">
                                <div class="form-group">
                                    <label for="jumlah">Jumlah</label>
                                    <input type="number" id="jumlah" name="jumlah" class="form-control" value="1" min="1" max="<?php echo htmlspecialchars($produk['222247_stok']); ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Pesan Sekarang</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>
