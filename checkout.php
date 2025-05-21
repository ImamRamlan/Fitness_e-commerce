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

// Koneksi ke database
include 'koneksi.php';

// Ambil produk yang dipilih untuk checkout
$pesanan_ids = [];
if (isset($_POST['pesanan_ids']) && !empty($_POST['pesanan_ids'])) {
    $pesanan_ids = $_POST['pesanan_ids'];

    if (is_array($pesanan_ids) && count($pesanan_ids) > 0) {
        $placeholders = implode(',', array_fill(0, count($pesanan_ids), '?'));

        $query_checkout = "
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
            WHERE p.222247_id_pesanan IN ($placeholders)
        ";

        $stmt = $koneksi->prepare($query_checkout);
        $stmt->bind_param(str_repeat('i', count($pesanan_ids)), ...$pesanan_ids);
        $stmt->execute();
        $result_checkout = $stmt->get_result();
    } else {
        $result_checkout = null;
    }
} else {
    $result_checkout = null;
}

// Ambil data pengguna
$query_pengguna = "
    SELECT 
        222247_nama_lengkap, 
        222247_nomor_telepon, 
        222247_alamat 
    FROM tbl_pengguna_222247 
    WHERE 222247_username = ?
";
$stmt_pengguna = $koneksi->prepare($query_pengguna);
$stmt_pengguna->bind_param('s', $username);
$stmt_pengguna->execute();
$result_pengguna = $stmt_pengguna->get_result();
$pengguna_data = $result_pengguna->fetch_assoc();

$sql = "SELECT id_lokasi, titik_lokasi FROM pilih_lokasi";
$result = $koneksi->query($sql);

