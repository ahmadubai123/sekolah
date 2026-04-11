<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('guru');

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Guru | HadirKu</title>
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
        max-width:500px;
        border-radius:24px;
        padding:30px;
        box-shadow:0 30px 70px rgba(0,0,0,.25);
        animation:fadeUp .8s ease;
    }
    @keyframes fadeUp{
        from{opacity:0;transform:translateY(40px)}
        to{opacity:1;transform:translateY(0)}
    }
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
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
    .profile-img{
        width:120px;
        height:120px;
        border-radius:50%;
        object-fit:cover;
        margin:0 auto 20px;
        display:block;
        border:4px solid #3498db;
    }
    form label{color:#2c3e50;font-weight:500;display:block;margin-bottom:8px;}
    form input, form textarea{
        width:100%;
        padding:12px 15px;
        margin-bottom:20px;
        border-radius:12px;
        border:1px solid #ddd;
    }
    form textarea{height:100px;resize:none;}
    form button{
        width:100%;
        padding:14px;
        border-radius:12px;
        border:none;
        background:linear-gradient(135deg,#3498db,#2980b9);
        color:#fff;
        font-size:15px;
        cursor:pointer;
    }
    form button:hover{opacity:0.9;}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>👤 Profil</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
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