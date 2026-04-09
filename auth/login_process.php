<?php
include "../config/database.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($conn, "
    SELECT * FROM users 
    WHERE username='$username' 
    AND password='$password'
");

$data = mysqli_fetch_assoc($query);

if ($data) {
    $_SESSION['login'] = true;
    $_SESSION['id'] = $data['id'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['nama'] = $data['nama_lengkap'];

    if ($data['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../siswa/dashboard.php");
    }
} else {
    echo "<script>alert('Login gagal');history.back();</script>";
}
