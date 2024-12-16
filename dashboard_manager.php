<?php
ob_start(); // Mulai output buffering untuk menghindari output sebelum PDF

require('fpdf.php');
include('koneksi.php'); // Pastikan koneksi ke database

// Fungsi untuk mengambil laporan berdasarkan rentang tanggal
function getLaporanByDateRange($conn, $startDate, $endDate) {
    $query = "
        SELECT 
            DATE(tanggal_transaksi) AS tanggal, 
            'Custom' AS jenis_laporan,
            SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END) AS total_keluar,
            SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END) AS total_masuk
        FROM transaksi_stok
        WHERE tanggal_transaksi BETWEEN ? AND ?
        GROUP BY DATE(tanggal_transaksi)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    return $stmt->get_result();
}

// Mengecek jika tombol download ditekan dan tanggal rentang tersedia
if (isset($_GET['download']) && isset($_GET['tanggal_awal']) && isset($_GET['tanggal_akhir'])) {
    $tanggal_awal = $_GET['tanggal_awal'];
    $tanggal_akhir = $_GET['tanggal_akhir'];

    // Validasi input
    if (!$tanggal_awal || !$tanggal_akhir) {
        echo "Tanggal awal dan tanggal akhir tidak boleh kosong.";
        exit;
    }

    if (strtotime($tanggal_awal) > strtotime($tanggal_akhir)) {
        echo "Tanggal awal tidak boleh lebih besar dari tanggal akhir.";
        exit;
    }

    // Ambil data laporan berdasarkan rentang tanggal
    $laporan = getLaporanByDateRange($conn, $tanggal_awal, $tanggal_akhir);

    if ($laporan->num_rows == 0) {
        echo "Tidak ada data untuk rentang tanggal yang dipilih.";
        exit;
    }

    // Membuat objek PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, "Laporan Stok Kemiri", 0, 1, 'C');
    $pdf->Ln(10);

    // Header tabel
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 10, 'Tanggal', 1);
    $pdf->Cell(50, 10, 'Jenis Laporan', 1);
    $pdf->Cell(40, 10, 'Total Masuk', 1);
    $pdf->Cell(40, 10, 'Total Keluar', 1);
    $pdf->Ln();

    // Menampilkan data laporan dalam tabel PDF
    $pdf->SetFont('Arial', '', 12);
    while ($row = $laporan->fetch_assoc()) {
        $pdf->Cell(30, 10, $row['tanggal'], 1);
        $pdf->Cell(50, 10, $row['jenis_laporan'], 1);
        $pdf->Cell(40, 10, $row['total_masuk'], 1);
        $pdf->Cell(40, 10, $row['total_keluar'], 1);
        $pdf->Ln();
    }

    // Output PDF ke browser (untuk diunduh)
    $pdf->Output('D', 'laporan_stok_kemiri.pdf');
    ob_end_flush();  // Mengakhiri output buffering
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Dashboard Manager" />
    <meta name="author" content="Sistem Manajemen Stok" />
    <title>Dashboard Manager</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        body.bg-primary {
            background: url('gambar/OIP.jpg') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index_manager.php">Dashboard Manager</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    </nav>

    <!-- Sidebar -->
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Menu</div>
                        <a class="nav-link" href="logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                            Logout
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Masuk sebagai:</div>
                    Manager
                </div>
            </nav>
        </div>

        <!-- Content -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard Manager</h1>

                    <!-- Form untuk memilih rentang tanggal -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Pilih Rentang Tanggal Laporan
                        </div>
                        <div class="card-body">
                            <form method="GET" action="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="tanggal_awal">Tanggal Awal:</label>
                                        <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_akhir">Tanggal Akhir:</label>
                                        <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Tampilkan Laporan</button>
                                <button type="submit" name="download" class="btn btn-danger mt-3">Download PDF</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
