<?php
include "../config/database.php";
include "../config/functions.php";
requireRole('admin');

$id = (int)$_GET['id'];
mysqli_query($conn, "DELETE FROM subjects WHERE id = $id");
header("Location: data_mapel.php");
exit;
?>