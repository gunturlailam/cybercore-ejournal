<?php

/**
 * Debug Database - Cek masalah login
 */

echo '<h1>🔍 DEBUG DATABASE</h1>';
echo '<hr>';

// 1. Koneksi database
echo '<h2>1. Cek Koneksi Database:</h2>';
$koneksi = new mysqli('localhost', 'root', '', 'cybercore_ejournal');

if ($koneksi->connect_error) {
    die('<span style="color:red">❌ ERROR KONEKSI: ' . $koneksi->connect_error . '</span>');
} else {
    echo '<span style="color:green">✅ Database terkoneksi</span><br>';
}

// 2. Cek tabel users
echo '<h2>2. Cek Tabel Users:</h2>';
$result = $koneksi->query("SELECT COUNT(*) as total FROM users");
if (!$result) {
    echo '<span style="color:red">❌ ERROR: ' . $koneksi->error . '</span><br>';
} else {
    $row = $result->fetch_assoc();
    echo '<span style="color:green">✅ Total user: ' . $row['total'] . '</span><br>';
}

// 3. Tampilkan semua user
echo '<h2>3. Daftar Lengkap User:</h2>';
$result = $koneksi->query("SELECT * FROM users");
if (!$result) {
    echo '<span style="color:red">❌ ERROR: ' . $koneksi->error . '</span><br>';
} else {
    echo '<table border="1" cellpadding="10" style="border-collapse:collapse;">';
    echo '<tr style="background:#333; color:white;">';
    echo '<th>ID</th><th>Nama</th><th>Username</th><th>Password Hash</th><th>Role</th><th>Active</th>';
    echo '</tr>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['nama'] . '</td>';
        echo '<td><strong>' . $row['username'] . '</strong></td>';
        echo '<td><code style="font-size:11px;">' . substr($row['password'], 0, 20) . '...</code></td>';
        echo '<td>' . $row['role'] . '</td>';
        echo '<td>' . ($row['is_active'] ? '✅ Yes' : '❌ No') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

// 4. Test query dengan prepared statement
echo '<h2>4. Test Query Prepared Statement:</h2>';
$username = 'admin';
$stmt = $koneksi->prepare("SELECT id, nama, username, password, role FROM users WHERE username = ? AND is_active = 1 LIMIT 1");

if (!$stmt) {
    echo '<span style="color:red">❌ PREPARE ERROR: ' . $koneksi->error . '</span><br>';
} else {
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo '<span style="color:green">✅ User "admin" ditemukan</span><br>';
        echo '<strong>Data User:</strong><br>';
        echo 'ID: ' . $user['id'] . '<br>';
        echo 'Nama: ' . $user['nama'] . '<br>';
        echo 'Username: ' . $user['username'] . '<br>';
        echo 'Password Hash: <code style="font-size:11px;">' . $user['password'] . '</code><br>';
        echo 'Role: ' . $user['role'] . '<br>';
        echo '<br>';

        // 5. Test password_verify
        echo '<h2>5. Test password_verify():</h2>';
        $password_input = 'password123';
        $password_hash = $user['password'];

        if (password_verify($password_input, $password_hash)) {
            echo '<span style="color:green">✅ Password COCOK!</span><br>';
            echo 'Password input: "<strong>' . $password_input . '</strong>" ✓<br>';
        } else {
            echo '<span style="color:red">❌ Password TIDAK COCOK</span><br>';
            echo 'Password input: "' . $password_input . '"<br>';
            echo 'Password hash: "' . $password_hash . '"<br>';
            echo '<br>';
            echo '<strong style="color:red">Mungkin password hash corrupt atau tidak di-hash dengan bcrypt!</strong><br>';
        }
    } else {
        echo '<span style="color:red">❌ User "admin" TIDAK DITEMUKAN</span><br>';
        echo 'Kemungkinan:<br>';
        echo '- Username tidak ada di database<br>';
        echo '- User tidak aktif (is_active = 0)<br>';
    }

    $stmt->close();
}

echo '<hr>';
echo '<h2>📋 Solusi:</h2>';
echo '<ul>';
echo '<li>Jika user tidak ada: Re-import database atau tambah user manual</li>';
echo '<li>Jika password tidak cocok: Update password dengan hash yang benar</li>';
echo '<li>Buka: <a href="reset-password.php">reset-password.php</a> untuk reset password admin</li>';
echo '</ul>';

$koneksi->close();
