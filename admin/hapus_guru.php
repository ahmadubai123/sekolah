<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

$id = (int)$_GET['id'];
mysqli_query($conn, "DELETE FROM users WHERE id = $id AND role = 'guru'");
header("Location: data_guru.php");
exit;
?>