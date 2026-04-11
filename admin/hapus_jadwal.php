<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

$id = (int)$_GET['id'];
mysqli_query($conn, "DELETE FROM schedules WHERE id = $id");
header("Location: data_jadwal.php");
exit;
?>