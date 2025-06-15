<?php
session_start();
include '../../config/config.php';

// Validasi session petugas
if (!isset($_SESSION['username']) || $_SESSION['level'] !== '2') {
    header("Location: ../../login/login.php");
    exit;
}

// Tambah data sales
if (isset($_POST['tambah'])) {
    $tgl         = $_POST['tgl_sales'];
    $id_customer = $_POST['id_customer'];
    $do_number   = $_POST['do_number'];
    $status      = $_POST['status'];

    $conn->query("INSERT INTO sales (tgl_sales, id_customer, do_number, status)
                  VALUES ('$tgl', $id_customer, '$do_number', '$status')");
    header("Location: sales.php");
    exit;
}

// Update data sales
if (isset($_POST['edit'])) {
    $id          = $_POST['id_sales'];
    $tgl         = $_POST['tgl_sales'];
    $id_customer = $_POST['id_customer'];
    $do_number   = $_POST['do_number'];
    $status      = $_POST['status'];

    $conn->query("UPDATE sales SET 
        tgl_sales='$tgl', 
        id_customer=$id_customer, 
        do_number='$do_number', 
        status='$status' 
        WHERE id_sales=$id");
    header("Location: sales.php");
    exit;
}

// Hapus data sales
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM sales WHERE id_sales=$id");
    header("Location: sales.php");
    exit;
}

// Ambil semua customer untuk pilihan
$customers = $conn->query("SELECT * FROM customer ORDER BY nama_customer ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Sales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Kelola Data Penjualan</h3>
    <a href="../dashboard.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

    <!-- Form Tambah -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Tambah Penjualan</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>Tanggal</label>
                        <input type="date" name="tgl_sales" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Customer</label>
                        <select name="id_customer" class="form-control" required>
                            <option value="">-- Pilih Customer --</option>
                            <?php while ($c = $customers->fetch_assoc()): ?>
                                <option value="<?= $c['id_customer'] ?>"><?= $c['nama_customer'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>DO Number</label>
                        <input type="text" name="do_number" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <input type="text" name="status" class="form-control" placeholder="Contoh: pending" required>
                    </div>
                </div>
                <button type="submit" name="tambah" class="btn btn-success mt-3">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Tabel Sales -->
    <div class="card">
        <div class="card-header bg-primary text-white">Daftar Penjualan</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>DO Number</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("
                    SELECT s.*, c.nama_customer 
                    FROM sales s 
                    JOIN customer c ON s.id_customer = c.id_customer 
                    ORDER BY s.id_sales DESC
                ");
                while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $row['tgl_sales'] ?></td>
                        <td><?= $row['nama_customer'] ?></td>
                        <td><?= $row['do_number'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_sales'] ?>">Edit</button>
                            <a href="?hapus=<?= $row['id_sales'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus penjualan ini?')">Hapus</a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $row['id_sales'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content" style="background-color: #ffffff !important;">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Penjualan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_sales" value="<?= $row['id_sales'] ?>">
                                        <div class="mb-2">
                                            <label>Tanggal</label>
                                            <input type="date" name="tgl_sales" class="form-control" value="<?= $row['tgl_sales'] ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Customer</label>
                                            <select name="id_customer" class="form-control" required>
                                                <?php
                                                $allCust = $conn->query("SELECT * FROM customer ORDER BY nama_customer ASC");
                                                while ($c = $allCust->fetch_assoc()):
                                                ?>
                                                    <option value="<?= $c['id_customer'] ?>" <?= $c['id_customer'] == $row['id_customer'] ? 'selected' : '' ?>>
                                                        <?= $c['nama_customer'] ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label>DO Number</label>
                                            <input type="text" name="do_number" class="form-control" value="<?= $row['do_number'] ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Status</label>
                                            <input type="text" name="status" class="form-control" value="<?= $row['status'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="edit" class="btn btn-warning">Simpan</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center text-muted py-3 mt-5">
    &copy; 2025 Copyright by Fikri Zain Darmawan
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
