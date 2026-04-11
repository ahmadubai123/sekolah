<?php
include "../config/database.php";
include "../config/functions.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = getUserId();
$today = date('Y-m');

// Get all students with their attendance stats
$students = mysqli_query($conn, "
    SELECT u.id, u.nama_lengkap,
        (SELECT COUNT(*) FROM absensi WHERE user_id = u.id AND tanggal <= CURDATE()) as total_absen,
        (SELECT COUNT(*) FROM absensi WHERE user_id = u.id AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND keterangan = 'Hadir') as hadir_30_hari
    FROM users u
    WHERE u.role = 'siswa'
    ORDER BY hadir_30_hari DESC, total_absen DESC
");

// Get current student stats
$my_stats = mysqli_query($conn, "
    SELECT 
        (SELECT COUNT(*) FROM absensi WHERE user_id = $student_id AND tanggal <= CURDATE()) as total_absen,
        (SELECT COUNT(*) FROM absensi WHERE user_id = $student_id AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND keterangan = 'Hadir') as hadir_30_hari
");
$my_data = mysqli_fetch_assoc($my_stats);

// Calculate rank
$rank = 1;
$my_position = 0;
mysqli_data_seek($students, 0);
while ($row = mysqli_fetch_assoc($students)) {
    if ($row['id'] == $student_id) {
        $my_position = $rank;
        break;
    }
    $rank++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ranking Siswa | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(135deg,#667eea,#764ba2);padding:30px;}
    .container{background:#fff;max-width:800px;margin:0 auto;border-radius:24px;padding:30px;box-shadow:0 25px 45px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#f1f1f1;padding:10px 18px;border-radius:12px;color:#333;}
    .my-rank{background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:25px;border-radius:20px;text-align:center;margin-bottom:25px;}
    .my-rank h3{font-size:18px;opacity:.9;}
    .my-rank h1{font-size:48px;margin:10px 0;}
    .my-rank p{font-size:14px;opacity:.8;}
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:12px;text-align:left;}
    th{background:#f4fdf7;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    tr:hover{background:#f4fdf7;}
    .rank-num{font-weight:700;font-size:18px;}
    .gold{color:#f1c40f;}
    .silver{color:#bdc3c7;}
    .bronze{color:#cd7f32}
    .me{background:#e8f4fd;font-weight:600;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🏆 Ranking Siswa</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <div class="my-rank">
        <h3>Posisi Kamu</h3>
        <h1>#<?= $my_position ?: '-' ?></h1>
        <p>Total Absensi: <?= $my_data['total_absen'] ?> | 30 Hari: <?= $my_data['hadir_30_hari'] ?></p>
    </div>

    <table>
        <tr><th>Peringkat</th><th>Nama Siswa</th><th>Total Absensi</th><th>30 Hari Terakhir</th></tr>
        <?php 
        $no = 1;
        mysqli_data_seek($students, 0);
        while ($row = mysqli_fetch_assoc($students)): 
            $rankClass = $no == 1 ? 'gold' : ($no == 2 ? 'silver' : ($no == 3 ? 'bronze' : ''));
            $rowClass = $row['id'] == $student_id ? 'me' : '';
        ?>
        <tr class="<?= $rowClass ?>">
            <td class="rank-num <?= $rankClass ?>">#<?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
            <td><?= $row['total_absen'] ?></td>
            <td><?= $row['hadir_30_hari'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>