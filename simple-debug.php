<?php

/**
 * Simple Debug - Cek Status Login
 */

// Langsung cek database tanpa include config (untuk hindari error)
$koneksi = new mysqli('localhost', 'root', '', 'cybercore_ejournal');

if ($koneksi->connect_error) {
    die('ERROR: Database tidak terkoneksi - ' . $koneksi->connect_error);
}

echo '<h1>🔍 Debug Login</h1>';
echo '<hr>';

// 1. Cek tabel users
echo '<h3>1. Cek Tabel Users:</h3>';
$result = $koneksi->query("SELECT COUNT(*) as total FROM users");
$row = $result->fetch_assoc();
echo 'Total user: <strong>' . $row['total'] . '</strong><br>';

if ($row['total'] == 0) {
    echo '<span style="color:red">❌ TIDAK ADA USER - Database belum ter-import!</span><br>';
    echo 'Buka: <a href="import_database.php">import_database.php</a>';
    exit;
} else {
    echo '<span style="color:green">✅ Ada user di database</span><br>';
}

echo '<br>';

// 2. Tampilkan semua user
echo '<h3>2. Daftar User:</h3>';
$result = $koneksi->query("SELECT id, nama, username, role FROM users");
echo '<table border="1" cellpadding="10">';
echo '<tr><th>ID</th><th>Nama</th><th>Username</th><th>Role</th></tr>';
while ($row = $result->fetch_assoc()) {
    echo '<tr><td>' . $row['id'] . '</td><td>' . $row['nama'] . '</td><td>' . $row['username'] . '</td><td>' . $row['role'] . '</td></tr>';
}
echo '</table>';

echo '<br><hr>';

// 3. Test form submission
echo '<h3>3. Test Login Form:</h3>';
echo '<form method="POST" action="test-login-process.php" style="border:1px solid #ccc; padding:20px; max-width:300px;">';
echo 'Username: <input type="text" name="username" value="admin" style="width:100%; padding:5px; margin-bottom:10px;"><br>';
echo 'Password: <input type="password" name="password" value="password123" style="width:100%; padding:5px; margin-bottom:10px;"><br>';
echo '<button type="submit" style="padding:10px 20px; background:blue; color:white; border:none; cursor:pointer;">Test Login</button>';
echo '</form>';

echo '<br><hr>';

// 4. Info
echo '<h3>📝 Info:</h3>';
echo '<ul>';
echo '<li>Jika sudah ada user di atas, coba test form di atas</li>';
echo '<li>Jika tidak ada user, klik link import_database.php</li>';
echo '<li>Username: <strong>admin</strong></li>';
echo '<li>Password: <strong>password123</strong></li>';
echo '</ul>';
