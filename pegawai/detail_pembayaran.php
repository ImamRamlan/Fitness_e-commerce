<?php
session_start();
require_once '../koneksi.php';

// Cek apakah ada ID pembayaran di parameter URL
if (!isset($_GET['222247_id_pembayaran']) || empty($_GET['222247_id_pembayaran'])) {
    $_SESSION['error'] = "ID pembayaran tidak valid!";
    header('Location: data_pembayaran.php');
    exit();
}

$id_pembayaran = mysqli_real_escape_string($koneksi, $_GET['222247_id_pembayaran']);

try {
    // Mengambil data pembayaran dengan relasi ke tabel lain
    $stmt = $koneksi->prepare("
        SELECT 
            p.222247_id_pembayaran, 
            p.222247_status_pembayaran, 
            p.222247_tanggal_pembayaran, 
            p.222247_metode,
            p.222247_bukti_pembayaran,
            o.222247_id_pesanan, 
            o.222247_total_harga, 
            u.222247_nama_lengkap, 
            i.222247_alamat, 
            i.222247_no_wa,
            i.222247_titik_lokasi -- Tambahkan kolom ini
        FROM tbl_pembayaran_222247 p
        JOIN tbl_pesanan_222247 o ON p.222247_id_pesanan = o.222247_id_pesanan
        JOIN tbl_pengguna_222247 u ON o.222247_id_pengguna = u.222247_id_pengguna
        JOIN tbl_info_222247 i ON p.222247_id_pembayaran = i.222247_id_pembayaran
        WHERE p.222247_id_pembayaran = ?
    ");
    $stmt->bind_param("i", $id_pembayaran);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah pembayaran ditemukan
    if ($result->num_rows == 0) {
        $_SESSION['error'] = "Pembayaran tidak ditemukan!";
        header('Location: data_pembayaran.php');
        exit();
    }

    $data_pembayaran = $result->fetch_assoc();
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: data_pembayaran.php');
    exit();
}

require_once 'include/header.php';
require_once 'include/navbar.php';
require_once 'include/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Pembayaran</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Detail Pembayaran</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Display Notification Message -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Pesan</h5>
                    <?php echo $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Pembayaran</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>ID Pembayaran</th>
                                    <td><?php echo htmlspecialchars($data_pembayaran['222247_id_pembayaran']); ?></td>
                                </tr>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <td><?php echo htmlspecialchars($data_pembayaran['222247_id_pesanan']); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Harga</th>
                                    <td><?php echo htmlspecialchars(number_format($data_pembayaran['222247_total_harga'], 2, ',', '.')); ?> IDR</td>
                                </tr>
                                <tr>
                                    <th>Status Pembayaran</th>
                                    <td>
                                        <span id="statusPembayaran"
                                            class="badge badge-<?php echo strtolower($data_pembayaran['222247_status_pembayaran']) === 'lunas' ? 'success' : (strtolower($data_pembayaran['222247_status_pembayaran']) === 'gagal' ? 'danger' : 'warning'); ?> "
                                            style="cursor: pointer;">
                                            <?php echo htmlspecialchars($data_pembayaran['222247_status_pembayaran']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pembayaran</th>
                                    <td><?php echo htmlspecialchars($data_pembayaran['222247_tanggal_pembayaran']); ?></td>
                                </tr>
                                <tr>
                                    <th>Metode Pembayaran</th>
                                    <td>
                                        <?php
                                        // Menampilkan 'COD' jika nilai metode pembayaran adalah 'cod', selain itu tampilkan 'Transfer'
                                        if ($data_pembayaran['222247_metode'] === 'cod') {
                                            echo 'COD';
                                        } else {
                                            echo 'Transfer';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nama Lengkap Pengguna</th>
                                    <td><?php echo htmlspecialchars($data_pembayaran['222247_nama_lengkap']); ?></td>
                                </tr>
                                <tr>
                                    <th>Nomor WhatsApp</th>
                                    <td><?php echo htmlspecialchars($data_pembayaran['222247_no_wa']); ?></td>
                                </tr>
                                <tr>
                                    <th>Alamat Pembayaran</th>
                                    <td><?php echo htmlspecialchars($data_pembayaran['222247_alamat']); ?></td>
                                </tr>
                                <tr>
                                    <th>Bukti Pembayaran</th>
                                    <td>
                                        <?php if ($data_pembayaran['222247_metode'] !== 'cod' && !empty($data_pembayaran['222247_bukti_pembayaran'])): ?>
                                            <a href="bukti_pembayaran/<?php echo htmlspecialchars($data_pembayaran['222247_bukti_pembayaran']); ?>" target="_blank">
                                                Lihat Bukti Pembayaran (Transfer ke Bank)
                                            </a>
                                        <?php elseif ($data_pembayaran['222247_metode'] === 'cod'): ?>
                                            Tidak Ada Bukti Pembayaran (Metode COD)
                                        <?php else: ?>
                                            Tidak Ada Bukti
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Titik Lokasi</th>
                                    <td>
                                        <?php if ($data_pembayaran['222247_metode'] === 'cod'): ?>
                                            <?php if (!empty($data_pembayaran['222247_titik_lokasi'])): ?>
                                                <a href="https://www.google.com/maps?q=<?php echo htmlspecialchars($data_pembayaran['222247_titik_lokasi']); ?>" target="_blank">
                                                    Lihat Lokasi
                                                </a>
                                            <?php else: ?>
                                                Lokasi Tidak Tersedia
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Tidak Ada Lokasi (Metode Pembayaran Transfer)
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="card-footer">
                            <a href="data_pembayaran.php" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var statusPembayaran = document.getElementById('statusPembayaran');
        var statuses = ['Pembayaran Diproses', 'Lunas', 'Gagal'];

        statusPembayaran.addEventListener('click', function() {
            var currentStatus = this.innerText.trim();
            var nextStatusIndex = (statuses.indexOf(currentStatus) + 1) % statuses.length;
            var nextStatus = statuses[nextStatusIndex];

            if (confirm("Ubah status pembayaran menjadi: " + nextStatus + "?")) {
                window.location.href = 'proses_pembayaran/ubah_status_pembayaran.php?id_pembayaran=<?php echo $id_pembayaran; ?>&status_pembayaran=' + encodeURIComponent(nextStatus);
            }
        });
    });
</script>