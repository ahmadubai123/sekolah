<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

$current_period = date('Y-m');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan & Ekspor | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(120deg,#2ecc71,#27ae60);display:flex;justify-content:center;align-items:center;padding:30px;}
    .container{background:#fff;width:100%;max-width:600px;border-radius:24px;padding:30px;box-shadow:0 30px 70px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#eafff3;padding:10px 18px;border-radius:12px;color:#27ae60;font-weight:500;}
    .back:hover{background:#27ae60;color:#fff;}
    .section{background:#f4fdf7;padding:20px;border-radius:16px;margin-bottom:20px;}
    .section h3{color:#2c3e50;margin-bottom:15px;}
    form label{display:block;margin-bottom:8px;color:#2c3e50;font-weight:500;}
    form select, form input{width:100%;padding:12px 15px;margin-bottom:15px;border-radius:10px;border:1px solid #ddd;}
    .btn{display:block;width:100%;padding:12px;border-radius:10px;border:none;background:linear-gradient(135deg,#2ecc71,#27ae60);color:#fff;cursor:pointer;text-decoration:none;text-align:center;font-weight:500;}
    .btn:hover{opacity:0.9;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>📄 Laporan & Ekspor</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <div class="section">
        <h3>Laporan Absensi</h3>
        <form method="GET" action="laporan.php">
            <input type="hidden" name="type" value="absensi">
            <label>Pilih Bulan</label>
            <input type="month" name="period" value="<?= $current_period ?>">
            <button type="submit" class="btn">📥 Ekspor CSV</button>
        </form>
    </div>

    <div class="section">
        <h3>Ranking Guru</h3>
        <a href="laporan.php?type=ranking" class="btn">📥 Ekspor CSV</a>
    </div>

    <div class="section">
        <h3>Data Siswa</h3>
        <a href="laporan.php?type=siswa" class="btn">📥 Ekspor CSV</a>
    </div>
</div>

</body>
</html>