<?php

/**
 * Module: Proses Login
 * Autentikasi user dengan username dan password
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/koneksi.php';

try {
    // Ambil input dari form
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = sanitizeInput($_POST['password'] ?? '');
    $remember = isset($_POST['remember']) ? true : false;

    // Validasi input
    if (empty($username) || empty($password)) {
        setFlashMessage('error', 'Username dan password harus diisi');
        redirect(APP_URL . 'login.php');
    }

    // Query user dengan prepared statement
    $result = queryPrepared(
        "SELECT id, nama, username, password, role, is_active FROM users WHERE username = ? LIMIT 1",
        [$username],
        "s"
    );

    if ($result->num_rows == 0) {
        // Log failed attempt
        logActivity('LOGIN_FAILED', "Invalid username: $username");

        setFlashMessage('error', 'Username atau password salah');
        redirect(APP_URL . 'login.php');
    }

    $user = $result->fetch_assoc();

    // Cek apakah user aktif
    if ($user['is_active'] == 0) {
        logActivity('LOGIN_FAILED', "Inactive user: $username");

        setFlashMessage('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        redirect(APP_URL . 'login.php');
    }

    // Verifikasi password
    if (!verifyPassword($password, $user['password'])) {
        // Log failed attempt
        logActivity('LOGIN_FAILED', "Invalid password for user: $username");

        setFlashMessage('error', 'Username atau password salah');
        redirect(APP_URL . 'login.php');
    }

    // Password benar - set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['login_time'] = time();

    // Remember me - extend session timeout
    if ($remember) {
        $_SESSION['remember_me'] = true;
        // Set cookie untuk 30 hari
        setcookie('user_remember', base64_encode($user['id'] . '|' . $user['username']), time() + (30 * 24 * 60 * 60), '/');
    }

    // Log successful login
    logActivity('LOGIN_SUCCESS', "User ID: {$user['id']} ({$user['username']}) logged in successfully");

    // Redirect ke dashboard
    setFlashMessage('success', 'Selamat datang, ' . $user['nama'] . '!');
    redirect(APP_URL . 'index.php');
} catch (Exception $e) {
    error_log('Login Error: ' . $e->getMessage());
    setFlashMessage('error', 'Terjadi kesalahan. Silakan coba lagi.');
    redirect(APP_URL . 'login.php');
}
