<?php
// Fix admin password and reset all

$conn = mysqli_connect("localhost", "root", "", "absensi_siswa");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

echo "<h2>Reset Password - Fix Login Issue</h2>";

// 1. Fix admin password - set to plain text for testing
$pass_plain = 'admin123';
mysqli_query($conn, "UPDATE users SET password = '$pass_plain' WHERE id = 1");
echo "✓ Admin password reset to 'admin123' (plain text)<br>";

// 2. Fix student passwords
$siswa = mysqli_query($conn, "UPDATE users SET password = '123' WHERE role = 'siswa'");
echo "✓ Semua student password di-reset ke '123'<br>";

// 3. Verify
echo "<h3>Akun yang bisa digunakan:</h3>";
$users = mysqli_query($conn, "SELECT id, nama_lengkap, username, role FROM users WHERE role IN ('admin','siswa') LIMIT 5");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Nama</th><th>Username</th><th>Role</th><th>Password</th></tr>";
while ($row = mysqli_fetch_assoc($users)) {
    echo "<tr><td>{$row['nama_lengkap']}</td><td>{$row['username']}</td><td>{$row['role']}</td><td>123 / admin123</td></tr>";
}
echo "</table>";

echo "<h2>Silakan coba login dengan:</h2>";
echo "<ul>";
echo "<li><b>Admin:</b> username = 'ahmad ubaidillah', password = 'admin123'</li>";
echo "<li><b>Siswa:</b> username = 'siswa1', password = '123'</li>";
echo "</ul>";
?>