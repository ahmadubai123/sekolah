<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('guru');

$teacher_id = getUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = (int)$_POST['student_id'];
    $status = sanitize($_POST['status']);
    $tanggal = date('Y-m-d');
    $jam = date('H:i:s');
    
    $cek = mysqli_query($conn, "SELECT id FROM absensi WHERE user_id = $student_id AND tanggal = '$tanggal'");
    if (mysqli_num_rows($cek) > 0) {
        $msg = "Siswa sudah absen hari ini";
    } else {
        $query = mysqli_query($conn, "INSERT INTO absensi (user_id, tanggal, jam, keterangan) VALUES ($student_id, '$tanggal', '$jam', '$status')");
        $msg = $query ? "Absensi berhasil" : "Gagal menyimpan";
    }
    header("Content-Type: application/json");
    echo json_encode(['message' => $msg]);
    exit;
}

$today = date('Y-m-d');
$schedule_id = $_GET['schedule'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Absensi | HadirKu</title>
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
    .filters{display:flex;gap:15px;margin-bottom:20px;flex-wrap:wrap;}
    .filters select, .filters input{
        padding:10px 15px;
        border-radius:10px;
        border:1px solid #ddd;
    }
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:12px;text-align:left;font-size:14px;}
    th{background:#f0f8ff;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    tr:hover{background:#e8f4fd;}
    .btn{background:linear-gradient(135deg,#3498db,#2980b9);color:#fff;border:none;padding:8px 14px;border-radius:8px;cursor:pointer;}
    .status-hadir{color:#27ae60;font-weight:600;}
    .status-alpha{color:#e74c3c;font-weight:600;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>📊 Kelola Absensi</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <div class="filters">
        <select onchange="filterSchedule(this.value)">
            <option value="">Semua Jadwal</option>
            <?php
            $schedules = mysqli_query($conn, "
                SELECT s.id, sub.name as subject, c.name as class, s.day_of_week, s.start_time
                FROM schedules s
                JOIN subjects sub ON s.subject_id = sub.id
                JOIN classes c ON s.class_id = c.id
                WHERE s.teacher_id = $teacher_id
                ORDER BY s.day_of_week, s.start_time
            ");
            while ($s = mysqli_fetch_assoc($schedules)):
            ?>
            <option value="<?= $s['id'] ?>" <?= $schedule_id == $s['id'] ? 'selected' : '' ?>>
                <?= $s['subject'] . ' - ' . $s['class'] . ' (' . $s['day_of_week'] . ')' ?>
            </option>
            <?php endwhile; ?>
        </select>
        <input type="date" value="<?= $today ?>" onchange="filterDate(this.value)">
    </div>

    <table>
        <tr>
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Jam Absen</th>
            <th>Status</th>
        </tr>
        <?php
        $where = "WHERE a.tanggal = '$today'";
        if ($schedule_id > 0) $where .= " AND a.schedule_id = $schedule_id";
        
        $query = mysqli_query($conn, "
            SELECT u.nama_lengkap, c.name as class_name, a.jam, a.keterangan
            FROM absensi a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN class_student cs ON cs.student_id = u.id
            LEFT JOIN classes c ON cs.class_id = c.id
            $where
            ORDER BY a.jam DESC
        ");
        $no = 1;
        if (mysqli_num_rows($query) > 0):
            while ($row = mysqli_fetch_assoc($query)):
                $statusClass = $row['keterangan'] === 'Hadir' ? 'status-hadir' : 'status-alpha';
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
            <td><?= htmlspecialchars($row['class_name'] ?? '-') ?></td>
            <td><?= date('H:i', strtotime($row['jam'])) ?></td>
            <td class="<?= $statusClass ?>"><?= $row['keterangan'] ?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="5" style="text-align:center;padding:40px;color:#999;">Belum ada absensi hari ini</td></tr>
        <?php endif; ?>
    </table>
</div>

<script>
function filterSchedule(id){window.location.href = '?schedule=' + id;}
function filterDate(date){console.log('Filter date:', date);}
</script>

</body>
</html>