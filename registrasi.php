<?php
ob_start();
session_start();
include 'koneksi.php';

// Cek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $kata_sandi = $_POST['kata_sandi'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_telepon = $_POST['no_telepon'];
    $alamat = $_POST['alamat']; // Menambahkan variabel alamat

    // Validasi jika username, email, no telepon sudah ada
    $stmt = $koneksi->prepare("SELECT * FROM tbl_pengguna_222247 WHERE 222247_username = ? OR 222247_email = ? OR 222247_nomor_telepon = ?");
    $stmt->bind_param("sss", $username, $email, $no_telepon);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika ada yang sama, tampilkan pesan error
        $error = "Username, Email, atau Nomor Telepon sudah terdaftar!";
    } else {
        // Jika tidak ada yang sama, lakukan pendaftaran
        $insert_stmt = $koneksi->prepare("INSERT INTO tbl_pengguna_222247 (222247_username, 222247_kata_sandi, 222247_nama_lengkap, 222247_email, 222247_nomor_telepon, 222247_alamat) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("ssssss", $username, $kata_sandi, $nama_lengkap, $email, $no_telepon, $alamat); // Menambahkan alamat

        if ($insert_stmt->execute()) {
            // Pendaftaran berhasil
            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='login.php';</script>";
            exit();
        } else {
            // Pendaftaran gagal
            $error = "Pendaftaran gagal! Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Pengguna | Dompet Digital</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="pegawai/assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="pegawai/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="pegawai/assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>Fitnes</b> Suplement</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Daftar untuk mendapatkan akun baru</p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <strong>Pesan Error:</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="no_telepon" placeholder="Nomor Telepon" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <textarea class="form-control" name="alamat" placeholder="Alamat" rows="3" required></textarea>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="kata_sandi" placeholder="Kata Sandi" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <a href="login.php" class="text-center">Sudah punya akun? Masuk</a>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="pegawai/assets/plugins/jquery/jquery.min.js"></script>
    <script src="pegawai/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="pegawai/assets/dist/js/adminlte.min.js"></script>
</body>

</html>