$title = "Checkout Pembayaran";
include 'include/header.php';
include 'include/navbar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Proses Pembayaran</h1>
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
            <?php if ($result_checkout && $result_checkout->num_rows > 0): ?>
                <div class="row">
                    <!-- Tampilkan Daftar Pesanan yang Dipilih -->
                    <?php while ($row = $result_checkout->fetch_assoc()): ?>
                        <div class="col-lg-8 col-md-6 col-sm-12">
                            <div class="card mb-4 shadow-sm">
                                <img src="pegawai/produk/<?php echo htmlspecialchars($row['222247_foto']); ?>" class="card-img-top" alt="Foto Produk">
                                <div class="card-body">
                                    <h5 class="card-title"><b><?php echo htmlspecialchars($row['222247_nama_produk']); ?></b></h5>
                                    <p class="card-text">
                                        Tanggal Pesanan: <?php echo date("d-m-Y H:i:s", strtotime($row['222247_tanggal_pesanan'])); ?><br>
                                        Status Pesanan:
                                        <span class="badge <?php echo $row['222247_status_pesanan'] === 'Selesai' ? 'bg-success' : 'bg-warning'; ?>">
                                            <?php echo htmlspecialchars($row['222247_status_pesanan']); ?>
                                        </span><br>
                                        Jumlah: <?php echo htmlspecialchars($row['222247_jumlah']); ?><br>
                                        Total Harga: Rp <?php echo number_format($row['222247_total_harga'], 2, ',', '.'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>

                </div>
                <!-- Tombol untuk Konfirmasi Pembayaran -->
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="card">
                            <form action="proses_pembayaran.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="pesanan_ids" value="<?php echo htmlspecialchars(implode(',', $pesanan_ids)); ?>">
                                <div class="form-group">
                                    <label for="payment_method">Metode Pembayaran</label>
                                    <select class="form-control" name="payment_method" id="payment_method" onchange="togglePaymentDetails(this.value)" required>
                                        <option value="">Pilih Metode Pembayaran</option>
                                        <option value="sea_bank_instant">SeaBank Bayar Instan</option>
                                        <option value="sea_bank">SeaBank</option>
                                        <option value="bca">Bank BCA</option>
                                        <option value="bri">Bank BRI</option>
                                        <option value="cod">COD</option>
                                        <option value="mandiri">Bank Mandiri</option>
                                        <option value="bni">Bank BNI</option>
                                        <option value="danamon">Bank Danamon</option>
                                        <option value="bsi">Bank Syariah Indonesia (BSI)</option>
                                        <option value="permata">Bank Permata</option>
                                        <option value="credit_card">Kartu Kredit/Debit</option>
                                    </select>

                                    <div id="payment-details"></div>
                                    <div id="rekening-info"></div>
                                </div>

                                <div id="payment-details"></div>
                                <div class="form-group">
                                    <label for="nama_penerima">Nama Penerima</label>
                                    <input type="text" class="form-control" name="nama_penerima" id="nama_penerima" required placeholder="Masukkan Nama Penerima">
                                </div>
                                <div class="form-group">
                                    <label for="no_wa">Nomor WhatsApp</label>
                                    <input type="text" class="form-control" name="no_wa" id="no_wa" required placeholder="Masukkan Nomor Whatsapp">
                                </div>
                                <div class="form-group">
                                    <label for="alamat">Alamat Pengiriman</label>
                                    <textarea class="form-control" name="alamat" id="alamat" rows="4" required>Masukkan Dengan Jelas Alamat Anda.</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="titik_lokasi">Pilih Titik Lokasi:</label>
                                    <select class="form-control" name="titik_lokasi" id="titik_lokasi" required>
                                        <option>Pilih</option>
                                        <?php
                                        // Menampilkan opsi untuk setiap data titik_lokasi
                                        if ($result->num_rows > 0) {
                                            // Mengambil setiap baris data
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='" . $row['titik_lokasi'] . "'>" . $row['titik_lokasi'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>Tidak ada titik lokasi</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="kode_pos">Kode Pos</label>
                                    <input type="text" class="form-control" name="kode_pos" id="kode_pos" required placeholder="Masukkan Kode Pos Anda">
                                </div>

                                <button type="submit" class="btn btn-success">Buat Pesanan</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        Tidak ada pesanan yang dipilih atau pesanan tidak ditemukan.
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
$stmt->close();
$stmt_pengguna->close();
$koneksi->close();
include 'include/footer.php';
?>

<script>
    // Fungsi untuk menampilkan detail pembayaran sesuai metode yang dipilih
    function togglePaymentDetails(method) {
        let paymentDetails = document.getElementById("payment-details");
        let rekeningInfo = document.getElementById("rekening-info");

        paymentDetails.innerHTML = ''; // Clear previous content
        rekeningInfo.innerHTML = ''; // Clear previous content

        // Kosongkan kolom yang tidak diperlukan
        let transferReceiptInput = document.getElementById("transfer_receipt");
        let deliveryAddressInput = document.getElementById("delivery_address");

        if (method === 'sea_bank_instant' || method === 'sea_bank' || method === 'bca' || method === 'bri' || method === 'mandiri' || method === 'bni' || method === 'danamon' || method === 'bsi' || method === 'permata') {
            paymentDetails.innerHTML = `
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="transfer_receipt">Foto Bukti Transfer</label>
                            <input type="file" class="form-control" name="transfer_receipt" id="transfer_receipt" required>
                        </div>
                    </div>
                </div>
            `;

            // Menampilkan nomor rekening yang sesuai dengan pilihan bank
            if (method === 'sea_bank_instant') {
                rekeningInfo.innerHTML = `<div class="alert alert-info"><strong>Nomor Rekening SeaBank Bayar Instan:</strong><br> 123-456-789</div>`;
            } else if (method === 'sea_bank') {
                rekeningInfo.innerHTML = `<div class="alert alert-info"><strong>Nomor Rekening SeaBank:</strong><br> 987-654-321</div>`;
            } else if (method === 'bca') {
                rekeningInfo.innerHTML = `<div class="alert alert-info"><strong>Nomor Rekening Bank BCA:</strong><br> 321-654-987</div>`;
            } else if (method === 'bri') {
                rekeningInfo.innerHTML = `<div class="alert alert-info"><strong>Nomor Rekening Bank BRI:</strong><br> 654-321-789</div>`;
            } else if (method === 'mandiri') {
                rekeningInfo.innerHTML = `<div class="alert alert-info"><strong>Nomor Rekening Bank Mandiri:</strong><br> 987-321-654</div>`;
            } else if (method === 'bni') {
                rekeningInfo.innerHTML = `<div class="alert alert-info"><strong>Nomor Rekening Bank BNI:</strong><br> 741-852-963</div>`;
            } else if (method === 'danamon') {
                rekeningInfo.innerHTML = `<div class="alert alert-info"><strong>Nomor Rekening Bank Danamon:</strong><br> 963-852-741</div>`;
            } else if (method === 'bsi') {
                rekeningInfo.innerHTML = `<div class="alert alert-info"><strong>Nomor Rekening Bank BSI:</strong><br> 852-741-963</div>`;
            } else if (method === 'permata') {
                rekeningInfo.innerHTML = `<div class="alert alert-info"><strong>Nomor Rekening Bank Permata:</strong><br> 852-963-741</div>`;
            }
        } else if (method === 'credit_card') {
            paymentDetails.innerHTML = `
                <div class="form-group">
                    <label for="credit_card_number">Nomor Kartu Kredit</label>
                    <input type="text" class="form-control" name="credit_card_number" id="credit_card_number" required>
                </div>
                <div class="form-group">
                    <label for="credit_card_expiry">Tanggal Kadaluarsa</label>
                    <input type="month" class="form-control" name="credit_card_expiry" id="credit_card_expiry" required>
                </div>
            `;
        }
    }
</script>