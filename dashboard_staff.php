<?php
session_start();

// Cek jika pengguna belum login
if (!isset($_SESSION['username']) || $_SESSION['peran'] !== 'Staff') {
    header('Location: login.php');
    exit;
}

include('laporan.php');
$type = isset($_GET['type']) ? $_GET['type'] : 'harian';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
$laporan = getLaporan($conn, $type, $startDate, $endDate);
?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">Staff Dashboard</a>
        <!-- Hapus bagian berikut untuk menghilangkan tombol hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation" id="sidebarToggle">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <!-- Hapus bagian ini -->
                <!-- <li class="nav-item">
                    <a class="nav-link" href="#" role="button">
                        <i class="fas fa-bars"></i> <!-- Ikon garis tiga -->
                    </a>
                </li> -->
            </ul>
        </div>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav" class="sb-sidenav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Menu</div>
                        <a class="nav-link" href="bahan_baku.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Bahan Baku
                        </a>
                        <a class="nav-link" href="transaksi_stok.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-exchange-alt"></i></div>
                            Transaksi Stok
                        </a>
                        <a class="nav-link" href="laporan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                            Laporan
                        </a>
                        <a class="nav-link text-danger" href="logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                            Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard Staff</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Selamat Datang, <?= $_SESSION['username']; ?>!</li>
                    </ol>
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">Bahan Baku</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="bahan_baku.php">Lihat Detail</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">Transaksi Stok</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="transaksi_stok.php">Lihat Detail</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">Laporan</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="laporan.php">Lihat Detail</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Memuat Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
