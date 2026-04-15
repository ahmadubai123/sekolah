<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = sanitize($_POST['name']);
    $grade = (int)$_POST['grade'];
    $capacity = (int)$_POST['capacity'];
    $teacher_id = (int)$_POST['teacher_id'];
    
    mysqli_query($conn, "UPDATE classes SET name = '$name', grade = $grade, capacity = $capacity, teacher_id = $teacher_id WHERE id = $id");
    header("Location: data_kelas.php");
    exit;
}

$kelas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM classes WHERE id = $id"));
$teachers = mysqli_query($conn, "SELECT * FROM users WHERE role = 'guru' ORDER BY nama_lengkap");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kelas | MadrasahKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wgwt@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(120deg,#2ecc71,#27ae60);display:flex;justify-content:center;align-items:center;padding:30px;}
    .container{background:#fff;width:100%;max-width:500px;border-radius:24px;padding:30px;box-shadow:0 30px 70px rgba(0,0,0,.25);}
    h2{color:#2c3e50;margin-bottom:20px;}
    .back{text-decoration:none;background:#eafff3;padding:10px 18px;border-radius:12px;color:#27ae60;font-weight:500;display:inline-block;margin-bottom:20px;}
    form input, form select{width:100%;padding:12px 15px;margin-bottom:15px;border-radius:10px;border:1px solid #ddd;}
    form button{width:100%;padding:12px;border-radius:10px;border:none;background:linear-gradient(135deg,#2ecc71,#27ae60);color:#fff;cursor:pointer;}
    </style>
</head>
<body>

<a href="data_kelas.php" class="back">⬅ Kembali</a>

<div class="container">
    <h2>✏️ Edit Kelas</h2>
    
    <form method="POST">
        <input type="text" name="name" value="<?= htmlspecialchars($kelas['name']) ?>" required>
        <input type="number" name="grade" value="<?= $kelas['grade'] ?>" min="1" max="6" required>
        <input type="number" name="capacity" value="<?= $kelas['capacity'] ?>" required>
        <select name="teacher_id">
            <option value="">Pilih Wali Kelas</option>
            <?php while($t=mysqli_fetch_assoc($teachers)): ?>
            <option value="<?= $t['id'] ?>" <?= $kelas['teacher_id'] == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nama_lengkap']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Simpan Perubahan</button>
    </form>
</div>

</body>
</html>