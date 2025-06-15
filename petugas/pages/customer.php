<?php
session_start();
include '../../config/config.php';

// Validasi akses hanya untuk petugas
if (!isset($_SESSION['username']) || $_SESSION['level'] !== '2') {
    header("Location: ../../login/login.php");
    exit;
}

// Tambah data customer
if (isset($_POST['tambah'])) {
    $nama   = $_POST['nama_customer'];
    $alamat = $_POST['alamat'];
    $telp   = $_POST['telp'];
    $fax    = $_POST['fax'];
    $email  = $_POST['email'];

    $conn->query("INSERT INTO customer (nama_customer, alamat, telp, fax, email)
                  VALUES ('$nama', '$alamat', '$telp', '$fax', '$email')");
    header("Location: customer.php");
    exit;
}

// Update data customer
if (isset($_POST['edit'])) {
    $id     = $_POST['id_customer'];
    $nama   = $_POST['nama_customer'];
    $alamat = $_POST['alamat'];
    $telp   = $_POST['telp'];
    $fax    = $_POST['fax'];
    $email  = $_POST['email'];

    $conn->query("UPDATE customer SET 
        nama_customer='$nama', 
        alamat='$alamat', 
        telp='$telp', 
        fax='$fax', 
        email='$email' 
        WHERE id_customer=$id");
    header("Location: customer.php");
    exit;
}

// Hapus data customer
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM customer WHERE id_customer=$id");
    header("Location: customer.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Kelola Data Customer</h3>
    <a href="../dashboard.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

    <!-- Form Tambah Customer -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Tambah Customer</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="nama_customer" placeholder="Nama Customer" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="alamat" placeholder="Alamat" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="telp" placeholder="No. Telepon" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="fax" placeholder="No. Fax" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input type="email" name="email" placeholder="Email" class="form-control" required>
                    </div>
                </div>
                <button type="submit" name="tambah" class="btn btn-success mt-3">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Tabel Customer -->
    <div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <span>Daftar Customer</span>
    <a href="export_customer.php" class="btn btn-light btn-sm" target="_blank">Unduh PDF</a>
</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Fax</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $customers = $conn->query("SELECT * FROM customer ORDER BY id_customer DESC");
                while ($c = $customers->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nama_customer']) ?></td>
                        <td><?= htmlspecialchars($c['alamat']) ?></td>
                        <td><?= htmlspecialchars($c['telp']) ?></td>
                        <td><?= htmlspecialchars($c['fax']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $c['id_customer'] ?>">Edit</button>
                            <a href="?hapus=<?= $c['id_customer'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus customer ini?')">Hapus</a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $c['id_customer'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #ffffff !important;">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_customer" value="<?= $c['id_customer'] ?>">
                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" name="nama_customer" class="form-control" value="<?= $c['nama_customer'] ?>" required>
                    </div>
                    <div class="mb-2">
                        <label>Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="<?= $c['alamat'] ?>" required>
                    </div>
                    <div class="mb-2">
                        <label>Telepon</label>
                        <input type="text" name="telp" class="form-control" value="<?= $c['telp'] ?>" required>
                    </div>
                    <div class="mb-2">
                        <label>Fax</label>
                        <input type="text" name="fax" class="form-control" value="<?= $c['fax'] ?>">
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $c['email'] ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="edit" class="btn btn-warning">Simpan Perubahan</button>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
