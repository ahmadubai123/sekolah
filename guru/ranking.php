<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('guru');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ranking Performa Guru | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{
        min-height:100vh;
        background:linear-gradient(120deg,#3498db,#2980b9);
        background-size:300% 300%;
        animation:bgMove 10s ease infinite;
        display:flex;
        justify-content:center;
        align-items:center;
        padding:30px;
    }
    @keyframes bgMove{
        0%{background-position:0% 50%}
        50%{background-position:100% 50%}
        100%{background-position:0% 50%}
    }
    .container{
        background:#fff;
        width:100%;
        max-width:900px;
        border-radius:24px;
        padding:30px;
        box-shadow:0 30px 70px rgba(0,0,0,.25);
        animation:fadeUp .8s ease;
    }
    @keyframes fadeUp{
        from{opacity:0;transform:translateY(40px)}
        to{opacity:1;transform:translateY(0)}
    }
    .header{
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:25px;
    }
    .header h2{color:#2c3e50;}
    .back{
        text-decoration:none;
        background:#e8f4fd;
        padding:10px 18px;
        border-radius:12px;
        color:#2980b9;
        font-weight:500;
        transition:.3s;
    }
    .back:hover{background:#2980b9;color:#fff;}
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:15px;text-align:left;font-size:14px;}
    th{background:#f0f8ff;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    tr:hover{background:#e8f4fd;}
    .rank{font-weight:700;font-size:18px;}
    .gold{color:#f1c40f;}
    .silver{color:#bdc3c7;}
    .bronze{color:#cd7f32}
    .badge{
        padding:6px 14px;
        border-radius:20px;
        font-size:12px;
        color:#fff;
    }
    .unggul{background:linear-gradient(135deg,#2ecc71,#27ae60);}
    .baik{background:linear-gradient(135deg,#3498db,#2980b9);}
    .cukup{background:linear-gradient(135deg,#f39c12,#e67e22);}
    .perlu{background:linear-gradient(135deg,#e74c3c,#c0392b);}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🏆 Ranking Performa Guru</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <table>
        <tr>
            <th>Peringkat</th>
            <th>Nama Guru</th>
            <th>Rating</th>
            <th>Absensi Kelas</th>
            <th>Nilai Akademik</th>
            <th>Skor Total</th>
            <th>Kategori</th>
        </tr>
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
</div>

</body>
</html>