<?php
session_start();
require_once('../../vendor/autoload.php'); // path ke dompdf
include '../../config/config.php';
use Dompdf\Dompdf;

// Ambil data penjualan + customer
$query = $conn->query("
    SELECT s.*, c.nama_customer 
    FROM sales s 
    JOIN customer c ON s.id_customer = c.id_customer 
    ORDER BY s.id_sales DESC
");

$tanggal = date("d-m-Y");

$html = "
<style>
    .kop-table { width: 100%; margin-bottom: 10px; }
    .kop-table td { vertical-align: middle; }
    .judul { text-align: center; font-size: 16pt; font-weight: bold; }
    .tanggal { text-align: right; font-size: 10pt; color: #555; }
</style>

<table class='kop-table'>
    <tr>
        <td><img src='../../assets/logo_koperasi.png' width='60'></td>
        <td class='judul'>LAPORAN PENJUALAN</td>
        <td class='tanggal'>Tanggal: $tanggal</td>
    </tr>
</table>

<table border='1' cellpadding='5' cellspacing='0' width='100%'>
<thead>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Customer</th>
        <th>DO Number</th>
        <th>Status</th>
    </tr>
</thead>
<tbody>";

$no = 1;
while ($row = $query->fetch_assoc()) {
    $html .= "<tr>
        <td>{$no}</td>
        <td>{$row['tgl_sales']}</td>
        <td>" . htmlspecialchars($row['nama_customer']) . "</td>
        <td>{$row['do_number']}</td>
        <td>{$row['status']}</td>
    </tr>";
    $no++;
}

$html .= "</tbody></table>";

// Cetak PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_penjualan.pdf", ["Attachment" => false]);
exit;
