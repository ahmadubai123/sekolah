<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('guru');

$teacher_id = getUserId();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Feedback & Rating | HadirKu</title>
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
    }
    .back:hover{background:#2980b9;color:#fff;}
    .stats{display:flex;gap:20px;margin-bottom:25px;flex-wrap:wrap;}
    .stat-box{
        flex:1;
        min-width:150px;
        background:#f0f8ff;
        padding:20px;
        border-radius:16px;
        text-align:center;
    }
    .stat-box h3{font-size:28px;color:#2980b9;}
    .stat-box p{color:#666;font-size:14px;}
    .feedback-item{
        background:#fafafa;
        padding:20px;
        border-radius:16px;
        margin-bottom:15px;
    }
    .feedback-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
    .stars{color:#f1c40f;font-size:18px;}
    .anon{background:#e8f4fd;color:#2980b9;padding:4px 10px;border-radius:10px;font-size:12px;}
    .name{font-weight:600;color:#2c3e50;}
    .date{color:#999;font-size:12px;}
    .comment{color:#555;line-height:1.6;}
    .empty{text-align:center;padding:40px;color:#999;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>⭐ Feedback & Rating</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <?php
    $stats = mysqli_query($conn, "
        SELECT 
            COUNT(*) as total,
            AVG(rating) as avg_rating,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
            SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) as low_star
        FROM feedback WHERE teacher_id = $teacher_id
    ");
    $stats_data = mysqli_fetch_assoc($stats);
    ?>
    <div class="stats">
        <div class="stat-box">
            <h3><?= $stats_data['total'] ?></h3>
            <p>Total Feedback</p>
        </div>
        <div class="stat-box">
            <h3><?= number_format($stats_data['avg_rating'] ?? 0, 1) ?></h3>
            <p>Rating Rata-rata</p>
        </div>
        <div class="stat-box">
            <h3>⭐⭐⭐⭐⭐</h3>
            <p><?= $stats_data['five_star'] ?>x</p>
        </div>
    </div>

    <?php
    $query = mysqli_query($conn, "
        SELECT f.*, u.nama_lengkap as student_name
        FROM feedback f
        LEFT JOIN users u ON f.student_id = u.id AND f.anonymous = 0
        WHERE f.teacher_id = $teacher_id
        ORDER BY f.created_at DESC
        LIMIT 20
    ");
    if (mysqli_num_rows($query) > 0):
        while ($row = mysqli_fetch_assoc($query)):
    ?>
    <div class="feedback-item">
        <div class="feedback-header">
            <span class="name"><?= $row['anonymous'] ? 'Anonim' : htmlspecialchars($row['student_name']) ?></span>
            <span class="date"><?= date('d M y', strtotime($row['created_at'])) ?></span>
        </div>
        <div class="stars"><?= str_repeat('⭐', $row['rating']) ?></div>
        <?php if ($row['comment']): ?>
        <p class="comment"><?= htmlspecialchars($row['comment']) ?></p>
        <?php endif; ?>
    </div>
    <?php endwhile; else: ?>
    <p class="empty">Belum ada feedback</p>
    <?php endif; ?>
</div>

</body>
</html>