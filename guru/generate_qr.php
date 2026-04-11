<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('guru');

header('Content-Type: application/json');

$schedule_id = (int)$_GET['id'];

$query = mysqli_query($conn, "SELECT qr_token, qr_expired_at FROM schedules WHERE id = $schedule_id AND teacher_id = " . getUserId());
$data = mysqli_fetch_assoc($query);

if (!$data || !$data['qr_token'] || strtotime($data['qr_expired_at']) < time()) {
    $token = generateQRToken();
    $expired = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    mysqli_query($conn, "UPDATE schedules SET qr_token = '$token', qr_expired_at = '$expired' WHERE id = $schedule_id");
    $qr_url = BASE_URL . "/siswa/scan.php?token=$token";
} else {
    $qr_url = BASE_URL . "/siswa/scan.php?token=" . $data['qr_token'];
}

echo json_encode(['url' => $qr_url]);