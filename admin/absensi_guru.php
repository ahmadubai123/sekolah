<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

$today = date('Y-m-d');

// Get all guru attendance today
$guru_hari_ini = mysqli_query($conn, "
    SELECT u.nama_lengkap, ag.jam_masuk, ag.jam_pulang, ag.status
    FROM absensi_guru ag
    JOIN users u ON ag.teacher_id = u.id
    WHERE ag.tanggal = '$today'
    ORDER BY ag.jam_masuk DESC
");

// Stats
$total_guru = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role = 'guru'"));
$guru_hadir = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi_guru WHERE tanggal = '$today' AND status = 'Hadir'"));

// All guru for attendance
$semua_guru = mysqli_query($conn, "SELECT id, nama_lengkap FROM users WHERE role = 'guru' ORDER BY nama_lengkap");

// Handle manual attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teacher_id'])) {
    $teacher_id = (int)$_POST['teacher_id'];
    $status = sanitize($_POST['status']);
    $jam = date("H:i:s");
    
    $cek = mysqli_query($conn, "SELECT id FROM absensi_guru WHERE teacher_id = $teacher_id AND tanggal = '$today'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE absensi_guru SET status = '$status' WHERE teacher_id = $teacher_id AND tanggal = '$today'");
    } else {
        mysqli_query($conn, "INSERT INTO absensi_guru (teacher_id, tanggal, jam_masuk, status) VALUES ($teacher_id, '$today', '$jam', '$status')");
    }
    header("Location: absensi_guru.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Guru | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(120deg,#2ecc71,#27ae60);padding:30px;}
    .container{background:#fff;max-width:900px;margin:0 auto;border-radius:24px;padding:30px;box-shadow:0 30px 70px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#eafff3;padding:10px 18px;border-radius:12px;color:#27ae60;}
    .stats{display:flex;gap:20px;margin-bottom:25px;}
    .stat{flex:1;background:#f4fdf7;padding:20px;border-radius:16px;text-align:center;}
    .stat h3{color:#27ae60;font-size:32px;}
    .stat p{color:#666;}
    .form-add{background:#f4fdf7;padding:20px;border-radius:16px;margin-bottom:25px;}
    .form-add select,.form-add button{width:100%;padding:12px;border-radius:10px;border:1px solid #ddd;margin-bottom:10px;}
    .form-add button{background:linear-gradient(135deg,#2ecc71,#27ae60);color:#fff;border:none;cursor:pointer;}
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:12px;text-align:left;}
    th{background:#f4fdf7;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    .hadir{color:#27ae60;font-weight:600;}
    .alpha{color:#e74c3c;}
    .izin{color:#f39c12;}
    .sakit{color:#9b59b6;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>📝 Absensi Guru Hari Ini</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <div class="stats">
        <div class="stat">
            <h3><?= $total_guru ?></h3>
            <p>Total Guru</p>
        </div>
        <div class="stat">
            <h3><?= $guru_hadir ?></h3>
            <p>Hadir</p>
        </div>
        <div class="stat">
            <h3><?= $total_guru - $guru_hadir ?></h3>
            <p>Belum Absen</p>
        </div>
    </div>

    <div class="form-add">
        <h3>Tambah Absensi Manual</h3>
        <form method="POST">
            <select name="teacher_id" required>
                <option value="">-- Pilih Guru --</option>
                <?php while($g=mysqli_fetch_assoc($semua_guru)): ?>
                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_lengkap']) ?></option>
                <?php endwhile; ?>
            </select>
            <select name="status" required>
                <option value="Hadir">Hadir</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
                <option value="Alpha">Alpha</option>
            </select>
            <button type="submit">Simpan Absensi</button>
        </form>
    </div>

    <h3>Daftar Guru yang Sudah Absen (<?= date('d M Y') ?>)</h3>
    <table>
        <tr><th>No</th><th>Nama Guru</th><th>Masuk</th><th>Pulang</th><th>Status</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($guru_hari_ini)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
            <td><?= $row['jam_masuk'] ? date('H:i', strtotime($row['jam_masuk'])) : '-' ?></td>
            <td><?= $row['jam_pulang'] ? date('H:i', strtotime($row['jam_pulang'])) : '-' ?></td>
            <td class="<?= strtolower($row['status']) ?>"><?= $row['status'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>