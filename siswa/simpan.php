<?php
include "../config/database.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') {
    exit("Akses ditolak");
}

$user_id = $_SESSION['id'];
$tanggal = date("Y-m-d");
$jam = date("H:i:s");
$kode = $_POST['kode'] ?? '';

if (empty($kode)) {
    exit("Kode tidak valid");
}

$query = mysqli_query($conn, "
    SELECT s.id, s.qr_token, s.qr_expired_at, sub.name as subject
    FROM schedules s
    JOIN subjects sub ON s.subject_id = sub.id
    WHERE s.qr_token = '$kode'
");

if (mysqli_num_rows($query) > 0) {
    $schedule = mysqli_fetch_assoc($query);
    
    if ($schedule['qr_expired_at'] && strtotime($schedule['qr_expired_at']) < time()) {
        exit("❌ QR Code sudah expired");
    }
    
    $cek = mysqli_query($conn, "
        SELECT id FROM absensi 
        WHERE user_id = $user_id AND tanggal = '$tanggal' AND schedule_id = " . $schedule['id']
    );
    
    if (mysqli_num_rows($cek) > 0) {
        exit("❌ Kamu sudah absen untuk mata pelajaran ini");
    }
    
    $simpan = mysqli_query($conn, "
        INSERT INTO absensi (user_id, schedule_id, tanggal, jam, keterangan, qr_scanned) 
        VALUES ($user_id, " . $schedule['id'] . ", '$tanggal', '$jam', 'Hadir', 1)
    ");
    
    if ($simpan) {
        exit("✅ Absensi berhasil: " . $schedule['subject']);
    } else {
        exit("❌ Gagal menyimpan absensi");
    }
} else {
    exit("❌ Kode QR tidak valid");
}
?>