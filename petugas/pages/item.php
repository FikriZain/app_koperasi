<?php
session_start();
include '../../config/config.php';

// Validasi session hanya untuk petugas
if (!isset($_SESSION['username']) || $_SESSION['level'] !== '2') {
    header("Location: ../../login/login.php");
    exit;
}

// Tambah item baru
if (isset($_POST['tambah'])) {
    $nama_item   = $_POST['nama_item'];
    $uom         = $_POST['uom'];
    $harga_beli  = $_POST['harga_beli'];
    $harga_jual  = $_POST['harga_jual'];

    $conn->query("INSERT INTO item (nama_item, uom, harga_beli, harga_jual)
                  VALUES ('$nama_item', '$uom', '$harga_beli', '$harga_jual')");
    header("Location: item.php");
    exit;
}

// Update item
if (isset($_POST['edit'])) {
    $id_item     = $_POST['id_item'];
    $nama_item   = $_POST['nama_item'];
    $uom         = $_POST['uom'];
    $harga_beli  = $_POST['harga_beli'];
    $harga_jual  = $_POST['harga_jual'];

    $conn->query("UPDATE item SET 
        nama_item='$nama_item', 
        uom='$uom', 
        harga_beli='$harga_beli', 
        harga_jual='$harga_jual'
        WHERE id_item=$id_item");
    header("Location: item.php");
    exit;
}

// Hapus item
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM item WHERE id_item=$id");
    header("Location: item.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3>Kelola Data Barang</h3>
    <a href="../dashboard.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

    <!-- Form Tambah -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Tambah Item</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="nama_item" class="form-control" placeholder="Nama Item" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="uom" class="form-control" placeholder="UOM (satuan)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="harga_beli" class="form-control" placeholder="Harga Beli" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="harga_jual" class="form-control" placeholder="Harga Jual" required>
                    </div>
                </div>
                <button type="submit" name="tambah" class="btn btn-success mt-3">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Tabel Item -->
    <div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <span>Daftar Item</span>
    <a href="export_item.php" class="btn btn-light btn-sm" target="_blank">Unduh PDF</a>
</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>UOM</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $items = $conn->query("SELECT * FROM item ORDER BY id_item DESC");
                    while ($i = $items->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($i['nama_item']) ?></td>
                        <td><?= htmlspecialchars($i['uom']) ?></td>
                        <td><?= number_format($i['harga_beli']) ?></td>
                        <td><?= number_format($i['harga_jual']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $i['id_item'] ?>">Edit</button>
                            <a href="?hapus=<?= $i['id_item'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus item ini?')">Hapus</a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $i['id_item'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content" style="background-color: #ffffff !important;">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Item</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_item" value="<?= $i['id_item'] ?>">
                                        <div class="mb-2">
                                            <label>Nama Item</label>
                                            <input type="text" name="nama_item" class="form-control" value="<?= $i['nama_item'] ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>UOM</label>
                                            <input type="text" name="uom" class="form-control" value="<?= $i['uom'] ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Harga Beli</label>
                                            <input type="number" name="harga_beli" class="form-control" value="<?= $i['harga_beli'] ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Harga Jual</label>
                                            <input type="number" name="harga_jual" class="form-control" value="<?= $i['harga_jual'] ?>" required>
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

<!-- Bootstrap JS (untuk modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
