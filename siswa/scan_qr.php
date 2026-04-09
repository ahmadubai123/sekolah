<?php
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
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
body{
    min-height:100vh;
    background:linear-gradient(135deg,#667eea,#764ba2);
    display:flex;
    justify-content:center;
    align-items:center;
}
.card{
    background:#fff;
    width:100%;
    max-width:420px;
    padding:25px;
    border-radius:20px;
    box-shadow:0 25px 45px rgba(0,0,0,.25);
    text-align:center;
}
#preview{
    width:100%;
    border-radius:16px;
    overflow:hidden;
    margin:20px 0;
}
a{
    display:inline-block;
    text-decoration:none;
    background:#f1f1f1;
    padding:12px 18px;
    border-radius:12px;
    color:#333;
}
</style>
</head>

<body>

<div class="card">
    <h2>Scan QR Absensi</h2>
    <p>Arahkan kamera ke QR Code</p>
    <div id="preview"></div>
    <a href="dashboard.php">⬅ Kembali</a>
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

        fetch("simpan.php", {
            method: "POST",
            headers: {"Content-Type":"application/x-www-form-urlencoded"},
            body: "kode=" + encodeURIComponent(text)
        })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            location.href = "dashboard.php";
        });
    }
);
</script>

</body>
</html>
