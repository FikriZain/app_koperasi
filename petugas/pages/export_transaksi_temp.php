<?php
session_start();

require_once('../../vendor/autoload.php'); // path ke dompdf
include '../../config/config.php';
use Dompdf\Dompdf;

// Ambil session_id user saat ini
$session_id = session_id();

// Query transaksi sementara
$query = $conn->query("
    SELECT t.*, i.nama_item
    FROM transaction_temp t
    JOIN item i ON t.id_item = i.id_item
    WHERE t.session_id = '$session_id'
");

// Buat HTML untuk ditampilkan sebagai PDF
// Ambil tanggal
$tanggal = date("d-m-Y");

// Path logo (pastikan sesuai struktur folder kamu)
$logo = '../../assets/logo_koperasi.png'; // letakkan logo di folder 'assets'

// HTML Kop Laporan
$html = "
<style>
    .kop-table {
        width: 100%;
        margin-bottom: 10px;
    }
    .kop-table td {
        vertical-align: middle;
    }
    .logo-kop {
        width: 60px;
    }
    .judul-kop {
        text-align: center;
        font-size: 16pt;
        font-weight: bold;
    }
    .tanggal-kop {
        text-align: right;
        font-size: 10pt;
        color: #333;
    }
</style>

<table class='kop-table'>
    <tr>
        <td class='logo-kop'><img src='$logo' width='60'></td>
        <td class='judul-kop'>LAPORAN TRANSAKSI SEMENTARA</td>
        <td class='tanggal-kop'>Tanggal: $tanggal</td>
    </tr>
</table>
";

$html .= "
<table border='1' cellspacing='0' cellpadding='5' width='100%'>
<thead>
<tr>
    <th>No</th>
    <th>Item</th>
    <th>Qty</th>
    <th>Harga</th>
    <th>Total</th>
    <th>Catatan</th>
</tr>
</thead>
<tbody>
";



$no = 1;
$grand_total = 0;
while ($row = $query->fetch_assoc()) {
    $html .= "<tr>
        <td>{$no}</td>
        <td>{$row['nama_item']}</td>
        <td>{$row['quantity']}</td>
        <td>Rp " . number_format($row['price']) . "</td>
        <td>Rp " . number_format($row['amount']) . "</td>
        <td>" . htmlspecialchars($row['remark']) . "</td>
    </tr>";
    $grand_total += $row['amount'];
    $no++;
}

$html .= "<tr>
    <td colspan='4' align='right'><strong>Grand Total</strong></td>
    <td colspan='2'><strong>Rp " . number_format($grand_total) . "</strong></td>
</tr>";

$html .= "</tbody></table>";

// Inisialisasi Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
// agar tampil di browser
$dompdf->stream("keranjang_transaksi.pdf", ["Attachment" => false]);
exit;
