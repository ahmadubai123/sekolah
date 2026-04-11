<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = sanitize($_POST['name']);
    $grade = (int)$_POST['grade'];
    $capacity = (int)$_POST['capacity'];
    mysqli_query($conn, "INSERT INTO classes (name, grade, capacity) VALUES ('$name', $grade, $capacity)");
    header("Location: data_kelas.php");
    exit;
}

$query = mysqli_query($conn, "SELECT c.*, u.nama_lengkap as guru_name FROM classes c LEFT JOIN users u ON c.teacher_id = u.id ORDER BY c.grade, c.name");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kelas | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(120deg,#2ecc71,#27ae60);display:flex;justify-content:center;align-items:center;padding:30px;}
    .container{background:#fff;width:100%;max-width:800px;border-radius:24px;padding:30px;box-shadow:0 30px 70px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#eafff3;padding:10px 18px;border-radius:12px;color:#27ae60;font-weight:500;}
    .back:hover{background:#27ae60;color:#fff;}
    form{background:#f4fdf7;padding:20px;border-radius:16px;margin-bottom:25px;display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:10px;}
    form input, form select{width:100%;padding:10px 12px;border-radius:10px;border:1px solid #ddd;}
    form button{background:linear-gradient(135deg,#2ecc71,#27ae60);color:#fff;border:none;border-radius:10px;padding:10px 20px;cursor:pointer;}
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:12px;text-align:left;}
    th{background:#f4fdf7;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    .edit{color:#3498db;text-decoration:none;font-weight:600;margin-right:10px;}
    .delete{color:#e74c3c;text-decoration:none;font-weight:600;}
    @media(max-width:600px){form{grid-template-columns:1fr;}}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>🏫 Kelola Kelas</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <form method="POST">
        <input type="text" name="name" placeholder="Nama Kelas (misal: Kelas 1A)" required>
        <input type="number" name="grade" placeholder="Tingkat (1-6)" min="1" max="6" required>
        <input type="number" name="capacity" placeholder="Kapasitas" value="30">
        <button type="submit">Tambah</button>
    </form>

    <table>
        <tr><th>No</th><th>Nama Kelas</th><th>Tingkat</th><th>Kapasitas</th><th>Wali Kelas</th><th>Aksi</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($query)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td>Kelas <?= $row['grade'] ?></td>
            <td><?= $row['capacity'] ?></td>
            <td><?= $row['guru_name'] ?? 'Belum ada' ?></td>
            <td>
                <a href="edit_kelas.php?id=<?= $row['id'] ?>" class="edit">Edit</a>
                <a href="hapus_kelas.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>