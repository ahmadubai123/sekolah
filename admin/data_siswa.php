<?php
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$data = mysqli_query($conn, "
    SELECT nama_lengkap, username 
    FROM users 
    WHERE role = 'siswa'
    ORDER BY nama_lengkap ASC
");

$total = mysqli_num_rows($data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Siswa | MadrasahKu</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    min-height:100vh;
    background:linear-gradient(120deg,#2ecc71,#27ae60,#1abc9c);
    background-size:300% 300%;
    animation:bgMove 10s ease infinite;
    display:flex;
    justify-content:center;
    align-items:center;
}

@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* CONTAINER */
.container{
    background:#fff;
    width:95%;
    max-width:900px;
    padding:30px;
    border-radius:24px;
    box-shadow:0 30px 70px rgba(0,0,0,.25);
    animation:fadeUp .8s ease;
}

@keyframes fadeUp{
    from{opacity:0;transform:translateY(40px)}
    to{opacity:1;transform:translateY(0)}
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.header h2{
    font-size:24px;
    color:#2c3e50;
}

.badge{
    background:linear-gradient(135deg,#2ecc71,#27ae60);
    color:#fff;
    padding:6px 14px;
    border-radius:20px;
    font-size:13px;
}

/* ACTION */
.actions{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.search{
    padding:11px 14px;
    border-radius:12px;
    border:1px solid #ddd;
    width:260px;
    font-size:14px;
}

.back{
    text-decoration:none;
    background:#eafff3;
    padding:10px 18px;
    border-radius:12px;
    color:#27ae60;
    font-size:14px;
    font-weight:500;
    transition:.3s;
}
.back:hover{
    background:linear-gradient(135deg,#2ecc71,#27ae60);
    color:#fff;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    overflow:hidden;
    border-radius:16px;
}

th,td{
    padding:14px;
    font-size:14px;
}

th{
    background:#f4fdf7;
    text-align:left;
    color:#2c3e50;
}

tr{
    transition:.2s;
}

tr:nth-child(even){
    background:#fafafa;
}

tr:hover{
    background:#eafff3;
    transform:scale(1.01);
}

.no{
    width:60px;
    text-align:center;
    font-weight:500;
}

@media(max-width:600px){
    .actions{
        flex-direction:column;
        gap:10px;
    }
    .search{
        width:100%;
    }
}
</style>
</head>

<body>

<div class="container">
    <div class="header">
        <h2>📋 Data Siswa</h2>
        <span class="badge">Total: <?= $total ?> Siswa</span>
    </div>

    <div class="actions">
        <input type="text" class="search" placeholder="🔍 Cari nama siswa..." onkeyup="searchTable(this.value)">
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <table id="tableSiswa">
        <tr>
            <th class="no">No</th>
            <th>Nama Lengkap</th>
            <th>Username</th>
        </tr>

        <?php $no=1; while ($row = mysqli_fetch_assoc($data)) { ?>
        <tr>
            <td class="no"><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
            <td><?= htmlspecialchars($row['username']); ?></td>
        </tr>
        <?php } ?>
    </table>
</div>

<script>
function searchTable(keyword){
    let rows = document.querySelectorAll("#tableSiswa tr");
    keyword = keyword.toLowerCase();

    for(let i=1;i<rows.length;i++){
        let text = rows[i].innerText.toLowerCase();
        rows[i].style.display = text.includes(keyword) ? "" : "none";
    }
}
</script>

</body>
</html>
