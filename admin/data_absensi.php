<?php
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$query = mysqli_query($conn, "
    SELECT users.nama_lengkap, absensi.tanggal, absensi.jam
    FROM absensi
    JOIN users ON absensi.user_id = users.id
    ORDER BY absensi.tanggal DESC, absensi.jam DESC
");

$today = date('Y-m-d');
$total = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Absensi | HadirKu</title>

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
    max-width:1050px;
    padding:32px;
    border-radius:26px;
    box-shadow:0 35px 70px rgba(0,0,0,.35);
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
    margin-bottom:18px;
}

.header h2{
    font-size:26px;
    color:#2c3e50;
}

.back{
    text-decoration:none;
    background:#eafff3;
    padding:11px 18px;
    border-radius:14px;
    color:#27ae60;
    font-weight:500;
    transition:.3s;
}
.back:hover{
    background:linear-gradient(135deg,#2ecc71,#27ae60);
    color:#fff;
}

/* TOPBAR */
.topbar{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.search,.date{
    padding:11px 16px;
    border-radius:14px;
    border:1px solid #ddd;
    font-size:14px;
}

.badge{
    background:linear-gradient(135deg,#2ecc71,#27ae60);
    color:#fff;
    padding:8px 18px;
    border-radius:20px;
    font-size:13px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    border-radius:18px;
    overflow:hidden;
}

th,td{
    padding:15px;
    font-size:14px;
}

th{
    background:#f4fdf7;
    text-align:left;
    color:#2c3e50;
}

tr{
    transition:.25s;
}

tr:nth-child(even){
    background:#fafafa;
}

tr:hover{
    background:#eafff3;
    transform:scale(1.01);
}

.today{
    background:linear-gradient(135deg,#eafff3,#f4fdf7);
    font-weight:500;
}

.no{
    width:60px;
    text-align:center;
}

/* STATUS */
.status{
    padding:6px 16px;
    border-radius:14px;
    font-size:12px;
    color:#fff;
    display:inline-block;
}
.hadir{background:#22c55e;}
.lama{background:#94a3b8;}

@media(max-width:600px){
    .header{
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
    }
}
</style>
</head>

<body>

<div class="container">
    <div class="header">
        <h2>📊 Data Absensi Siswa</h2>
        <a href="dashboard.php" class="back">⬅ Dashboard</a>
    </div>

    <div class="topbar">
        <input type="text" class="search" placeholder="🔍 Cari siswa..." onkeyup="searchTable(this.value)">
        <input type="date" class="date" onchange="filterDate(this.value)">
        <span class="badge">📌 Total Absensi: <?= $total ?></span>
    </div>

    <table id="absenTable">
        <tr>
            <th class="no">No</th>
            <th>Nama Siswa</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Status</th>
        </tr>

        <?php $no=1; while ($row = mysqli_fetch_assoc($query)) {
            $isToday = $row['tanggal'] === $today;
        ?>
        <tr class="<?= $isToday ? 'today' : '' ?>">
            <td class="no"><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
            <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
            <td><?= date('H:i', strtotime($row['jam'])) ?> WIB</td>
            <td>
                <span class="status <?= $isToday ? 'hadir' : 'lama' ?>">
                    <?= $isToday ? 'Hadir Hari Ini' : 'Data Lama' ?>
                </span>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<script>
function searchTable(keyword){
    keyword = keyword.toLowerCase();
    document.querySelectorAll("#absenTable tr").forEach((row,i)=>{
        if(i===0) return;
        row.style.display = row.innerText.toLowerCase().includes(keyword) ? "" : "none";
    });
}

function filterDate(date){
    document.querySelectorAll("#absenTable tr").forEach((row,i)=>{
        if(i===0) return;
        row.style.display = row.children[2].innerText.includes(
            date.split('-').reverse().join(' ')
        ) ? "" : "none";
    });
}
</script>

</body>
</html>
