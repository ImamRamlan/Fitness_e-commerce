<?php
ob_start();
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

// Ambil data pengguna dari sesi
$username = $_SESSION['username'];
$nama_lengkap = $_SESSION['nama_lengkap'];
$user_id = $_SESSION['user_id'];

// Koneksi ke database
include 'koneksi.php';

// Ambil status filter dari URL (jika ada)
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';

// Buat query untuk mengambil pesanan berdasarkan status filter
$query_pesanan = "
    SELECT 
        p.222247_id_pesanan, 
        p.222247_tanggal_pesanan, 
        p.222247_status_pesanan, 
        p.222247_total_harga, 
        p.222247_jumlah,
        pr.222247_foto, 
        pr.222247_nama_produk
    FROM tbl_pesanan_222247 AS p
    JOIN tbl_produk_222247 AS pr 
        ON p.222247_id_produk = pr.222247_id_produk
    WHERE p.222247_id_pengguna = ?
";

// Jika ada filter status, tambahkan kondisi WHERE untuk status
if ($status_filter !== '') {
    $query_pesanan .= " AND p.222247_status_pesanan = ?";
}

// Persiapkan statement
$stmt = $koneksi->prepare($query_pesanan);

// Bind parameter untuk user_id dan status (jika ada filter status)
if ($status_filter !== '') {
    $stmt->bind_param("is", $user_id, $status_filter);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result_pesanan = $stmt->get_result();
$total_pesanan = $result_pesanan->num_rows; // Hitung total pesanan

// Set judul halaman
$title = "Daftar Pesanan dan Pembayaran";
include 'include/header.php';
include 'include/navbar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Daftar Pesanan dan Pembayaran</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pembayaran</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <!-- Filter Status -->
            <div class="row">
                <!-- Kartu Total Pesanan -->
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo htmlspecialchars($total_pesanan); ?></h3>
                            <p>Total Pesanan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
       

            <form action="checkout.php" method="POST">


                <div class="row">
                    <!-- Pesan Status -->
                    <?php
                    if (isset($_GET['status']) && isset($_GET['message'])) {
                        $status = $_GET['status'];
                        $message = urldecode($_GET['message']);

                        echo $status === 'success' ? "<div class='alert alert-success'>$message</div>" :
                            "<div class='alert alert-danger'>$message</div>";
                    }
                    ?>

                    <!-- Looping daftar pesanan -->
                    <?php if ($total_pesanan > 0): ?>
                        <?php while ($row = $result_pesanan->fetch_assoc()): ?>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="card mb-4 shadow-sm">
                                    <img src="pegawai/produk/<?php echo htmlspecialchars($row['222247_foto']); ?>" class="card-img-top" alt="Foto Produk">
                                    <div class="card-body">
                                        <h5 class="card-title"><b><?php echo htmlspecialchars($row['222247_nama_produk']); ?></b></h5>
                                        <p class="card-text">
                                            Tanggal: <?php echo date("d-m-Y H:i:s", strtotime($row['222247_tanggal_pesanan'])); ?><br>
                                            Status:
                                            <span class="badge bg-<?php echo ($row['222247_status_pesanan'] === 'Pesanan Diproses') ? 'warning' : 'secondary'; ?>">
                                                <?php echo htmlspecialchars($row['222247_status_pesanan']); ?>
                                            </span><br>
                                            Jumlah: <?php echo htmlspecialchars($row['222247_jumlah']); ?><br>
                                            Total Harga: Rp <?php echo number_format($row['222247_total_harga'], 2, ',', '.'); ?>
                                        </p>

                                        <!-- Tampilkan checkbox hanya jika status adalah "Pesanan Diproses" -->
                                        <?php if ($row['222247_status_pesanan'] === 'Pesanan Diproses'): ?>
                                            <input type="checkbox" name="pesanan_ids[]" value="<?php echo $row['222247_id_pesanan']; ?>">
                                            Pilih untuk checkout
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-warning text-center">
                                Tidak ada pesanan yang tersedia.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tombol Proses -->
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success">Checkout</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$stmt->close();
$koneksi->close();
include 'include/footer.php';
?>