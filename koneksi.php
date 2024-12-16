<?php
// Konfigurasi koneksi database
$host = "localhost"; // Nama host
$username = "root"; // Nama pengguna MySQL
$password = ""; // Kata sandi MySQL (kosong di XAMPP)
$dbname = "tugas_ppl"; // Nama database Anda

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
echo "Koneksi berhasil!";

/**
 * Fungsi untuk mendapatkan laporan berdasarkan tipe.
 *
 * @param mysqli $conn Koneksi database.
 * @param string $type Jenis laporan (harian, mingguan, bulanan, custom).
 * @param string|null $startDate Tanggal mulai (untuk laporan custom).
 * @param string|null $endDate Tanggal akhir (untuk laporan custom).
 * @return mysqli_result|bool Hasil query atau false jika gagal.
 */
function getLaporan($conn, $type, $startDate = null, $endDate = null) {
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

        case 'custom':
            $query = "
                SELECT 
                    DATE(tanggal_transaksi) AS tanggal, 
                    'Custom' AS jenis_laporan,
                    SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END) AS total_keluar,
                    SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END) AS total_masuk
                FROM transaksi_stok
                WHERE tanggal_transaksi BETWEEN ? AND ?
                GROUP BY DATE(tanggal_transaksi)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ss', $startDate, $endDate);
            $stmt->execute();
            return $stmt->get_result();
    }

    return $conn->query($query);
}
?>
