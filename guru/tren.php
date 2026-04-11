<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('guru');

$teacher_id = getUserId();

$monthly_attendance = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i month"));
    $query = mysqli_query($conn, "
        SELECT COUNT(*) as total, 
            SUM(CASE WHEN a.keterangan = 'Hadir' THEN 1 ELSE 0 END) as hadir
        FROM absensi a
        JOIN schedules s ON a.schedule_id = s.id
        WHERE s.teacher_id = $teacher_id AND DATE_FORMAT(a.tanggal, '%Y-%m') = '$month'
    ");
    $data = mysqli_fetch_assoc($query);
    $monthly_attendance[] = [
        'month' => date('M y', strtotime($month)),
        'rate' => $data['total'] > 0 ? round(($data['hadir'] / $data['total']) * 100, 1) : 0
    ];
}

$monthly_feedback = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i month"));
    $query = mysqli_query($conn, "
        SELECT AVG(rating) as avg_rating
        FROM feedback
        WHERE teacher_id = $teacher_id AND DATE_FORMAT(created_at, '%Y-%m') = '$month'
    ");
    $data = mysqli_fetch_assoc($query);
    $monthly_feedback[] = [
        'month' => date('M y', strtotime($month)),
        'rating' => $data['avg_rating'] ? round($data['avg_rating'], 2) : 0
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Grafik Tren | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        max-width:1000px;
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
    .charts{
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(400px,1fr));
        gap:25px;
    }
    .chart-box{
        background:#f8fafc;
        padding:20px;
        border-radius:16px;
    }
    .chart-box h3{color:#2c3e50;margin-bottom:15px;text-align:center;}
    @media(max-width:900px){
        .charts{grid-template-columns:1fr;}
    }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>📈 Grafik Tren</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <div class="charts">
        <div class="chart-box">
            <h3>Tingkat Absensi Bulanan</h3>
            <canvas id="attendanceChart"></canvas>
        </div>
        <div class="chart-box">
            <h3>Tren Rating Feedback</h3>
            <canvas id="feedbackChart"></canvas>
        </div>
    </div>
</div>

<script>
const attendanceData = <?= json_encode(array_column($monthly_attendance, 'rate')) ?>;
const attendanceLabels = <?= json_encode(array_column($monthly_attendance, 'month')) ?>;
const feedbackData = <?= json_encode(array_column($monthly_feedback, 'rating')) ?>;
const feedbackLabels = <?= json_encode(array_column($monthly_feedback, 'month')) ?>;

new Chart(document.getElementById('attendanceChart'), {
    type: 'line',
    data: {
        labels: attendanceLabels,
        datasets: [{
            label: 'Tingkat Absensi (%)',
            data: attendanceData,
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: { y: { min: 0, max: 100 } }
    }
});

new Chart(document.getElementById('feedbackChart'), {
    type: 'bar',
    data: {
        labels: feedbackLabels,
        datasets: [{
            label: 'Rating Rata-rata',
            data: feedbackData,
            backgroundColor: ['#f1c40f','#f39c12','#e67e22','#e74c3c','#9b59b6','#3498db']
        }]
    },
    options: {
        responsive: true,
        scales: { y: { min: 0, max: 5 } }
    }
});
</script>

</body>
</html>