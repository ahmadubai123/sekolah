<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_id'])) {
    $class_id = (int)$_POST['class_id'];
    $teacher_id = (int)$_POST['teacher_id'];
    $subject_id = (int)$_POST['subject_id'];
    $day = sanitize($_POST['day_of_week']);
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    
    mysqli_query($conn, "INSERT INTO schedules (class_id, teacher_id, subject_id, day_of_week, start_time, end_time) VALUES ($class_id, $teacher_id, $subject_id, '$day', '$start', '$end')");
    header("Location: data_jadwal.php");
    exit;
}

$schedules = mysqli_query($conn, "
    SELECT s.id, s.day_of_week, s.start_time, s.end_time, sub.name as subject, c.name as class, u.nama_lengkap as guru
    FROM schedules s
    JOIN subjects sub ON s.subject_id = sub.id
    JOIN classes c ON s.class_id = c.id
    JOIN users u ON s.teacher_id = u.id
    ORDER BY FIELD(s.day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), s.start_time
");

$classes = mysqli_query($conn, "SELECT * FROM classes ORDER BY grade, name");
$teachers = mysqli_query($conn, "SELECT * FROM users WHERE role = 'guru' ORDER BY nama_lengkap");
$subjects = mysqli_query($conn, "SELECT * FROM subjects ORDER BY name");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Jadwal | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(120deg,#2ecc71,#27ae60);display:flex;justify-content:center;align-items:center;padding:30px;}
    .container{background:#fff;width:100%;max-width:1000px;border-radius:24px;padding:30px;box-shadow:0 30px 70px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#eafff3;padding:10px 18px;border-radius:12px;color:#27ae60;font-weight:500;}
    .back:hover{background:#27ae60;color:#fff;}
    form{background:#f4fdf7;padding:20px;border-radius:16px;margin-bottom:25px;display:grid;grid-template-columns:repeat(4,1fr) auto;gap:10px;align-items:end;}
    form select, form input{width:100%;padding:10px 12px;border-radius:10px;border:1px solid #ddd;}
    form button{background:linear-gradient(135deg,#2ecc71,#27ae60);color:#fff;border:none;border-radius:10px;padding:10px 20px;cursor:pointer;}
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:12px;text-align:left;font-size:14px;}
    th{background:#f4fdf7;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    .delete{color:#e74c3c;text-decoration:none;font-weight:600;}
    @media(max-width:900px){form{grid-template-columns:1fr 1fr;}}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>📅 Kelola Jadwal</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <form method="POST">
        <select name="class_id" required>
            <option value="">Pilih Kelas</option>
            <?php while($c=mysqli_fetch_assoc($classes)): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <select name="teacher_id" required>
            <option value="">Pilih Guru</option>
            <?php while($t=mysqli_fetch_assoc($teachers)): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nama_lengkap']) ?></option>
            <?php endwhile; ?>
        </select>
        <select name="subject_id" required>
            <option value="">Pilih Mapel</option>
            <?php while($s=mysqli_fetch_assoc($subjects)): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <select name="day_of_week" required>
            <option value="Monday">Senin</option>
            <option value="Tuesday">Selasa</option>
            <option value="Wednesday">Rabu</option>
            <option value="Thursday">Kamis</option>
            <option value="Friday">Jumat</option>
            <option value="Saturday">Sabtu</option>
        </select>
        <input type="time" name="start_time" required>
        <input type="time" name="end_time" required>
        <button type="submit">Tambah</button>
    </form>

    <table>
        <tr><th>No</th><th>Hari</th><th>Jam</th><th>Mata Pelajaran</th><th>Kelas</th><th>Guru</th><th>Aksi</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($schedules)):
            $days = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $days[$row['day_of_week']] ?? $row['day_of_week'] ?></td>
            <td><?= date('H:i',strtotime($row['start_time'])) ?>-<?= date('H:i',strtotime($row['end_time'])) ?></td>
            <td><?= htmlspecialchars($row['subject']) ?></td>
            <td><?= htmlspecialchars($row['class']) ?></td>
            <td><?= htmlspecialchars($row['guru']) ?></td>
            <td><a href="hapus_jadwal.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Hapus?')">Hapus</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>