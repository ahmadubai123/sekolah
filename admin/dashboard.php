<?php
include "../config/database.php";
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
$qr_url = "http://localhost/Aplikasi%20Absensi%20SISWA/siswa/scan.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin | MadrasahKu</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>

<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
body{
    min-height:100vh;
    background:linear-gradient(120deg,#2ecc71,#27ae60,#1abc9c);
    background-size:300% 300%;
    animation:bgMove 10s ease infinite;
    display:flex;
    justify-content:center;
    align-items:center;
    position:relative;
    overflow:hidden;
}
@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* ===== BACKGROUND 3D (FIX FINAL) ===== */
.bg-left,.bg-right{
    position:absolute;
    bottom:40px;
    width:260px;
    max-width:30vw;
    z-index:1;
    opacity:0.95;
    pointer-events:none;
    animation:float 6s ease-in-out infinite;
    filter:drop-shadow(0 18px 35px rgba(0,0,0,.25));
}
.bg-left{left:3%;}
.bg-right{right:3%;animation-delay:1.5s;}

@keyframes float{
    0%{transform:translateY(0)}
    50%{transform:translateY(-20px)}
    100%{transform:translateY(0)}
}

/* ===== DASHBOARD ===== */
.dashboard{
    width:90%;
    max-width:950px;
    background:#fff;
    border-radius:26px;
    padding:35px;
    box-shadow:0 30px 70px rgba(0,0,0,.25);
    z-index:2;
    animation:fadeUp .8s ease;
}
@keyframes fadeUp{
    from{opacity:0;transform:translateY(40px)}
    to{opacity:1;transform:translateY(0)}
}
.header{
    background:linear-gradient(135deg,#2ecc71,#27ae60);
    color:#fff;
    padding:30px;
    border-radius:20px;
    text-align:center;
    margin-bottom:30px;
}
.menu{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:22px;
}
.card{
    background:#f4fdf7;
    padding:26px;
    border-radius:20px;
    text-align:center;
    text-decoration:none;
    color:#2c3e50;
    transition:.35s;
    cursor:pointer;
}
.card span{font-size:42px;}
.card h3{margin:15px 0 6px;}
.card p{font-size:14px;color:#666;}
.card:hover{
    transform:translateY(-12px);
    background:linear-gradient(135deg,#2ecc71,#27ae60);
    color:#fff;
}
.card:hover p{color:#eafff3;}
.card.logout:hover{
    background:linear-gradient(135deg,#ff6b6b,#ff4757);
}

/* ===== MODAL ===== */
.modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.5);
    display:none;
    justify-content:center;
    align-items:center;
    z-index:99;
}
.modal-box{
    background:#fff;
    padding:30px;
    border-radius:20px;
    text-align:center;
}
.close{
    margin-top:15px;
    color:#27ae60;
    cursor:pointer;
}

/* ===== RESPONSIVE ===== */
@media(max-width:1200px){
    .dashboard{max-width:90%;padding:25px;}
    .menu{grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px}
    .card{padding:20px;}
    .card span{font-size:36px;}
}

@media(max-width:900px){
    .bg-left,.bg-right{display:none;}
    .dashboard{padding:20px;}
    .menu{grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;}
    .card{padding:18px;border-radius:16px;}
    .card span{font-size:32px;}
    .card h3{font-size:16px;}
    .card p{font-size:12px;}
    .header{padding:20px;}
}

@media(max-width:600px){
    .menu{grid-template-columns:repeat(2,1fr);}
    .card h3{font-size:14px;}
}

@media(max-width:400px){
    .menu{grid-template-columns:1fr;}
}
</style>
</head>

<body>

<!-- BACKGROUND 3D -->
<img src="../assets/img/bag-3d.png" class="bg-left" alt="Tas Sekolah 3D">
<img src="../assets/img/stationery-3d.png" class="bg-right" alt="Alat Tulis 3D">

<div class="dashboard">
    <div class="header">
        <h2>Dashboard Admin</h2>
        <p>Halo, <b><?= htmlspecialchars($_SESSION['nama']); ?></b></p>
    </div>

    <div class="menu">
        <a href="data_siswa.php" class="card">
            <span>📋</span>
            <h3>Data Siswa</h3>
            <p>Kelola data siswa</p>
        </a>

        <a href="data_absensi.php" class="card">
            <span>📊</span>
            <h3>Data Absensi</h3>
            <p>Laporan kehadiran</p>
        </a>

        <div class="card" onclick="openQR()">
            <span>📱</span>
            <h3>QR Absensi</h3>
            <p>Tampilkan QR untuk siswa</p>
        </div>

        <a href="data_mapel.php" class="card">
            <span>📚</span>
            <h3>Mata Pelajaran</h3>
            <p>Kelola mata pelajaran</p>
        </a>

        <a href="data_kelas.php" class="card">
            <span>🏫</span>
            <h3>Kelas</h3>
            <p>Kelola kelas</p>
        </a>

        <a href="data_jadwal.php" class="card">
            <span>📅</span>
            <h3>Jadwal</h3>
            <p>Kelola jadwal</p>
        </a>

        <a href="data_guru.php" class="card">
            <span>👨‍🏫</span>
            <h3>Guru</h3>
            <p>Kelola data guru</p>
        </a>

        <a href="ranking.php" class="card">
            <span>🏆</span>
            <h3>Ranking</h3>
            <p>Performa guru & siswa</p>
        </a>

        <a href="absensi_guru.php" class="card">
            <span>👨‍🏫</span>
            <h3>Absensi Guru</h3>
            <p>Kehadiran guru</p>
        </a>

        <a href="laporan.php" class="card">
            <span>📄</span>
            <h3>Laporan</h3>
            <p>Ekspor data</p>
        </a>

        <a href="../auth/logout.php" class="card logout">
            <span>🚪</span>
            <h3>Logout</h3>
            <p>Keluar aplikasi</p>
        </a>
    </div>
</div>

<!-- MODAL QR -->
<div class="modal" id="qrModal">
    <div class="modal-box">
        <h3>QR Code Absensi</h3>
        <div id="qrcode"></div>
        <div class="close" onclick="closeQR()">Tutup</div>
    </div>
</div>

<script>
function openQR(){
    document.getElementById("qrModal").style.display="flex";
    document.getElementById("qrcode").innerHTML="";
    new QRCode(document.getElementById("qrcode"),{
        text:"<?= $qr_url ?>",
        width:220,
        height:220
    });
}
function closeQR(){
    document.getElementById("qrModal").style.display="none";
}
</script>

</body>
</html>
