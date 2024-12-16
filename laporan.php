<?php
// Mulai sesi
session_start();

// Cek apakah user sudah login dan berperan sebagai Staff
if (!isset($_SESSION['username']) || $_SESSION['peran'] !== 'Staff') {
    header("Location: login.php");
    exit();
}

// Sambungkan ke database
$conn = new mysqli("localhost", "root", "", "tugas_ppl");
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Fungsi untuk mengambil data laporan
function getLaporan($conn, $type) {
    $query = "";

    switch ($type) {
        case 'harian':
            $query = "
                SELECT 
                    DATE(tanggal_transaksi) AS tanggal, 
                    'Harian' AS jenis_laporan,
                    SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END) AS total_keluar,
                    SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END) AS total_masuk
                FROM transaksi_stok
                WHERE DATE(tanggal_transaksi) = CURDATE()
                GROUP BY DATE(tanggal_transaksi)";
            break;

        case 'mingguan':
            $query = "
                SELECT 
                    YEARWEEK(tanggal_transaksi, 1) AS minggu, 
                    'Mingguan' AS jenis_laporan,
                    SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END) AS total_keluar,
                    SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END) AS total_masuk
                FROM transaksi_stok
                WHERE YEARWEEK(tanggal_transaksi, 1) = YEARWEEK(CURDATE(), 1)
                GROUP BY YEARWEEK(tanggal_transaksi, 1)";
            break;

        case 'bulanan':
            $query = "
                SELECT 
                    MONTH(tanggal_transaksi) AS bulan, 
                    'Bulanan' AS jenis_laporan,
                    SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END) AS total_keluar,
                    SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END) AS total_masuk
                FROM transaksi_stok
                WHERE MONTH(tanggal_transaksi) = MONTH(CURDATE()) 
                  AND YEAR(tanggal_transaksi) = YEAR(CURDATE())
                GROUP BY MONTH(tanggal_transaksi), YEAR(tanggal_transaksi)";
            break;
    }

    return $conn->query($query);
}

// Ambil data berdasarkan jenis laporan
$type = isset($_GET['type']) ? $_GET['type'] : 'harian';
$laporan = getLaporan($conn, $type);

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Kemiri</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">Staff Dashboard</a>
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
                        <a class="nav-link active" href="laporan.php">
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
                    <h1 class="mt-4">Laporan Stok Kemiri</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan</li>
                    </ol>

                    <!-- Button Group to switch between reports -->
                    <div class="d-flex justify-content-center btn-group mb-4">
                        <a href="?type=harian" class="btn btn-primary">Harian</a>
                        <a href="?type=mingguan" class="btn btn-success">Mingguan</a>
                        <a href="?type=bulanan" class="btn btn-warning">Bulanan</a>
                        <a href="laporan.php?download=true&tanggal_awal=2024-01-01&tanggal_akhir=2024-12-31">Download Laporan PDF</a>

                    </div>

                    <!-- Tabel Laporan -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Data Laporan Stok Kemiri
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jenis Laporan</th>
                                        <th>Total Masuk</th>
                                        <th>Total Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($laporan && $laporan->num_rows > 0): ?>
                                        <?php while ($row = $laporan->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <?= isset($row['tanggal']) ? $row['tanggal'] : (isset($row['minggu']) ? "Minggu " . $row['minggu'] : "Bulan " . $row['bulan']) ?>
                                                </td>
                                                <td><?= $row['jenis_laporan'] ?></td>
                                                <td><?= $row['total_masuk'] ?></td>
                                                <td><?= $row['total_keluar'] ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
