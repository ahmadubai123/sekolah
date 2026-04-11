<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

$id = (int)$_GET['id'];
$new_password = password_hash('guru123', PASSWORD_BCRYPT);
mysqli_query($conn, "UPDATE users SET password = '$new_password' WHERE id = $id");
header("Location: data_guru.php");
exit;
?>