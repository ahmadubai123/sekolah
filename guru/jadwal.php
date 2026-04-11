<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('guru');

$teacher_id = getUserId();
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$current_day = $_GET['day'] ?? date('l');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Mengajar | HadirKu</title>
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
    .days{
        display:flex;
        gap:10px;
        margin-bottom:20px;
        flex-wrap:wrap;
    }
    .day-btn{
        padding:10px 16px;
        border-radius:12px;
        border:none;
        background:#f0f8ff;
        color:#2c3e50;
        cursor:pointer;
        transition:.3s;
    }
    .day-btn.active, .day-btn:hover{
        background:linear-gradient(135deg,#3498db,#2980b9);
        color:#fff;
    }
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:15px;text-align:left;font-size:14px;}
    th{background:#f0f8ff;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    tr:hover{background:#e8f4fd;}
    .empty{text-align:center;padding:40px;color:#999;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>📅 Jadwal Mengajar</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <div class="days">
        <?php foreach ($days as $day): ?>
        <a href="?day=<?= $day ?>" class="day-btn <?= $current_day == $day ? 'active' : '' ?>"><?= $day ?></a>
        <?php endforeach; ?>
    </div>

    <table>
        <tr>
            <th>No</th>
            <th>Mata Pelajaran</th>
            <th>Kelas</th>
            <th>Waktu</th>
        </tr>
        <?php
        $query = mysqli_query($conn, "
            SELECT s.id, sub.name as subject, c.name as class, s.start_time, s.end_time
            FROM schedules s
            JOIN subjects sub ON s.subject_id = sub.id
            JOIN classes c ON s.class_id = c.id
            WHERE s.teacher_id = $teacher_id AND s.day_of_week = '$current_day'
            ORDER BY s.start_time
        ");
        $no = 1;
        if (mysqli_num_rows($query) > 0):
            while ($row = mysqli_fetch_assoc($query)):
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['subject']) ?></td>
            <td><?= htmlspecialchars($row['class']) ?></td>
            <td><?= date('H:i', strtotime($row['start_time'])) . ' - ' . date('H:i', strtotime($row['end_time'])) ?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="4" class="empty">Tidak ada jadwal hari ini</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>