<?php
error_reporting(0);

$conn = mysqli_connect("localhost", "root", "", "absensi_siswa");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

session_start();

$username = trim($_POST['username']);
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    echo "<script>alert('Username dan password harus diisi');history.back();</script>";
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Username tidak ditemukan');history.back();</script>";
    exit;
}

// Check if password is hashed - check if it starts with $2y$ or $2a$
$is_hashed = (strpos($data['password'], '$2y$') === 0 || strpos($data['password'], '$2a$') === 0);

if ($is_hashed) {
    $valid = password_verify($password, $data['password']);
} else {
    // Plain text comparison
    $valid = ($password === $data['password']);
}

if ($valid) {
    $_SESSION['login'] = true;
    $_SESSION['id'] = $data['id'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['nama'] = $data['nama_lengkap'];
    
    if ($data['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } elseif ($data['role'] == 'guru') {
        header("Location: ../guru/dashboard.php");
    } else {
        header("Location: ../siswa/dashboard.php");
    }
    exit;
} else {
    echo "<script>alert('Password salah');history.back();</script>";
}
?>