<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

// Koneksi ke database
include 'koneksi.php';

// Menentukan jumlah data per halaman
$limit = 3;

// Mengambil nomor halaman dari URL, jika tidak ada maka halaman 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit; // Menghitung offset berdasarkan halaman

// Query untuk menghitung total pembayaran
$query_total = "SELECT COUNT(*) AS total FROM tbl_pembayaran_222247 AS p
                JOIN tbl_pesanan_222247 AS pes ON p.222247_id_pesanan = pes.222247_id_pesanan
                WHERE pes.222247_id_pengguna = ?";
$stmt_total = $koneksi->prepare($query_total);
$stmt_total->bind_param("i", $_SESSION['user_id']);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_data = $result_total->fetch_assoc()['total']; // Total data pembayaran

// Query untuk mengambil data pembayaran dengan join ke tbl_info_222247
$query_pembayaran = "SELECT p.222247_id_pembayaran, p.222247_tanggal_pembayaran, 
                        p.222247_status_pembayaran, p.222247_bukti_pembayaran,
                        p.222247_metode, pr.222247_nama_produk, pes.222247_jumlah, pes.222247_total_harga,
                        i.222247_alamat AS info_alamat, i.222247_no_wa, i.222247_titik_lokasi
                    FROM tbl_pembayaran_222247 AS p
                    JOIN tbl_pesanan_222247 AS pes ON p.222247_id_pesanan = pes.222247_id_pesanan
                    JOIN tbl_produk_222247 AS pr ON pes.222247_id_produk = pr.222247_id_produk
                    LEFT JOIN tbl_info_222247 AS i ON i.222247_id_pembayaran = p.222247_id_pembayaran
                    WHERE pes.222247_id_pengguna = ?
                    LIMIT ?, ?
                    ";
$stmt = $koneksi->prepare($query_pembayaran);
$stmt->bind_param("iii", $_SESSION['user_id'], $offset, $limit);
$stmt->execute();
$result_pembayaran = $stmt->get_result();

$total_pembayaran = $result_pembayaran->num_rows; // Menghitung total pembayaran

// Set title halaman
$title = "Riwayat Pembayaran";
include 'include/header.php';
include 'include/navbar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Riwayat Pembayaran</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Riwayat Pembayaran</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="row">
                <!-- Kartu Total Pembayaran -->
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo htmlspecialchars($total_pembayaran); ?></h3>
                            <p>Total Pembayaran Tersedia</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Cek apakah ada pesan status di URL
            if (isset($_GET['status']) && isset($_GET['message'])) {
                $status = $_GET['status'];
                $message = urldecode($_GET['message']); // Decode URL-encoded message

                if ($status == 'success') {
                    echo "<div class='alert alert-success'>$message</div>";
                } elseif ($status == 'error') {
                    echo "<div class='alert alert-danger'>$message</div>";
                }
            }
            ?>
            <div class="row">
                <!-- Daftar Pembayaran dalam bentuk kartu -->
                <?php if ($total_pembayaran > 0): ?>
                    <?php while ($row = $result_pembayaran->fetch_assoc()): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title"><b>Nama Produk: <?php echo htmlspecialchars($row['222247_nama_produk']); ?></b></h5>
                                    <p class="card-text">
                                        Tanggal Pembayaran: <?php echo date("d-m-Y H:i:s", strtotime($row['222247_tanggal_pembayaran'])); ?><br>
                                        Status Pembayaran:
                                        <span class="badge <?php echo $row['222247_status_pembayaran'] == 'Selesai' ? 'bg-success' : 'bg-warning'; ?>">
                                            <?php echo htmlspecialchars($row['222247_status_pembayaran']); ?>
                                        </span><br>
                                        Jumlah: <?php echo htmlspecialchars($row['222247_jumlah']); ?><br>
                                        Total Harga: <?php echo number_format($row['222247_total_harga'], 0, ',', '.'); ?><br>
                                        Nomor WhatsApp: <?php echo htmlspecialchars($row['222247_no_wa']); ?><br>
                                        Alamat Info: <?php echo htmlspecialchars($row['info_alamat']); ?><br>
                                        Titik Lokasi: <?php echo htmlspecialchars($row['222247_titik_lokasi']); ?><br>

                                        <!-- Menampilkan Bukti Pembayaran jika metode bukan COD -->
                                        <?php if ($row['222247_metode'] !== 'cod'): ?>
                                            <b>Bukti Pembayaran:</b><br>
                                            <img src="pegawai/bukti_pembayaran/<?php echo htmlspecialchars($row['222247_bukti_pembayaran']); ?>"
                                                class="img-thumbnail" alt="Bukti Pembayaran" style="max-width: 100%; height: auto;">
                                        <?php else: ?>
                                    <div class="alert alert-info">
                                        <b>Bukti Pembayaran:</b><br>
                                        Pembayaran dilakukan dengan metode COD (Cash On Delivery), sehingga bukti pembayaran tidak tersedia.
                                    </div>
                                <?php endif; ?>
                                </p>

                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            Tidak ada riwayat pembayaran yang tersedia.
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-12 text-center">
                    <?php
                    // Menentukan jumlah total halaman
                    $total_pages = ceil($total_data / $limit);

                    // Menampilkan tombol Previous jika bukan halaman pertama
                    if ($page > 1) {
                        echo "<a href='riwayat_pembayaran.php?page=" . ($page - 1) . "' class='btn btn-primary'>Previous</a> ";
                    }

                    // Menampilkan link halaman
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $active_class = ($i == $page) ? 'active' : '';
                        echo "<a href='riwayat_pembayaran.php?page=$i' class='btn btn-primary $active_class'>$i</a> ";
                    }

                    // Menampilkan tombol Next jika bukan halaman terakhir
                    if ($page < $total_pages) {
                        echo "<a href='riwayat_pembayaran.php?page=" . ($page + 1) . "' class='btn btn-primary'>Next</a>";
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$stmt->close();
$stmt_total->close();
$koneksi->close();
include 'include/footer.php';
?>