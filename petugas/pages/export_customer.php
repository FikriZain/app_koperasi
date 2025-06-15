<?php
session_start();
require_once('../../vendor/autoload.php'); // path ke dompdf
include '../../config/config.php';
use Dompdf\Dompdf;

// Tanggal sekarang
$tanggal = date("d-m-Y");

// Ambil data customer
$query = $conn->query("SELECT * FROM customer ORDER BY nama_customer ASC");

// Awal HTML
$html = "
<style>
    .kop-table { width: 100%; margin-bottom: 10px; }
    .kop-table td { vertical-align: middle; }
    .judul { text-align: center; font-size: 16pt; font-weight: bold; }
    .tanggal { text-align: right; font-size: 10pt; color: #555; }
    table { font-size: 10pt; }
</style>

<table class='kop-table'>
    <tr>
        <td><img src='../../assets/logo_koperasi.png' width='60'></td>
        <td class='judul'>LAPORAN DATA CUSTOMER</td>
        <td class='tanggal'>Tanggal: $tanggal</td>
    </tr>
</table>

<table border='1' cellpadding='5' cellspacing='0' width='100%'>
<thead>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Alamat</th>
        <th>Telepon</th>
        <th>Fax</th>
        <th>Email</th>
    </tr>
</thead>
<tbody>
";

// Isi tabel dari database
$no = 1;
while ($c = $query->fetch_assoc()) {
    $html .= "<tr>
        <td>$no</td>
        <td>" . htmlspecialchars($c['nama_customer']) . "</td>
        <td>" . htmlspecialchars($c['alamat']) . "</td>
        <td>" . htmlspecialchars($c['telp']) . "</td>
        <td>" . htmlspecialchars($c['fax']) . "</td>
        <td>" . htmlspecialchars($c['email']) . "</td>
    </tr>";
    $no++;
}

$html .= "</tbody></table>";

// Inisialisasi Dompdf dan cetak
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // landscape untuk data lebar
$dompdf->render();
$dompdf->stream("laporan_customer.pdf", ["Attachment" => false]); // tampilkan di browser
exit;
