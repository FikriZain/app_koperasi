<?php
session_start();
require_once('../../vendor/autoload.php'); // path ke dompdf
include '../../config/config.php';
use Dompdf\Dompdf;

// Query semua item
$query = $conn->query("SELECT * FROM item ORDER BY nama_item ASC");

// Mulai HTML
$html = '
<h3 style="text-align:center;">LAPORAN DATA BARANG</h3>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
<thead>
<tr>
    <th>No</th>
    <th>Nama Item</th>
    <th>UOM</th>
    <th>Harga Beli</th>
    <th>Harga Jual</th>
</tr>
</thead>
<tbody>
';

$no = 1;
while ($row = $query->fetch_assoc()) {
    $html .= "<tr>
        <td>{$no}</td>
        <td>" . htmlspecialchars($row['nama_item']) . "</td>
        <td>{$row['uom']}</td>
        <td>Rp " . number_format($row['harga_beli']) . "</td>
        <td>Rp " . number_format($row['harga_jual']) . "</td>
    </tr>";
    $no++;
}

$html .= "</tbody></table>";

// Buat PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_barang.pdf", ["Attachment" => false]); // tampil di browser
exit;
