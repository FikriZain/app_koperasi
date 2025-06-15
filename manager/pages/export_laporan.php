<?php
require_once('../../vendor/autoload.php'); // path ke dompdf
include '../../config/config.php';

use Dompdf\Dompdf;

// Ambil data transaksi
$html = '<h3 style="text-align:center;">Laporan Transaksi</h3>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';

$query = $conn->query("SELECT t.*, i.nama_item FROM transaction t JOIN item i ON t.id_item = i.id_item ORDER BY t.id_transaction DESC");
$no = 1;
$grand_total = 0;
while ($row = $query->fetch_assoc()) {
    $html .= '<tr>
                <td>' . $no++ . '</td>
                <td>' . htmlspecialchars($row['nama_item']) . '</td>
                <td>' . $row['quantity'] . '</td>
                <td>Rp ' . number_format($row['price'], 0, ',', '.') . '</td>
                <td>Rp ' . number_format($row['amount'], 0, ',', '.') . '</td>
              </tr>';
    $grand_total += $row['amount'];
}

$html .= '<tr>
            <td colspan="4" align="right"><strong>Grand Total</strong></td>
            <td><strong>Rp ' . number_format($grand_total, 0, ',', '.') . '</strong></td>
          </tr>
        </tbody>
    </table>';

// Render PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_transaksi.pdf", array("Attachment" => false));
exit;
