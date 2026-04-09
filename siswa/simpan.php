<?php
include "../config/database.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') {
    exit("Akses ditolak");
}

$user_id = $_SESSION['id'];
$tanggal = date("Y-m-d");
$jam = date("H:i:s");

/* CEK SUDAH ABSEN */
$cek = mysqli_query($conn, "
    SELECT id FROM absensi 
    WHERE user_id='$user_id' AND tanggal='$tanggal'
");

if (!$cek) {
    exit("Query cek error");
}

if (mysqli_num_rows($cek) > 0) {
    exit("❌ Kamu sudah absen hari ini");
}

/* SIMPAN ABSEN */
$simpan = mysqli_query($conn, "
    INSERT INTO absensi (user_id, tanggal, jam, keterangan)
    VALUES ('$user_id','$tanggal','$jam','Hadir')
");

if (!$simpan) {
    exit("❌ Gagal menyimpan absensi");
}

echo "✅ Absensi berhasil";
