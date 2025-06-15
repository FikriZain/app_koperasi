<?php
// Membuat koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "koperasi";

$conn = mysqli_connect ($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
