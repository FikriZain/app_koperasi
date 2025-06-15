<?php
include '../config/config.php'; // Panggil koneksi database

$error = '';   // Untuk menyimpan pesan error
$success = ''; // Untuk menyimpan pesan sukses

// Cek jika form registrasi dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form input
    $nama     = $_POST['nama']; // Nama lengkap pengguna
    $username = $_POST['username']; // Username login
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Enkripsi password
    $level    = $_POST['level']; // ID level: 1 = manager, 2 = petugas

    // Cek apakah username sudah digunakan (di tabel petugas ATAU manager)
    $checkPetugas = $conn->query("SELECT * FROM petugas WHERE username = '$username'");
    $checkManager = $conn->query("SELECT * FROM manager WHERE username = '$username'");

    if ($checkPetugas->num_rows > 0 || $checkManager->num_rows > 0) {
        // Jika username sudah ada, tampilkan error
        $error = "Username sudah digunakan!";
    } else {
        // Jika level = 1 (manager), masukkan ke tabel manager
        if ($level == '1') {
            $conn->query("INSERT INTO manager (nama_user, username, password, level)
                          VALUES ('$nama', '$username', '$password', $level)");
        } else {
            // Jika level = 2 (petugas), masukkan ke tabel petugas
            $conn->query("INSERT INTO petugas (nama_user, username, password, level)
                          VALUES ('$nama', '$username', '$password', $level)");
        }

        // Tampilkan pesan sukses
        $success = "Registrasi berhasil, silakan login.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Akun</title>
    <!-- Load Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Container utama -->
<div class="container mt-5 col-md-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white text-center">
            <h4>Registrasi Akun</h4>
        </div>
        <div class="card-body">

            <!-- Tampilkan pesan error jika ada -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php elseif (!empty($success)): ?>
                <!-- Tampilkan pesan sukses jika registrasi berhasil -->
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <!-- Form registrasi -->
            <form method="POST">
                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required placeholder="Masukkan Nama Lengkap">
                </div>
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Masukkan Username">
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Masukkan Password">
                </div>
                <div class="mb-3">
                    <label>Level</label>
                    <!-- Pilihan level: manager atau petugas -->
                    <select name="level" class="form-control" required>
                        <option value="">-- Pilih Level --</option>
                        <option value="1">Manager</option>
                        <option value="2">Petugas</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-100">Daftar</button>
            </form>

            <hr>
            <p class="text-center">Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</div>
<!-- Footer -->
<footer class="text-center text-muted py-3 mt-5">
    &copy; 2025 Copyright by Fikri Zain Darmawan
</footer>
</body>
</html>
