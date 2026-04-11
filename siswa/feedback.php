<?php
include "../config/database.php";
include "../config/functions.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teacher_id'])) {
    $teacher_id = (int)$_POST['teacher_id'];
    $student_id = getUserId();
    $rating = (int)$_POST['rating'];
    $comment = sanitize($_POST['comment']);
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    
    mysqli_query($conn, "INSERT INTO feedback (teacher_id, student_id, rating, comment, anonymous) VALUES ($teacher_id, $student_id, $rating, '$comment', $anonymous)");
    echo "<script>alert('Terima kasih atas feedbacknya!');location.href='feedback.php';</script>";
}

$today = date('l');
$query = mysqli_query($conn, "
    SELECT DISTINCT s.id, s.teacher_id, u.nama_lengkap as guru, sub.name as mapel
    FROM schedules s
    JOIN users u ON s.teacher_id = u.id
    JOIN subjects sub ON s.subject_id = sub.id
    JOIN class_student cs ON cs.class_id = s.class_id
    WHERE cs.student_id = " . getUserId() . " AND s.day_of_week = '$today'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berikan Feedback | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;justify-content:center;align-items:center;padding:30px;}
    .container{background:#fff;width:100%;max-width:450px;padding:25px;border-radius:20px;box-shadow:0 25px 45px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#f1f1f1;padding:8px 14px;border-radius:10px;color:#333;}
    .teacher{background:#f4fdf7;padding:15px;border-radius:12px;margin-bottom:15px;}
    .teacher h4{color:#2c3e50;margin-bottom:5px;}
    .teacher p{color:#666;font-size:14px;}
    .rating{text-align:center;margin:20px 0;}
    .rating input{display:none;}
    .rating label{font-size:40px;cursor:pointer;color:#ddd;}
    .rating input:checked ~ label{color:#f1c40f;}
    .rating label:hover, .rating label:hover ~ label{color:#f1c40f;}
    form textarea{width:100%;height:80px;padding:12px;border-radius:10px;border:1px solid #ddd;margin-bottom:15px;}
    form button{width:100%;padding:12px;border-radius:10px;border:none;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;cursor:pointer;}
    .anon{display:flex;align-items:center;gap:10px;margin-bottom:15px;}
    .anon input{width:auto;}
    .empty{text-align:center;padding:40px;color:#999;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>⭐ Feedback Guru</h2>
        <a href="dashboard.php" class="back">⬅ Kembali</a>
    </div>

    <?php if (mysqli_num_rows($query) > 0): ?>
    <form method="POST">
        <label>Pilih Guru:</label>
        <select name="teacher_id" style="width:100%;padding:12px;border-radius:10px;border:1px solid #ddd;margin-bottom:15px;" required>
            <option value="">-- Pilih Guru --</option>
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
            <option value="<?= $row['teacher_id'] ?>"><?= htmlspecialchars($row['guru']) ?> - <?= htmlspecialchars($row['mapel']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Rating:</label>
        <div class="rating">
            <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" required>
            <label for="star<?= $i ?>">★</label>
            <?php endfor; ?>
        </div>

        <textarea name="comment" placeholder="Komentar (opsional)"></textarea>
        
        <div class="anon">
            <input type="checkbox" name="anonymous" id="anon" checked>
            <label for="anon">Kirim sebagai anonimus</label>
        </div>

        <button type="submit">Kirim Feedback</button>
    </form>
    <?php else: ?>
    <p class="empty">Tidak ada guru yang mengajar hari ini</p>
    <?php endif; ?>
</div>

</body>
</html>