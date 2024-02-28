<?php
// Load TCPDF library
require_once('vendor/autoload.php');
require_once('config.php'); // Sesuaikan dengan jalur file koneksi.php

// Inisialisasi variabel tahun dan periode yang digunakan untuk filter
$selectedYear = isset($_POST['tahun_mengajukan']) ? $_POST['tahun_mengajukan'] : "";
$selectedPeriode = isset($_POST['periode']) ? $_POST['periode'] : "";

// Query database dengan filter tahun dan periode yang dipilih
$query = "SELECT s.kode_siswa, s.nisn, s.nama, s.tahun_mengajukan, s.periode, 
          MAX(CASE WHEN n.kd_kriteria = 'C1' THEN n.nilai END) AS nilai_C1,
          MAX(CASE WHEN n.kd_kriteria = 'C2' THEN n.nilai END) AS nilai_C2,
          MAX(CASE WHEN n.kd_kriteria = 'C3' THEN n.nilai END) AS nilai_C3,
          MAX(CASE WHEN n.kd_kriteria = 'C4' THEN n.nilai END) AS nilai_C4,
          MAX(CASE WHEN n.kd_kriteria = 'C5' THEN n.nilai END) AS nilai_C5
          FROM siswa s
          LEFT JOIN nilai n ON s.nisn = n.nisn";
if (!empty($selectedYear) && !empty($selectedPeriode)) {
    $query .= " WHERE s.tahun_mengajukan = '$selectedYear' AND s.periode = '$selectedPeriode'";
} elseif (!empty($selectedYear)) {
    $query .= " WHERE s.tahun_mengajukan = '$selectedYear'";
} elseif (!empty($selectedPeriode)) {
    $query .= " WHERE s.periode = '$selectedPeriode'";
}
$query .= " GROUP BY s.kode_siswa, s.nisn, s.nama, s.tahun_mengajukan, s.periode";

$result = $connection->query($query);

// Periksa apakah query berhasil dieksekusi
if (!$result) {
    echo "Query error: " . $connection->error;
    exit;
}

// Buat instance TCPDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set judul dokumen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Tabel Perangkingan');

// Tambahkan halaman baru
$pdf->AddPage();

// Tambahkan HTML ke dokumen
$html = '<h1>Tabel Perangkingan</h1>';

// Dapatkan data tabel dari database dan tambahkan ke HTML
$html .= '<table border="1">';
$html .= '<thead><tr><th>No</th><th>Kode Siswa</th><th>NISN</th><th>Nama</th><th>C1</th><th>C2</th><th>C3</th><th>C4</th><th>C5</th></tr></thead>';
$html .= '<tbody>';
// Query database dan tambahkan baris-baris data ke HTML
$no = 1;
while ($row = $result->fetch_assoc()) {
    $html .= "<tr>";
    $html .= "<td>" . $no++ . "</td>";
    $html .= "<td>" . $row['kode_siswa'] . "</td>";
    $html .= "<td>" . $row['nisn'] . "</td>";
    $html .= "<td>" . $row['nama'] . "</td>";
    $html .= "<td>" . $row['nilai_C1'] . "</td>";
    $html .= "<td>" . $row['nilai_C2'] . "</td>";
    $html .= "<td>" . $row['nilai_C3'] . "</td>";
    $html .= "<td>" . $row['nilai_C4'] . "</td>";
    $html .= "<td>" . $row['nilai_C5'] . "</td>";
    $html .= "</tr>";
}
$html .= '</tbody>';
$html .= '</table>';

// Tambahkan HTML ke halaman PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF ke browser atau simpan sebagai file
$pdf->Output('tabel_perangkingan.pdf', 'I');

