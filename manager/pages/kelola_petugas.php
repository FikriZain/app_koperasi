<?php
session_start();
include '../../config/config.php';

// Validasi hanya manager (level = 1)
if (!isset($_SESSION['username']) || $_SESSION['level'] !== '1') {
    header("Location: ../../login/login.php");
    exit;
}

// Tambah petugas
if (isset($_POST['tambah'])) {
    $nama     = $_POST['nama_user'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $level    = 2; // Petugas

    // Cek apakah username sudah digunakan
    $cek = $conn->query("SELECT * FROM user WHERE username = '$username'");
    if ($cek->num_rows > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $conn->query("INSERT INTO user (nama_user, username, password, level) 
                      VALUES ('$nama', '$username', '$password', $level)");
        header("Location: petugas.php");
        exit;
    }
}

// Edit petugas
if (isset($_POST['edit'])) {
    $id       = $_POST['id_user'];
    $nama     = $_POST['nama_user'];
    $username = $_POST['username'];

    $conn->query("UPDATE user SET nama_user='$nama', username='$username' WHERE id_user=$id");
    header("Location: petugas.php");
    exit;
}

// Hapus petugas
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM user WHERE id_user = $id");
    header("Location: petugas.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Petugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3>Kelola Petugas</h3>
    <a href="../dashboard.php" class="btn btn-primary mb-3">← Kembali ke Dashboard</a>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Form Tambah -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Tambah Petugas</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Nama</label>
                        <input type="text" name="nama_user" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" name="tambah" class="btn btn-primary mt-3">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Daftar Petugas -->
    <div class="card">
        <div class="card-header bg-primary text-white">Daftar Petugas</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $petugas = $conn->query("SELECT * FROM petugas WHERE level = 2 ORDER BY id_user DESC");
                while ($p = $petugas->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nama_user']) ?></td>
                        <td><?= htmlspecialchars($p['username']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $p['id_user'] ?>">Edit</button>
                            <a href="?hapus=<?= $p['id_user'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus petugas ini?')">Hapus</a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $p['id_user'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Petugas</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id_user" value="<?= $p['id_user'] ?>">
                                    <div class="mb-2">
                                        <label>Nama</label>
                                        <input type="text" name="nama_user" class="form-control" value="<?= $p['nama_user'] ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control" value="<?= $p['username'] ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="edit" class="btn btn-warning">Simpan</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </form>
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
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
