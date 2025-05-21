<?php
ob_start();
session_start();
require_once '../koneksi.php';
$title = "Data Pesanan | Fitnes Suplement";

// Cek apakah sesi login ada
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit();
}

require_once 'include/header.php';
require_once 'include/navbar.php';
require_once 'include/sidebar.php';

try {
    // Mengambil data dari tabel pesanan dan pembayaran dengan LEFT JOIN
    $stmt = $koneksi->prepare("
        SELECT p.222247_id_pesanan, u.222247_nama_lengkap, p.222247_tanggal_pesanan, p.222247_status_pesanan, 
               p.222247_total_harga, pb.222247_metode
        FROM tbl_pesanan_222247 p
        JOIN tbl_pengguna_222247 u ON p.222247_id_pengguna = u.222247_id_pengguna
        LEFT JOIN tbl_pembayaran_222247 pb ON p.222247_id_pesanan = pb.222247_id_pesanan
    ");
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Pesanan</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Pesanan</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Display Notification Message -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Pesan</h5>
                    <?php echo $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['delete'])): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Pesan</h5>
                    <?php echo $_SESSION['delete']; ?>
                    <?php unset($_SESSION['delete']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Pesan</h5>
                    <?php echo $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <!-- End Notification Message -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Pesanan</h3>
                        </div>
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pengguna</th>
                                        <th>Tanggal Pesanan</th>
                                        <th>Status Pesanan</th>
                                        <th>Total Harga</th>
                                        <th>Metode Pembayaran</th> <!-- Kolom metode pembayaran baru -->
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($data_pesanan = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($data_pesanan['222247_nama_lengkap']); ?></td>
                                            <td><?php echo htmlspecialchars($data_pesanan['222247_tanggal_pesanan']); ?></td>
                                            <td><?php echo htmlspecialchars($data_pesanan['222247_status_pesanan']); ?></td>
                                            <td><?php echo htmlspecialchars($data_pesanan['222247_total_harga']); ?></td>
                                            <td>
                                                <?php
                                                // Menampilkan 'COD' jika nilai metode pembayaran adalah 'cod', selain itu tampilkan 'Transfer'
                                                if ($data_pesanan['222247_metode'] === 'cod') {
                                                    echo 'COD';
                                                } else {
                                                    echo 'Transfer';
                                                }
                                                ?>
                                            </td> <!-- Menampilkan metode pembayaran -->
                                            <td>
                                                <a href="detail_pesanan.php?222247_id_pesanan=<?php echo $data_pesanan['222247_id_pesanan']; ?>" class="btn btn-info btn-sm">Detail</a>
                                                <a href="proses_pesanan/p_delete_pesanan.php?222247_id_pesanan=<?php echo $data_pesanan['222247_id_pesanan']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus data ini?')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pengguna</th>
                                        <th>Tanggal Pesanan</th>
                                        <th>Status Pesanan</th>
                                        <th>Total Harga</th>
                                        <th>Metode Pembayaran</th> <!-- Kolom metode pembayaran baru -->
                                        <th>Aksi</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php require_once 'include/footer.php'; ?>