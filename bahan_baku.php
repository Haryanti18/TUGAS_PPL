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

// Inisialisasi variabel $result untuk menghindari error jika query gagal
$result = false;

// Proses input data jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $jumlah_stok = $_POST['jumlah_stok'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $tanggal_keluar = !empty($_POST['tanggal_keluar']) ? $_POST['tanggal_keluar'] : NULL;

    $sql = "INSERT INTO bahan_baku (nama, jumlah_stok, tanggal_masuk, tanggal_keluar) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("siss", $nama, $jumlah_stok, $tanggal_masuk, $tanggal_keluar);

        if ($stmt->execute()) {
            echo "<script>alert('Data bahan baku berhasil ditambahkan!');</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat menyimpan data: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Gagal menyiapkan statement: " . $conn->error . "');</script>";
    }
}

// Ambil data dari tabel bahan_baku untuk tabel dan grafik
$sql = "SELECT * FROM bahan_baku";
$result = $conn->query($sql);

// Ambil data nama, jumlah stok, dan tanggal untuk grafik
$sqlGrafik = "SELECT nama, jumlah_stok, tanggal_masuk FROM bahan_baku";
$resultGrafik = $conn->query($sqlGrafik);

// Data untuk grafik
$dataGrafik = [];
if ($resultGrafik && $resultGrafik->num_rows > 0) {
    while ($row = $resultGrafik->fetch_assoc()) {
        $dataGrafik[] = $row;
    }
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Bahan Baku</title>
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
                        <a class="nav-link active" href="transaksi_stok.php">
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
                    <h1 class="mt-4">Data Bahan Baku</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Bahan Baku</li>
                    </ol>

                    <!-- Form Input -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus me-1"></i>
                            Tambah Data Bahan Baku
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Bahan Baku</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                                <div class="mb-3">
                                    <label for="jumlah_stok" class="form-label">Jumlah Stok</label>
                                    <input type="number" class="form-control" id="jumlah_stok" name="jumlah_stok" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                                    <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_keluar" class="form-label">Tanggal Keluar (Opsional)</label>
                                    <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar">
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Data Bahan Baku
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Jumlah Stok</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Tanggal Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?= $row['id_bahan_baku'] ?></td>
                                                <td><?= $row['nama'] ?></td>
                                                <td><?= $row['jumlah_stok'] ?></td>
                                                <td><?= $row['tanggal_masuk'] ?></td>
                                                <td><?= $row['tanggal_keluar'] ?? 'Belum keluar' ?></td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Grafik Stok -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Grafik Jumlah Stok
                        </div>
                        <div class="card-body">
                            <!-- Tentukan ukuran grafik -->
                            <canvas id="stokChart" style="max-width: 400px; height: 300px; margin: 0 auto;"></canvas>
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

    <!-- Tambahkan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data grafik dari PHP
        const dataGrafik = <?= json_encode($dataGrafik); ?>;

        // Ambil nama, jumlah stok, dan tanggal
        const labels = dataGrafik.map(data => data.nama + ' (' + data.tanggal_masuk + ')');
        const stokData = dataGrafik.map(data => data.jumlah_stok);

        // Grafik Chart.js
        const ctx = document.getElementById('stokChart').getContext('2d');
        const stokChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Stok',
                    data: stokData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>
