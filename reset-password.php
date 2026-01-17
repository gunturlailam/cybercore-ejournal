<?php

/**
 * Reset Password Admin
 * Untuk reset password jika lupa atau ada masalah hash
 */

echo '<h1>🔑 Reset Password Admin</h1>';
echo '<hr>';

// Koneksi database
$koneksi = new mysqli('localhost', 'root', '', 'cybercore_ejournal');

if ($koneksi->connect_error) {
    die('❌ Database Error: ' . $koneksi->connect_error);
}

$koneksi->set_charset('utf8mb4');

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validasi
    if (empty($username) || empty($new_password) || empty($confirm_password)) {
        $message = '❌ Semua field harus diisi';
    } elseif ($new_password !== $confirm_password) {
        $message = '❌ Password tidak cocok';
    } elseif (strlen($new_password) < 6) {
        $message = '❌ Password minimal 6 karakter';
    } else {
        // Hash password
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 10]);

        // Update password
        $stmt = $koneksi->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param('ss', $password_hash, $username);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $message = '✅ Password berhasil direset!';
            $success = true;
            echo '<div style="background:#d4edda; border:1px solid #c3e6cb; padding:15px; border-radius:5px; margin-bottom:20px;">';
            echo '<strong style="color:green;">Berhasil!</strong><br>';
            echo 'Username: <strong>' . htmlspecialchars($username) . '</strong><br>';
            echo 'Password baru: <strong>' . htmlspecialchars($new_password) . '</strong><br>';
            echo '<br>';
            echo '<a href="login.php" style="background:green; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;">Kembali ke Login</a>';
            echo '</div>';
        } else {
            $message = '❌ Username tidak ditemukan atau tidak ada perubahan';
        }

        $stmt->close();
    }
}

if (!$success) {
?>

    <div style="background:#f8d7da; border:1px solid #f5c6cb; padding:15px; border-radius:5px; margin-bottom:20px;">
        <strong style="color:#721c24;">Ayo Reset Password</strong><br>
        Masukkan username dan password baru:
    </div>

    <?php if ($message): ?>
        <div style="background:#f8d7da; border:1px solid #f5c6cb; padding:15px; border-radius:5px; margin-bottom:20px;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" style="max-width:400px; background:#f5f5f5; padding:20px; border-radius:5px;">

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">Username:</label>
            <input type="text" name="username" placeholder="admin" value="admin" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">Password Baru:</label>
            <input type="password" name="new_password" placeholder="Minimal 6 karakter" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">Konfirmasi Password:</label>
            <input type="password" name="confirm_password" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; box-sizing:border-box;">
        </div>

        <button type="submit" style="width:100%; padding:12px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold; font-size:16px;">
            Reset Password
        </button>
    </form>

<?php
}
?>

<hr style="margin-top:30px;">

<h2>📝 Akun Demo Default:</h2>
<ul>
    <li><strong>Username:</strong> admin</li>
    <li><strong>Password:</strong> password123</li>
</ul>

<?php
$koneksi->close();
?>