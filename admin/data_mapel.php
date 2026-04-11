<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = sanitize($_POST['name']);
    $desc = sanitize($_POST['description']);
    mysqli_query($conn, "INSERT INTO subjects (name, description) VALUES ('$name', '$desc')");
    header("Location: data_mapel.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM subjects ORDER BY name");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Mata Pelajaran | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(120deg,#2ecc71,#27ae60);display:flex;justify-content:center;align-items:center;padding:30px;}
    .container{background:#fff;width:100%;max-width:700px;border-radius:24px;padding:30px;box-shadow:0 30px 70px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#eafff3;padding:10px 18px;border-radius:12px;color:#27ae60;font-weight:500;}
    .back:hover{background:#27ae60;color:#fff;}
    form{background:#f4fdf7;padding:20px;border-radius:16px;margin-bottom:25px;}
    form input, form textarea{width:100%;padding:12px 15px;margin-bottom:15px;border-radius:10px;border:1px solid #ddd;}
    form button{width:100%;padding:12px;border-radius:10px;border:none;background:linear-gradient(135deg,#2ecc71,#27ae60);color:#fff;cursor:pointer;}
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:12px;text-align:left;}
    th{background:#f4fdf7;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    .delete{color:#e74c3c;text-decoration:none;font-weight:600;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>📚 Kelola Mata Pelajaran</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <form method="POST">
        <input type="text" name="name" placeholder="Nama Mata Pelajaran" required>
        <textarea name="description" placeholder="Deskripsi (opsional)"></textarea>
        <button type="submit">Tambah Mata Pelajaran</button>
    </form>

    <table>
        <tr><th>No</th><th>Nama</th><th>Deskripsi</th><th>Aksi</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($query)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['description'] ?? '-') ?></td>
            <td><a href="hapus_mapel.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Hapus?')">Hapus</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>