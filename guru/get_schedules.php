<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('guru');

header('Content-Type: application/json');

$teacher_id = getUserId();
$day = $_GET['day'] ?? date('l');

$query = mysqli_query($conn, "
    SELECT s.id, sub.name as subject, c.name as class, s.start_time, s.end_time, s.day_of_week
    FROM schedules s
    JOIN subjects sub ON s.subject_id = sub.id
    JOIN classes c ON s.class_id = c.id
    WHERE s.teacher_id = $teacher_id AND s.day_of_week = '$day'
    ORDER BY s.start_time
");

$schedules = [];
while ($row = mysqli_fetch_assoc($query)) {
    $schedules[] = [
        'id' => $row['id'],
        'subject' => $row['subject'],
        'class' => $row['class'],
        'time' => date('H:i', strtotime($row['start_time'])) . ' - ' . date('H:i', strtotime($row['end_time']))
    ];
}

echo json_encode($schedules);