<?php
// Mulai session hanya jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi database
$sname    = "localhost";
$uname    = "root";
$pwd      = "1234";
$database = "panahan";

$conn = new mysqli($sname, $uname, $pwd, $database);
// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
