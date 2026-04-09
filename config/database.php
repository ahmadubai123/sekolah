<?php
$conn = mysqli_connect("localhost", "root", "", "absensi_siswa");

if (!$conn) {
    die("Koneksi database gagal");
}

session_start();
?>
