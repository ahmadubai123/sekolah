<?php
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function isLoggedIn() {
    return isset($_SESSION['login']) && $_SESSION['login'] === true;
}

function isRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/auth/login.php");
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (!isRole($role)) {
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
}

function getUserId() {
    return $_SESSION['id'] ?? null;
}

function getUserName() {
    return $_SESSION['nama'] ?? null;
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function response($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function generateQRToken() {
    return bin2hex(random_bytes(16));
}

function generateScheduleQR($schedule_id, $expire_minutes = 30) {
    $token = generateQRToken();
    $expired = date('Y-m-d H:i:s', strtotime("+$expire_minutes minutes"));
    
    global $conn;
    mysqli_query($conn, "UPDATE schedules SET qr_token = '$token', qr_expired_at = '$expired' WHERE id = $schedule_id");
    
    return [
        'token' => $token,
        'expired' => $expired,
        'url' => (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['REQUEST_URI']) . "/siswa/scan.php?token=" . $token
    ];
}

function calculateRankingScore($feedback_avg, $attendance_rate, $academic_score) {
    return ($feedback_avg * 20) + ($attendance_rate * 0.3) + ($academic_score * 0.3);
}

function getCategoryFromScore($score) {
    if ($score >= 80) return 'Unggul';
    if ($score >= 60) return 'Baik';
    if ($score >= 40) return 'Cukup';
    return 'Perlu Perbaikan';
}

if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base = dirname($_SERVER['SCRIPT_NAME']);
    define('BASE_URL', $protocol . '://' . $host . ($base === '/' ? '' : $base));
}