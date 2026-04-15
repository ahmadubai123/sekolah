<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('guru');

$teacher_id = getUserId();
$today = date('Y-m-d');

// Handle redirect to prevent resubmission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the alert message before any output
    $alert_msg = '';
    
    $action = $_POST['action'] ?? '';
    
    $cek = mysqli_query($conn, "SELECT * FROM absensi_guru WHERE teacher_id = $teacher_id AND tanggal = '$today'");
    
    if ($action === 'check_in') {
        if (mysqli_num_rows($cek) > 0) {
            $alert_msg = 'Kamu sudah check-in hari ini';
        } else {
            $jam = date("H:i:s");
            mysqli_query($conn, "INSERT INTO absensi_guru (teacher_id, tanggal, jam_masuk, status) VALUES ($teacher_id, '$today', '$jam', 'Hadir')");
            $alert_msg = 'Check-in berhasil!';
        }
    } elseif ($action === 'check_out') {
        if (mysqli_num_rows($cek) == 0) {
            $alert_msg = 'Kamu belum check-in';
        } else {
            $jam = date("H:i:s");
            mysqli_query($conn, "UPDATE absensi_guru SET jam_pulang = '$jam' WHERE teacher_id = $teacher_id AND tanggal = '$today'");
            $alert_msg = 'Check-out berhasil!';
        }
    }
    
    // Redirect after POST to prevent resubmission
    if ($alert_msg) {
        echo "<script>alert('$alert_msg'); window.location.href = 'absensi_guru.php';</script>";
        exit;
    }
}

// Get today's attendance
$absen_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM absensi_guru WHERE teacher_id = $teacher_id AND tanggal = '$today'"));

// Stats
$total_hadir = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi_guru WHERE teacher_id = $teacher_id AND status = 'Hadir'"));
$bulan_ini = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi_guru WHERE teacher_id = $teacher_id AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND status = 'Hadir'"));

// History
$history = mysqli_query($conn, "SELECT * FROM absensi_guru WHERE teacher_id = $teacher_id ORDER BY tanggal DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Guru | MadrasahKu</title>
    <meta name="theme-color" content="#2ecc71">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="../../manifest.json">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(120deg,#2ecc71,#27ae60);padding:30px;}
    .container{background:#fff;max-width:800px;margin:0 auto;border-radius:24px;padding:30px;box-shadow:0 30px 70px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#e8f4fd;padding:10px 18px;border-radius:12px;color:#2980b9;}
    .status-box{background:linear-gradient(135deg,#2ecc71,#27ae60);color:#fff;padding:30px;border-radius:20px;text-align:center;margin-bottom:25px;}
    .status-box h3{font-size:18px;opacity:.9;}
    .status-box .time{font-size:48px;font-weight:700;margin:10px 0;}
    .status-box .date{font-size:14px;}
    .buttons{display:flex;gap:15px;margin-bottom:25px;}
    .btn{flex:1;padding:15px;border-radius:12px;border:none;font-size:16px;cursor:pointer;}
    .btn-checkin{background:linear-gradient(135deg,#2ecc71,#27ae60);color:#fff;}
    .btn-checkout{background:linear-gradient(135deg,#e74c3c,#c0392b);color:#fff;}
    .btn:disabled{background:#bdc3c7;cursor:not-allowed;}
    .stats{display:flex;gap:20px;margin-bottom:25px;}
    .stat{flex:1;background:#f0f8ff;padding:20px;border-radius:16px;text-align:center;}
    .stat h3{color:#2980b9;font-size:28px;}
    .stat p{color:#666;}
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:12px;text-align:left;}
    th{background:#f0f8ff;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    .hadir{color:#27ae60;font-weight:600;}
    .alpha{color:#e74c3c;}
    .izin{color:#f39c12;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>📝 Absensi Guru</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <div class="status-box">
        <h3><?= date('l, d M Y') ?></h3>
        <div class="time"><?= date('H:i:s') ?></div>
        <div class="date">Status: <?= $absen_hari_ini ? ($absen_hari_ini['jam_pulang'] ? 'Selesai' : 'Bekerja') : 'Belum Check-in' ?></div>
    </div>

    <div class="buttons">
        <form method="POST">
            <input type="hidden" name="action" value="check_in">
            <button type="submit" class="btn btn-checkin" <?= $absen_hari_ini ? 'disabled' : '' ?>>
                ✅ Check In<br><small><?= $absen_hari_ini ? date('H:i', strtotime($absen_hari_ini['jam_masuk'])) : '--:--' ?></small>
            </button>
        </form>
        <form method="POST">
            <input type="hidden" name="action" value="check_out">
            <button type="submit" class="btn btn-checkout" <?= !$absen_hari_ini || $absen_hari_ini['jam_pulang'] ? 'disabled' : '' ?>>
                🏁 Check Out<br><small><?= $absen_hari_ini && $absen_hari_ini['jam_pulang'] ? date('H:i', strtotime($absen_hari_ini['jam_pulang'])) : '--:--' ?></small>
            </button>
        </form>
    </div>

    <div class="stats">
        <div class="stat">
            <h3><?= $total_hadir ?></h3>
            <p>Total Hadir</p>
        </div>
        <div class="stat">
            <h3><?= $bulan_ini ?></h3>
            <p>Bulan Ini</p>
        </div>
        <div class="stat">
            <h3><?= date('t') - $bulan_ini ?></h3>
            <p>Tersisa</p>
        </div>
    </div>

    <h3>Riwayat Absensi</h3>
    <table>
        <tr><th>No</th><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($history)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
            <td><?= $row['jam_masuk'] ? date('H:i', strtotime($row['jam_masuk'])) : '-' ?></td>
            <td><?= $row['jam_pulang'] ? date('H:i', strtotime($row['jam_pulang'])) : '-' ?></td>
            <td class="<?= strtolower($row['status']) ?>"><?= $row['status'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<script src="../../assets/js/pwa.js"></script>
<script>
setInterval(() => {
    const now = new Date();
    document.querySelector('.time').textContent = now.toLocaleTimeString('id-ID');
}, 1000);
</script>

</body>
</html>