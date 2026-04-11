<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

$type = $_GET['type'] ?? 'guru';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ranking | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(120deg,#2ecc71,#27ae60);padding:30px;}
    .container{background:#fff;max-width:900px;margin:0 auto;border-radius:24px;padding:30px;box-shadow:0 30px 70px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#eafff3;padding:10px 18px;border-radius:12px;color:#27ae60;}
    .tabs{display:flex;gap:10px;margin-bottom:20px;}
    .tab{padding:12px 24px;border-radius:12px;border:none;background:#f4fdf7;color:#2c3e50;cursor:pointer;text-decoration:none;}
    .tab.active{background:linear-gradient(135deg,#2ecc71,#27ae60);color:#fff;}
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:12px;text-align:left;}
    th{background:#f4fdf7;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    tr:hover{background:#eafff3;}
    .rank{font-weight:700;font-size:20px;}
    .gold{color:#f1c40f;}
    .silver{color:#bdc3c7;}
    .bronze{color:#cd7f32}
    .badge{padding:6px 14px;border-radius:20px;font-size:12px;color:#fff;}
    .unggul{background:#2ecc71;}
    .baik{background:#3498db;}
    .cukup{background:#f39c12;}
    .perlu{background:#e74c3c;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🏆 Ranking & Performa</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <div class="tabs">
        <a href="?type=guru" class="tab <?= $type == 'guru' ? 'active' : '' ?>">👨‍🏫 Guru</a>
        <a href="?type=siswa" class="tab <?= $type == 'siswa' ? 'active' : '' ?>">👦 Siswa</a>
    </div>

    <?php if ($type == 'guru'): ?>
    <h3>Ranking Guru</h3>
    <table>
        <tr><th>Peringkat</th><th>Nama Guru</th><th>Rating</th><th>Absensi Kelas</th><th>Nilai Akademik</th><th>Skor Total</th><th>Kategori</th></tr>
        <?php
        $query = mysqli_query($conn, "
            SELECT u.id, u.nama_lengkap,
                COALESCE(AVG(f.rating), 0) as avg_rating,
                COALESCE(p.attendance_rate, 0) as attendance,
                COALESCE(p.academic_achievement_score, 0) as academic,
                COALESCE(p.ranking_score, 0) as score,
                COALESCE(p.category, 'Perlu Perbaikan') as category
            FROM users u
            LEFT JOIN feedback f ON f.teacher_id = u.id
            LEFT JOIN performance p ON p.teacher_id = u.id
            WHERE u.role = 'guru'
            GROUP BY u.id
            ORDER BY score DESC
        ");
        $rank = 1;
        while ($row = mysqli_fetch_assoc($query)):
            $rankClass = $rank == 1 ? 'gold' : ($rank == 2 ? 'silver' : ($rank == 3 ? 'bronze' : ''));
            $badgeClass = strtolower(str_replace(' ', '', $row['category']));
        ?>
        <tr>
            <td class="rank <?= $rankClass ?>">#<?= $rank++ ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
            <td><?= number_format($row['avg_rating'], 1) ?>/5</td>
            <td><?= number_format($row['attendance'], 1) ?>%</td>
            <td><?= number_format($row['academic'], 1) ?></td>
            <td><?= number_format($row['score'], 1) ?></td>
            <td><span class="badge <?= $badgeClass ?>"><?= $row['category'] ?></span></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
    <h3>Ranking Siswa</h3>
    <table>
        <tr><th>Peringkat</th><th>Nama Siswa</th><th>Total Absensi</th><th>30 Hari Terakhir</th><th>Persentase</th></tr>
        <?php
        $query = mysqli_query($conn, "
            SELECT u.id, u.nama_lengkap,
                (SELECT COUNT(*) FROM absensi WHERE user_id = u.id) as total_absen,
                (SELECT COUNT(*) FROM absensi WHERE user_id = u.id AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND keterangan = 'Hadir') as hadir_30_hari
            FROM users u
            WHERE u.role = 'siswa'
            ORDER BY hadir_30_hari DESC, total_absen DESC
        ");
        $rank = 1;
        while ($row = mysqli_fetch_assoc($query)):
            $total_hari = 30;
            $persen = round(($row['hadir_30_hari'] / $total_hari) * 100);
            $rankClass = $rank == 1 ? 'gold' : ($rank == 2 ? 'silver' : ($rank == 3 ? 'bronze' : ''));
        ?>
        <tr>
            <td class="rank <?= $rankClass ?>">#<?= $rank++ ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
            <td><?= $row['total_absen'] ?></td>
            <td><?= $row['hadir_30_hari'] ?></td>
            <td><?= $persen ?>%</td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php endif; ?>
</div>

</body>
</html>