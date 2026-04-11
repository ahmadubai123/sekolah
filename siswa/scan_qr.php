<?php
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

$token = $_GET['token'] ?? '';

if ($token) {
    $query = mysqli_query($conn, "
        SELECT s.id, s.qr_token, s.qr_expired_at, sub.name as subject, c.name as class
        FROM schedules s
        JOIN subjects sub ON s.subject_id = sub.id
        JOIN classes c ON s.class_id = c.id
        WHERE s.qr_token = '$token'
    ");
    
    if (mysqli_num_rows($query) > 0) {
        $schedule = mysqli_fetch_assoc($query);
        
        if (strtotime($schedule['qr_expired_at']) < time()) {
            echo "<script>alert('QR Code sudah expired');location.href='dashboard.php';</script>";
            exit;
        }
        
        $user_id = $_SESSION['id'];
        $tanggal = date("Y-m-d");
        $jam = date("H:i:s");
        
        $cek = mysqli_query($conn, "SELECT id FROM absensi WHERE user_id = $user_id AND tanggal = '$tanggal' AND schedule_id = " . $schedule['id']);
        
        if (mysqli_num_rows($cek) > 0) {
            echo "<script>alert('Kamu sudah absen untuk mata pelajaran ini');location.href='dashboard.php';</script>";
            exit;
        }
        
        $simpan = mysqli_query($conn, "INSERT INTO absensi (user_id, schedule_id, tanggal, jam, keterangan, qr_scanned) VALUES ($user_id, " . $schedule['id'] . ", '$tanggal', '$jam', 'Hadir', 1)");
        
        if ($simpan) {
            echo "<script>alert('Absensi berhasil: " . htmlspecialchars($schedule['subject']) . "');location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan absensi');location.href='dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('QR Code tidak valid');location.href='dashboard.php';</script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Scan QR Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;justify-content:center;align-items:center;}
    .card{background:#fff;width:100%;max-width:420px;padding:25px;border-radius:20px;box-shadow:0 25px 45px rgba(0,0,0,.25);text-align:center;}
    #preview{width:100%;border-radius:16px;overflow:hidden;margin:20px 0;min-height:250px;}
    a{display:inline-block;text-decoration:none;background:#f1f1f1;padding:12px 18px;border-radius:12px;color:#333;}
    .manual{background:#f4fdf7;padding:20px;border-radius:16px;margin-top:20px;}
    .manual input{padding:12px;width:100%;margin:10px 0;border-radius:10px;border:1px solid #ddd;}
    .manual button{width:100%;padding:12px;border-radius:10px;border:none;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;cursor:pointer;}
    </style>
</head>
<body>

<div class="card">
    <h2>Scan QR Absensi</h2>
    <p>Arahkan kamera ke QR Code</p>
    <div id="preview"></div>
    <a href="dashboard.php">⬅ Kembali</a>
    
    <div class="manual">
        <p>Atau masukkan kode manual:</p>
        <input type="text" id="manualToken" placeholder="Masukkan kode absensi">
        <button onclick="submitManual()">Absen Sekarang</button>
    </div>
</div>

<script>
let scanned = false;
const scanner = new Html5Qrcode("preview");

scanner.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    (text) => {
        if (scanned) return;
        scanned = true;
        scanner.stop();
        processQR(text);
    },
    (error) => {}
);

function submitManual() {
    const token = document.getElementById('manualToken').value;
    if (token) processQR(token);
}

function processQR(text) {
    fetch("simpan.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "kode=" + encodeURIComponent(text)
    })
    .then(res => res.text())
    .then(msg => {
        alert(msg);
        location.href = "dashboard.php";
    })
    .catch(err => {
        alert("Gagal memproses QR");
        location.href = "scan_qr.php";
    });
}
</script>

</body>
</html>