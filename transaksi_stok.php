<?php
session_start();
require 'koneksi.php'; // File koneksi database

// Cek jika pengguna belum login
if (!isset($_SESSION['username']) || $_SESSION['peran'] !== 'Staff') {
    header('Location: login.php');
    exit;
}

// Proses simpan data jika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_bahan_baku = $_POST['id_bahan_baku'];
    $jumlah = $_POST['jumlah'];
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $tanggal_transaksi = date('Y-m-d'); // Tanggal saat ini

    // Query untuk menyimpan data ke database
    $query = "INSERT INTO transaksi_stok (id_bahan_baku, jumlah, jenis_transaksi, tanggal_transaksi) 
              VALUES ('$id_bahan_baku', '$jumlah', '$jenis_transaksi', '$tanggal_transaksi')";

    if ($conn->query($query) === TRUE) {
        echo "<script>alert('Transaksi berhasil ditambahkan!');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan transaksi: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Stok</title>
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
                    <h1 class="mt-4">Transaksi Stok</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Transaksi Stok</li>
                    </ol>

                    <!-- Form Input Transaksi -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus me-1"></i>
                            Tambah Transaksi Stok
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="id_bahan_baku" class="form-label">ID Bahan Baku</label>
                                    <input type="text" class="form-control" id="id_bahan_baku" name="id_bahan_baku" required>
                                </div>
                                <div class="mb-3">
                                    <label for="jumlah" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
                                    <select class="form-control" id="jenis_transaksi" name="jenis_transaksi" required>
                                        <option value="Masuk">Masuk</option>
                                        <option value="Keluar">Keluar</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Data Transaksi -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Data Transaksi Stok
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>ID Bahan Baku</th>
                                        <th>Jumlah</th>
                                        <th>Jenis Transaksi</th>
                                        <th>Tanggal Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM transaksi_stok";
                                    $result = $conn->query($query);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['id_transaksi'] . "</td>";
                                            echo "<td>" . $row['id_bahan_baku'] . "</td>";
                                            echo "<td>" . $row['jumlah'] . "</td>";
                                            echo "<td>" . $row['jenis_transaksi'] . "</td>";
                                            echo "<td>" . $row['tanggal_transaksi'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>Tidak ada data</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Grafik Transaksi Stok -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Grafik Transaksi Stok
                        </div>
                        <div class="card-body">
                            <canvas id="transaksiChart" style="max-width: 600px; height: 400px; margin: 0 auto;"></canvas>
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

    <!-- Memuat Bootstrap JS dan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data untuk Grafik
        <?php
        // Query untuk mengambil data transaksi masuk dan keluar per tanggal
        $masukQuery = "SELECT tanggal_transaksi, SUM(jumlah) AS total_masuk FROM transaksi_stok WHERE jenis_transaksi = 'Masuk' GROUP BY tanggal_transaksi ORDER BY tanggal_transaksi";
        $keluarQuery = "SELECT tanggal_transaksi, SUM(jumlah) AS total_keluar FROM transaksi_stok WHERE jenis_transaksi = 'Keluar' GROUP BY tanggal_transaksi ORDER BY tanggal_transaksi";

        $masukResult = $conn->query($masukQuery);
        $keluarResult = $conn->query($keluarQuery);

        // Array untuk menyimpan data transaksi masuk dan keluar per tanggal
        $masukData = [];
        $keluarData = [];
        $tanggalData = [];

        while ($row = $masukResult->fetch_assoc()) {
            $tanggalData[] = $row['tanggal_transaksi'];
            $masukData[] = $row['total_masuk'];
        }

        while ($row = $keluarResult->fetch_assoc()) {
            $keluarData[] = $row['total_keluar'];
        }

        // Mengkonversi data PHP ke format JavaScript
        $masukDataJS = json_encode($masukData);
        $keluarDataJS = json_encode($keluarData);
        $tanggalDataJS = json_encode($tanggalData);
        ?>

        const data = {
            labels: <?= $tanggalDataJS ?>, // Tanggal transaksi
            datasets: [{
                label: 'Jumlah Masuk',
                data: <?= $masukDataJS ?>,
                backgroundColor: '#4caf50', // Warna untuk transaksi masuk
            }, {
                label: 'Jumlah Keluar',
                data: <?= $keluarDataJS ?>,
                backgroundColor: '#f44336', // Warna untuk transaksi keluar
            }]
        };

        // Konfigurasi Grafik
        const config = {
            type: 'bar', // Jenis grafik
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Grafik Transaksi Stok per Tanggal'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal Transaksi'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Jumlah Transaksi'
                        }
                    }
                }
            },
        };

        // Render Grafik
        const transaksiChart = new Chart(
            document.getElementById('transaksiChart'),
            config
        );
    </script>
</body>
</html>
