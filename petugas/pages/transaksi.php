<?php
session_start();
include '../../config/config.php';

// Validasi login petugas
if (!isset($_SESSION['username']) || $_SESSION['level'] !== '2') {
    header("Location: ../../login/login.php");
    exit;
}

// Ambil session ID unik untuk keranjang user
$session_id = session_id();

// Tambah item ke keranjang sementara (transaction_temp)
if (isset($_POST['tambah'])) {
    $id_item  = $_POST['id_item'];
    $quantity = $_POST['quantity'];
    $remark   = $_POST['remark'];

    // Ambil harga item
    $item = $conn->query("SELECT harga_jual FROM item WHERE id_item = $id_item")->fetch_assoc();
    $price  = $item['harga_jual'];
    $amount = $quantity * $price;

    $conn->query("INSERT INTO transaction_temp (id_item, quantity, price, amount, session_id, remark)
                  VALUES ($id_item, $quantity, $price, $amount, '$session_id', '$remark')");
    header("Location: transaksi.php");
    exit;
}

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM transaction_temp WHERE id_transaction = $id AND session_id = '$session_id'");
    header("Location: transaksi.php");
    exit;
}

// Simpan transaksi permanen
if (isset($_POST['simpan_transaksi'])) {
    $temp = $conn->query("SELECT * FROM transaction_temp WHERE session_id = '$session_id'");
    while ($row = $temp->fetch_assoc()) {
        $conn->query("INSERT INTO transaction (id_item, quantity, price, amount)
                      VALUES ({$row['id_item']}, {$row['quantity']}, {$row['price']}, {$row['amount']})");
    }
    // Kosongkan keranjang
    $conn->query("DELETE FROM transaction_temp WHERE session_id = '$session_id'");
    header("Location: transaksi.php");
    exit;
}

// Data item untuk dropdown
$items = $conn->query("SELECT * FROM item ORDER BY nama_item ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaksi Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3>Transaksi Penjualan</h3>
    <a href="../dashboard.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

    <!-- Form Tambah Item -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Tambah Item ke Keranjang</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Item</label>
                        <select name="id_item" class="form-control" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($i = $items->fetch_assoc()): ?>
                                <option value="<?= $i['id_item'] ?>">
                                    <?= $i['nama_item'] ?> (Rp <?= number_format($i['harga_jual']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Qty</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label>Catatan</label>
                        <input type="text" name="remark" class="form-control" placeholder="Misal: urgent / COD">
                    </div>
                    <div class="col-md-2 d-grid align-items-end">
                        <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Keranjang -->
    <div class="card">
        <div class="card-header bg-primary text-white">Keranjang Transaksi</div>
        <div class="card-body">
            <form method="POST">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Total</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $data = $conn->query("
                        SELECT t.*, i.nama_item
                        FROM transaction_temp t
                        JOIN item i ON t.id_item = i.id_item
                        WHERE t.session_id = '$session_id'
                        ORDER BY t.id_transaction DESC
                    ");
                    $grand_total = 0;
                    while ($row = $data->fetch_assoc()):
                        $grand_total += $row['amount'];
                    ?>
                        <tr>
                            <td><?= $row['nama_item'] ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td>Rp <?= number_format($row['price']) ?></td>
                            <td>Rp <?= number_format($row['amount']) ?></td>
                            <td><?= htmlspecialchars($row['remark']) ?></td>
                            <td>
                                <a href="?hapus=<?= $row['id_transaction'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Hapus item ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                        <tr class="table-secondary fw-bold">
                            <td colspan="3" class="text-end">Grand Total</td>
                            <td colspan="3">Rp <?= number_format($grand_total) ?></td>
                        </tr>
                    </tbody>
                </table>

                <?php if ($grand_total > 0): ?>
                    <div class="d-flex gap-2">
                        <button type="submit" name="simpan_transaksi" class="btn btn-primary">Simpan Transaksi</button>
                        <a href="export_transaksi_temp.php" class="btn btn-danger" target="_blank">Unduh Transaksi (PDF)</a>
                    </div>
                <?php else: ?>
                    <div class="text-muted">Keranjang masih kosong</div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center text-muted py-3 mt-5">
    &copy; 2025 Copyright by Fikri Zain Darmawan
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
