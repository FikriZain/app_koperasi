<?php
session_start();
include '../config/config.php'; // Koneksi ke database

// Validasi akses login hanya untuk petugas (level = 2)
if (!isset($_SESSION['username']) || $_SESSION['level'] != '2') {
    header("Location: ../login/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Petugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: blue;
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
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h5 class="text-center">Menu Petugas</h5>
    <hr class="bg-light">
    <a href="dashboard.php">Dashboard</a>
    <a href="pages/customer.php">Kelola Customer</a>
    <a href="pages/sales.php">Kelola Sales</a>
    <a href="pages/item.php">Kelola Barang</a>
    <a href="pages/transaksi.php">Transaksi</a>
    <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <h3>Selamat datang, <?= $_SESSION['nama'] ?>!</h3>
    <p>Anda login sebagai <strong>Petugas</strong>.</p>

    <hr>

    <!-- Tabel Data Penjualan -->
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Penjualan Terbaru</h4>
    <a href="pages/export_penjualan_dashboard.php" class="btn btn-outline-danger btn-sm" target="_blank">Unduh PDF</a>
</div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>DO Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // Ambil data sales dan customer
            $query = mysqli_query($conn, "
                SELECT s.*, c.nama_customer 
                FROM sales s 
                JOIN customer c ON s.id_customer = c.id_customer 
                ORDER BY s.id_sales DESC
            ");

            while ($row = mysqli_fetch_assoc($query)) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['tgl_sales']}</td>
                        <td>{$row['nama_customer']}</td>
                        <td>{$row['do_number']}</td>
                        <td>{$row['status']}</td>
                      </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>

<!-- Footer -->
<footer class="text-center text-muted py-3 mt-5">
    &copy; 2025 Copyright by Fikri Zain Darmawan
</footer>
</div>

</body>
</html>
