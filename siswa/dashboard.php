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
<title>Kehadiran Siswa | HadirKu</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

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

/* ===== BACKGROUND SISWA ===== */
.student-left,
.student-right{
    position:absolute;
    bottom:0;
    width:260px;
    animation:float 6s ease-in-out infinite;
    pointer-events:none;
}

.student-left{
    left:3%;
}

.student-right{
    right:3%;
    animation-delay:1.5s;
}

@keyframes float{
    0%{transform:translateY(0)}
    50%{transform:translateY(-18px)}
    100%{transform:translateY(0)}
}

/* ===== DASHBOARD ===== */
.dashboard{
    width:90%;
    max-width:900px;
    background:#fff;
    border-radius:26px;
    padding:35px;
    box-shadow:0 35px 70px rgba(0,0,0,.25);
    z-index:2;
    animation:fadeUp .8s ease;
}

@keyframes fadeUp{
    from{opacity:0;transform:translateY(40px)}
    to{opacity:1;transform:translateY(0)}
}

/* HEADER */
.header{
    background:linear-gradient(135deg,#2ecc71,#27ae60);
    color:#fff;
    padding:30px;
    border-radius:20px;
    text-align:center;
    margin-bottom:30px;
}

.header h2{
    font-size:28px;
    margin-bottom:6px;
}

.header p{
    font-size:15px;
    opacity:.9;
}

/* MENU */
.menu{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:22px;
}

/* CARD */
.card{
    background:#f4fdf7;
    padding:28px;
    border-radius:20px;
    text-decoration:none;
    color:#2c3e50;
    text-align:center;
    transition:.35s;
}

.card span{
    font-size:46px;
}

.card h3{
    margin-top:14px;
    margin-bottom:6px;
}

.card p{
    font-size:14px;
    color:#666;
}

.card:hover{
    transform:translateY(-12px);
    background:linear-gradient(135deg,#2ecc71,#27ae60);
    color:#fff;
}

.card:hover p{
    color:#eafff3;
}

/* LOGOUT */
.logout:hover{
    background:linear-gradient(135deg,#ff6b6b,#ff4757);
}

/* RESPONSIVE */
@media(max-width:900px){
    .student-left,
    .student-right{
        display:none;
    }
}
</style>
</head>

<body>

<!-- SISWA KIRI -->
<img src="../assets/img/student-pramuka-left.png" class="student-left" alt="Siswa Pramuka">

<!-- SISWA KANAN -->
<img src="../assets/img/student-hijau-right.png" class="student-right" alt="Siswa MI">

<div class="dashboard">

    <div class="header">
        <h2>Kehadiran Siswa</h2>
        <p>Halo, <b><?= htmlspecialchars($_SESSION['nama']); ?></b></p>
    </div>

    <div class="menu">
        <a href="scan_qr.php" class="card">
            <span>📷</span>
            <h3>Scan QR</h3>
            <p>Absensi hari ini</p>
        </a>

        <a href="feedback.php" class="card">
            <span>⭐</span>
            <h3>Feedback</h3>
            <p>Beri rating guru</p>
        </a>

        <a href="ranking.php" class="card">
            <span>🏆</span>
            <h3>Ranking</h3>
            <p>Lihat ranking siswa</p>
        </a>

        <a href="profil.php" class="card">
            <span>👤</span>
            <h3>Profil</h3>
            <p>Edit data diri</p>
        </a>

        <a href="../auth/logout.php" class="card logout">
            <span>🚪</span>
            <h3>Logout</h3>
            <p>Keluar dari akun</p>
        </a>
    </div>

</div>

</body>
</html>
