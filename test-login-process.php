<?php

/**
 * Test Login Process
 * Untuk debug proses login
 */

echo '<pre>';
echo "=== TEST LOGIN PROCESS ===\n\n";

// Test database connection
$koneksi = new mysqli('localhost', 'root', '', 'cybercore_ejournal');
if ($koneksi->connect_error) {
    die('Database Error: ' . $koneksi->connect_error);
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

echo "1. Input yang diterima:\n";
echo "   Username: $username\n";
echo "   Password: $password\n\n";

if (empty($username) || empty($password)) {
    die('ERROR: Username dan password harus diisi!');
}

// 2. Query user
echo "2. Query database untuk username '$username':\n";
$stmt = $koneksi->prepare("SELECT id, nama, username, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "   ❌ User TIDAK ditemukan\n";
    exit;
} else {
    echo "   ✅ User ditemukan\n";
}

$user = $result->fetch_assoc();
echo "   Nama: {$user['nama']}\n";
echo "   Role: {$user['role']}\n";
echo "   Password Hash: {$user['password']}\n\n";

// 3. Test password
echo "3. Verifikasi password:\n";
if (password_verify($password, $user['password'])) {
    echo "   ✅ PASSWORD BENAR!\n";
    echo "   Login seharusnya BERHASIL\n";
} else {
    echo "   ❌ PASSWORD SALAH\n";
    echo "   Coba dengan password lain\n";
}

echo "\n";

// 4. Start session test
echo "4. Test Session:\n";
session_start();
$_SESSION['user_id'] = $user['id'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

if (isset($_SESSION['user_id'])) {
    echo "   ✅ Session berhasil disimpan\n";
    echo "   Session ID: " . session_id() . "\n";
} else {
    echo "   ❌ Session GAGAL\n";
}

echo "\n</pre>";
