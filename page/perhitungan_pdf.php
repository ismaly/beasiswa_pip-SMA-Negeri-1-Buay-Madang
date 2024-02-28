<?php
require 'vendor/autoload.php'; // Include Dompdf Autoloader
use Dompdf\Dompdf;

// Inisialisasi objek Dompdf
$dompdf = new Dompdf();

$html= "<h1>hello world</h1>"; 
// Mulai membuat dokumen PDF
$dompdf->loadHtml($html);

// Mengatur ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'portrait');

// Render PDF (konversi HTML ke PDF)
$dompdf->render();

// Mengirimkan dokumen PDF ke browser pengguna
$dompdf->stream('laporan_perhitungan.pdf', array('Attachments' => false));
