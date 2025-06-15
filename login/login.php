<?php
session_start(); // Memulai session PHP untuk menyimpan data login
include '../config/config.php'; // Menghubungkan ke database

$error = ''; // Untuk menyimpan pesan error jika login gagal

// Cek apakah form login dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil username dan password dari form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Cek di tabel petugas
    $queryPetugas = "
        SELECT p.username, p.nama_user, p.password, l.level AS level_name
        FROM petugas p
        JOIN level l ON p.level = l.id_level
        WHERE p.username = '$username'
    ";
    $resultPetugas = $conn->query($queryPetugas);
    $dataPetugas   = $resultPetugas->fetch_assoc();

    // Jika ditemukan di petugas dan password cocok
    if ($dataPetugas && password_verify($password, $dataPetugas['password'])) {
        $_SESSION['username'] = $dataPetugas['username'];       // Simpan username
        $_SESSION['nama']     = $dataPetugas['nama_user'];      // Simpan nama petugas
        $_SESSION['level']    = $dataPetugas['level_name'];     // Simpan level dari tabel level

        header("Location: ../petugas/dashboard.php"); // Redirect ke dashboard petugas
        exit;
    }

    // cek table manager
    $queryManager = "
        SELECT m.username, m.nama_user, m.password, l.level AS level_name
        FROM manager m
        JOIN level l ON m.level = l.id_level
        WHERE m.username = '$username'
    ";
    $resultManager = $conn->query($queryManager);
    $dataManager   = $resultManager->fetch_assoc();

    // Jika ditemukan di manager dan password cocok
    if ($dataManager && password_verify($password, $dataManager['password'])) {
        $_SESSION['username'] = $dataManager['username'];       // Simpan username
        $_SESSION['nama']     = $dataManager['nama_user'];      // Simpan nama manager
        $_SESSION['level']    = $dataManager['level_name'];     // Simpan level dari tabel level

        header("Location: ../manager/dashboard.php"); // Redirect ke dashboard manager
        exit;
    }

    // Jika tidak cocok di keduanya
    $error = "Login gagal: Username atau password salah!";
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login Koperasi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Container tampilan form -->
<div class="container mt-5 col-md-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4>Login Koperasi</h4>
        </div>
        <div class="card-body">
            <!-- Tampilkan error jika ada -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Form Login -->
            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required class="form-control" autocomplete="off" placeholder="Masukkan Username">
                </div>
                <div class="mb-3">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required class="form-control" autocomplete="off" placeholder="Masukkan Password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <hr>
            <p class="text-center">
                Belum punya akun? <a href="registrasi.php">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
<!-- Footer -->
<footer class="text-center text-muted py-3 mt-5">
    &copy; 2025 Copyright by Fikri Zain Darmawan
</footer>
</body>
</html>
