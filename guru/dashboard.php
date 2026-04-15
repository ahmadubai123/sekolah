<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('guru');

$user_id = getUserId();
$teacher_name = getUserName();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru | MadrasahKu</title>
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
        background:#f0f8ff;
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
        background:linear-gradient(135deg,#e74c3c,#c0392b);
    }
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
    .modal-box select, .modal-box button{
        padding:12px;
        margin:10px 0;
        border-radius:10px;
        border:1px solid #ddd;
        width:100%;
    }
    .modal-box button{
        background:linear-gradient(135deg,#3498db,#2980b9);
        color:#fff;
        border:none;
        cursor:pointer;
    }
    .close{
        margin-top:15px;
        color:#2980b9;
        cursor:pointer;
    }
    @media(max-width:900px){
        .dashboard{width:95%;padding:20px;}
    }
    </style>
</head>
<body>

<div class="dashboard">
    <div class="header">
        <h2>Dashboard Guru</h2>
        <p>Halo, <b><?= htmlspecialchars($teacher_name); ?></b></p>
    </div>

    <div class="menu">
        <a href="jadwal.php" class="card">
            <span>📅</span>
            <h3>Jadwal Mengajar</h3>
            <p>Lihat jadwal mingguan</p>
        </a>
        <a href="absensi_guru.php" class="card">
            <span>📝</span>
            <h3>Absensi Saya</h3>
            <p>Check in/out guru</p>
        </a>
        <a href="absensi.php" class="card">
            <span>📊</span>
            <h3>Kelola Absensi</h3>
            <p>Catat & lihat absensi</p>
        </a>
        <div class="card" onclick="openQR()">
            <span>📱</span>
            <h3>QR Absensi</h3>
            <p>Buat QR untuk kelas</p>
        </div>
        <a href="ranking.php" class="card">
            <span>🏆</span>
            <h3>Ranking</h3>
            <p>Lihat performa guru</p>
        </a>
        <a href="tren.php" class="card">
            <span>📈</span>
            <h3>Grafik Tren</h3>
            <p>Lihat statistik</p>
        </a>
        <a href="feedback.php" class="card">
            <span>⭐</span>
            <h3>Feedback</h3>
            <p>Lihat rating & komentar</p>
        </a>
        <a href="profil.php" class="card">
            <span>👤</span>
            <h3>Profil</h3>
            <p>Kelola data diri</p>
        </a>
        <a href="../auth/logout.php" class="card logout">
            <span>🚪</span>
            <h3>Logout</h3>
            <p>Keluar aplikasi</p>
        </a>
    </div>
</div>

<div class="modal" id="qrModal">
    <div class="modal-box">
        <h3>QR Code Absensi</h3>
        <p>Pilih jadwal:</p>
        <select id="scheduleSelect">
            <option value="">-- Pilih Jadwal --</option>
        </select>
        <button onclick="generateQR()">Generate QR</button>
        <div id="qrcode" style="margin:20px 0;"></div>
        <div class="close" onclick="closeQR()">Tutup</div>
    </div>
</div>

<script>
function openQR(){
    document.getElementById("qrModal").style.display="flex";
    loadSchedules();
}
function closeQR(){
    document.getElementById("qrModal").style.display="none";
}
function loadSchedules(){
    fetch('get_schedules.php')
    .then(res => res.json())
    .then(data => {
        const select = document.getElementById('scheduleSelect');
        select.innerHTML = '<option value="">-- Pilih Jadwal --</option>';
        data.forEach(s => {
            select.innerHTML += `<option value="${s.id}">${s.subject} - ${s.class} (${s.time})</option>`;
        });
    });
}
function generateQR(){
    const scheduleId = document.getElementById('scheduleSelect').value;
    if(!scheduleId){alert('Pilih jadwal dulu');return;}
    fetch('generate_qr.php?id='+scheduleId)
    .then(res => res.json())
    .then(data => {
        document.getElementById("qrcode").innerHTML = "";
        new QRCode(document.getElementById("qrcode"),{
            text:data.url,
            width:220,
            height:220
        });
    });
}
</script>

</body>
</html>