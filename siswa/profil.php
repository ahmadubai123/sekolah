<?php
include "../config/database.php";
include "../config/functions.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = getUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama_lengkap']);
    $bio = sanitize($_POST['bio']);
    
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
        $target = dirname(__DIR__) . '/assets/img/profiles/' . $filename;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
            mysqli_query($conn, "UPDATE users SET profile_pic = '$filename' WHERE id = $user_id");
        }
    }
    
    mysqli_query($conn, "UPDATE users SET nama_lengkap = '$nama', bio = '$bio' WHERE id = $user_id");
    $_SESSION['nama'] = $nama;
    echo "<script>alert('Profil berhasil diperbarui');</script>";
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($query);

$absensi_hari_ini = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi WHERE user_id = $user_id AND tanggal = CURDATE()"));
$total_absensi = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM absensi WHERE user_id = $user_id"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Siswa | HadirKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;justify-content:center;align-items:center;padding:30px;}
    .container{background:#fff;width:100%;max-width:500px;padding:25px;border-radius:20px;box-shadow:0 25px 45px rgba(0,0,0,.25);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
    .header h2{color:#2c3e50;}
    .back{text-decoration:none;background:#f1f1f1;padding:8px 14px;border-radius:10px;color:#333;}
    .profile-img{width:120px;height:120px;border-radius:50%;object-fit:cover;margin:0 auto 20px;display:block;border:4px solid #667eea;}
    .stats{display:flex;gap:15px;margin-bottom:20px;}
    .stat{background:#f4fdf7;flex:1;padding:15px;border-radius:12px;text-align:center;}
    .stat h3{color:#667eea;font-size:24px;}
    .stat p{color:#666;font-size:12px;}
    form label{color:#2c3e50;font-weight:500;display:block;margin-bottom:8px;}
    form input, form textarea{width:100%;padding:12px 15px;margin-bottom:20px;border-radius:12px;border:1px solid #ddd;}
    form textarea{height:80px;resize:none;}
    form button{width:100%;padding:14px;border-radius:12px;border:none;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;cursor:pointer;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>👤 Profil</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <div class="stats">
        <div class="stat">
            <h3><?= $absensi_hari_ini ?></h3>
            <p>Hari Ini</p>
        </div>
        <div class="stat">
            <h3><?= $total_absensi ?></h3>
            <p>Total Absensi</p>
        </div>
    </div>

    <?php
    $img_path = !empty($user['profile_pic']) ? '../assets/img/profiles/' . $user['profile_pic'] : '../assets/img/default-avatar.png';
    ?>
    <img src="<?= $img_path ?>" class="profile-img" alt="Foto Profil">

    <form method="POST" enctype="multipart/form-data">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>

        <label>Username</label>
        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled>

        <label>Bio</label>
        <textarea name="bio" placeholder="Tulis bio singkat..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>

        <label>Foto Profil</label>
        <input type="file" name="profile_pic" accept="image/*">

        <button type="submit">Simpan Perubahan</button>
    </form>
</div>

</body>
</html>