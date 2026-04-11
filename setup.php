<?php
// Script untuk memperbaiki dan setup database

$conn = mysqli_connect("localhost", "root", "", "absensi_siswa");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

echo "<h2>Setup Awal Aplikasi HadirKu</h2>";

// 1. Update role menjadi termasuk 'guru'
echo "<h3>1. Update Role Table...</h3>";
$alter = "ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru','siswa') NOT NULL";
if (mysqli_query($conn, $alter)) {
    echo "✓ Role column updated<br>";
} else {
    echo "✗ Error: " . mysqli_error($conn) . "<br>";
}

// 2. Insert guru baru untuk testing
echo "<h3>2. Check Guru Account...</h3>";
$check = mysqli_query($conn, "SELECT id FROM users WHERE role = 'guru'");
if (mysqli_num_rows($check) == 0) {
    $pass_hash = password_hash('guru123', PASSWORD_BCRYPT);
    mysqli_query($conn, "INSERT INTO users (nama_lengkap, username, password, role) VALUES ('Budi Santoso', 'budi.guru', '$pass_hash', 'guru')");
    echo "✓ Guru account created (budi.guru / guru123)<br>";
} else {
    echo "✓ Guru account already exists<br>";
}

// 3. Check data
echo "<h3>3. User Accounts...</h3>";
$users = mysqli_query($conn, "SELECT id, nama_lengkap, username, role FROM users LIMIT 10");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Nama</th><th>Username</th><th>Role</th><th>Password Status</th></tr>";
while ($row = mysqli_fetch_assoc($users)) {
    $pass_status = (password_get_info($row['password'])['algo'] !== 0) ? 'Hashed' : 'Plain';
    echo "<tr><td>{$row['id']}</td><td>{$row['nama_lengkap']}</td><td>{$row['username']}</td><td>{$row['role']}</td><td>{$pass_status}</td></tr>";
}
echo "</table>";

echo "<h3>4. Migration Check...</h3>";

// Create tables if not exist
$tables = ['subjects', 'classes', 'class_student', 'schedules', 'performance', 'feedback', 'trends', 'notifications', 'grades'];

foreach ($tables as $table) {
    $check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($check) == 0) {
        echo "✗ Table $table not found<br>";
    } else {
        echo "✓ Table $table exists<br>";
    }
}

echo "<h2>Selesai! Silakan Login dengan:</h2>";
echo "<ul>";
echo "<li><b>Admin:</b> username 'ahmad ubaidillah' atau 'admin', password 'admin123'</li>";
echo "<li><b>Guru:</b> username 'budi.guru', password 'guru123'</li>";
echo "<li><b>Siswa:</b> username 'siswa1', password '123'</li>";
echo "</ul>";
?>