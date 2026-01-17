<?php

/**
 * Logout - Keluar dari sistem
 */

require_once 'config/config.php';

// Log logout activity
if (isset($_SESSION['user_id'])) {
    logActivity('LOGOUT', "User logged out");
}

// Destroy session
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['user_remember'])) {
    setcookie('user_remember', '', time() - 3600, '/');
}

// Redirect ke login
setFlashMessage('success', 'Anda telah berhasil logout');
redirect(APP_URL . 'login.php');
