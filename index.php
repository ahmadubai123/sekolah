<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Panggil koneksi & session
require_once __DIR__ . "/config/database.php";

// Kalau belum login → ke halaman login
if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

// Kalau sudah login → arahkan sesuai role
if ($_SESSION['role'] == 'admin') {
    header("Location: admin/dashboard.php");
    exit;
} elseif ($_SESSION['role'] == 'siswa') {
    header("Location: siswa/dashboard.php");
    exit;
}

// fallback kalau role aneh
session_destroy();
header("Location: auth/login.php");
exit;
