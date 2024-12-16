<?php
if (isset($_GET['download'])) {
    include('laporan.php');

    $tanggal_awal = $_GET['tanggal_awal'];
    $tanggal_akhir = $_GET['tanggal_akhir'];

    $laporan = getLaporan($conn, 'custom', $tanggal_awal, $tanggal_akhir);

    if ($laporan->num_rows > 0) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, "Laporan Stok Kemiri", 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(30, 10, 'Tanggal', 1);
        $pdf->Cell(50, 10, 'Jenis Laporan', 1);
        $pdf->Cell(40, 10, 'Total Masuk', 1);
        $pdf->Cell(40, 10, 'Total Keluar', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        while ($row = $laporan->fetch_assoc()) {
            $pdf->Cell(30, 10, $row['tanggal'], 1);
            $pdf->Cell(50, 10, $row['jenis_laporan'], 1);
            $pdf->Cell(40, 10, $row['total_masuk'], 1);
            $pdf->Cell(40, 10, $row['total_keluar'], 1);
            $pdf->Ln();
        }

        $pdf->Output('D', 'laporan_stok_kemiri.pdf');
        exit();
    } else {
        echo "Tidak ada data pada rentang tanggal yang dipilih.";
    }
}
