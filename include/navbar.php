<!-- Navbar -->
<nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
        <a href="index.php" class="navbar-brand">
            <img src="pegawai/assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Alat Fitnes dan Suplemen</span>
        </a>

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">Beranda</a>
                </li>
                <li class="nav-item">
                    <a href="produk.php" class="nav-link">Produk</a>
                </li>
                <li class="nav-item">
                    <a href="pembayaran.php" class="nav-link">Pembayaran</a>
                </li>
                <li class="nav-item">
                    <a href="riwayat_pembayaran.php" class="nav-link">Riwayat Pembayaran</a>
                </li>
                <li class="nav-item">
                    <a href="titik_lokasi.php" class="nav-link">Titik Lokasi</a>
                </li>
                <li class="nav-item">
                    <a href="profil.php" class="nav-link">Profil</a>
                </li>
                
            </ul>
        </div>

        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </li>
        </ul>
    </div>
</nav>
<!-- /.navbar -->
