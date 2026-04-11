<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_lengkap'])) {
    $nama = sanitize($_POST['nama_lengkap']);
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    mysqli_query($conn, "INSERT INTO users (nama_lengkap, username, password, role) VALUES ('$nama', '$username', '$password', 'guru')");
    header("Location: data_guru.php");
    exit;
}

$query = mysqli_query($conn, "
    SELECT u.*, COUNT(s.id) as jumlah_jadwal
    FROM users u
    LEFT JOIN schedules s ON s.teacher_id = u.id
    WHERE u.role = 'guru'
    GROUP BY u.id
    ORDER BY u.nama_lengkap
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Guru | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(120deg,#2ecc71,#27ae60);display:flex;justify-content:center;align-items:center;padding:30px;}
    .container{background:#fff;width:100%;max-width:800px;border-radius:24px;padding:30px;box-shadow:0 30px 70px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#eafff3;padding:10px 18px;border-radius:12px;color:#27ae60;font-weight:500;}
    .back:hover{background:#27ae60;color:#fff;}
    form{background:#f4fdf7;padding:20px;border-radius:16px;margin-bottom:25px;}
    form input{width:100%;padding:12px 15px;margin-bottom:15px;border-radius:10px;border:1px solid #ddd;}
    form button{width:100%;padding:12px;border-radius:10px;border:none;background:linear-gradient(135deg,#2ecc71,#27ae60);color:#fff;cursor:pointer;}
    table{width:100%;border-collapse:collapse;border-radius:16px;overflow:hidden;}
    th,td{padding:12px;text-align:left;}
    th{background:#f4fdf7;color:#2c3e50;}
    tr:nth-child(even){background:#fafafa;}
    .delete{color:#e74c3c;text-decoration:none;font-weight:600;}
    .edit{color:#3498db;text-decoration:none;font-weight:600;margin-right:10px;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>👨‍🏫 Kelola Guru</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <form method="POST">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap Guru" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Tambah Guru</button>
    </form>

    <table>
        <tr><th>No</th><th>Nama</th><th>Username</th><th>Jadwal</th><th>Aksi</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($query)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $row['jumlah_jadwal'] ?> jadwal</td>
            <td>
                <a href="reset_password.php?id=<?= $row['id'] ?>" class="edit" onclick="return confirm('Reset password?')">Reset</a>
                <a href="hapus_guru.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Hapus guru?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>