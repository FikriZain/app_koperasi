<?php
session_start();
include '../config/config.php'; // Koneksi DB

// Cek login manager
if (!isset($_SESSION['username']) || $_SESSION['level'] !== '1') {
    header("Location: ../login/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #0000FF;
            padding-top: 20px;
            color: white;
            position: fixed;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: skyblue;
        }
        .content {
            margin-left: 250px;
            padding: 30px;
            flex-grow: 1;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h5 class="text-center">Menu Manager</h5>
    <hr class="bg-light">
    <a href="dashboard.php">Dashboard</a>
    <a href="pages/kelola_petugas.php">Kelola Petugas</a>
    <a href="dashboard.php#laporan">Laporan Transaksi</a>
    <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <h3>Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</h3>
    <p>Anda login sebagai <strong>Manager</strong>.</p>

    <!-- Tabel Transaksi -->
    <div id="laporan" class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span>Laporan Transaksi</span>
            <a href="pages/export_laporan.php" class="btn btn-light btn-sm" target="_blank">Unduh PDF</a>
        </div>
        
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = $conn->query("
                        SELECT t.*, i.nama_item 
                        FROM transaction t 
                        JOIN item i ON t.id_item = i.id_item 
                        ORDER BY t.id_transaction DESC
                    ");
                    $grand_total = 0;
                    while ($row = $query->fetch_assoc()):
                        $grand_total += $row['amount'];
                    ?>  
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_item']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($row['amount'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr class="fw-bold table-secondary">
                        <td colspan="4" class="text-end">Total Transaksi</td>
                        <td>Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
<!-- Footer -->
<footer class="text-center text-muted py-3 mt-5">
    &copy; 2025 Copyright by Fikri Zain Darmawan
</footer>
</div>
</body>
</html>
