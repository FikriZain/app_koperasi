<?php
session_start(); // Mulai session untuk mengecek login

// Jika sudah login dan ada level-nya
if (isset($_SESSION['level'])) {
    if ($_SESSION['level'] === 'manager') {
        header("Location: manager/dashboard.php"); // Redirect ke dashboard manager
        exit;
    } elseif ($_SESSION['level'] === 'petugas') {
        header("Location: petugas/dashboard.php"); // Redirect ke dashboard petugas
        exit;
    }
}

// Jika belum login, arahkan ke halaman login
header("Location: login/login.php");
exit;
