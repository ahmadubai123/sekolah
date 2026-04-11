<?php
$conn = mysqli_connect("localhost", "root", "", "absensi_siswa");

// Create table for guru attendance if not exists
$check = mysqli_query($conn, "SHOW TABLES LIKE 'absensi_guru'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "CREATE TABLE absensi_guru (
        id INT(11) NOT NULL AUTO_INCREMENT,
        teacher_id INT(11) NOT NULL,
        tanggal DATE NOT NULL,
        jam_masuk TIME,
        jam_pulang TIME,
        status ENUM('Hadir','Izin','Sakit','Alpha') DEFAULT 'Hadir',
        keterangan TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table absensi_guru created<br>";
} else {
    echo "Table absensi_guru already exists<br>";
}

echo "Selesai!";
?>