<?php

/**
 * File Konfigurasi Global Aplikasi
 * Menyimpan variabel global dan konfigurasi keamanan
 */

// ===== INFORMASI SEKOLAH =====
define('NAMA_SEKOLAH', 'SMK Cyber Core Indonesia');
define('SINGKAT_SEKOLAH', 'SMK CCI');
define('ALAMAT_SEKOLAH', 'Jl. Cyber Core No. 123, Indonesia');
define('TELEPON_SEKOLAH', '(021) 1234-5678');
define('EMAIL_SEKOLAH', 'info@cybercore.id');
define('WEBSITE_SEKOLAH', 'https://www.cybercore.id');

// ===== INFORMASI APLIKASI =====
define('NAMA_APLIKASI', 'SMK Cyber Core Indonesia E-Journal');
define('VERSI_APLIKASI', '1.0.0');
define('DEVELOPER', 'Tim IT SMK Cyber Core Indonesia');

// ===== KONFIGURASI KEAMANAN =====
// Timezone
define('TIMEZONE', 'Asia/Jakarta');
date_default_timezone_set(TIMEZONE);

// Path aplikasi
define('BASE_PATH', dirname(dirname(__FILE__)) . '/');
define('APP_URL', 'http://localhost/cybercore-ejournal/');

// Durasi session (dalam detik)
define('SESSION_TIMEOUT', 3600); // 1 jam

// Hash password menggunakan bcrypt dengan cost factor 10
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_OPTIONS', ['cost' => 10]);

// ===== KONFIGURASI CORS (untuk API) =====
define('ALLOWED_ORIGINS', array(
    'http://localhost:3000',
    'http://localhost:8080',
    'https://localhost'
));

// ===== KONFIGURASI LOGGING =====
define('LOG_PATH', BASE_PATH . 'logs/');
define('LOG_FILE_ERROR', LOG_PATH . 'error.log');
define('LOG_FILE_ACTIVITY', LOG_PATH . 'activity.log');

// Buat folder logs jika belum ada
if (!is_dir(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}

// ===== KONFIGURASI ERROR HANDLING =====
// Tampilkan error hanya di development
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_FILE_ERROR);
}

// ===== KONFIGURASI UPLOAD FILE =====
define('UPLOAD_PATH', BASE_PATH . 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_FILE_TYPES', array('jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'));

// Buat folder uploads jika belum ada
if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

// ===== KONFIGURASI PAGINATION =====
define('ITEMS_PER_PAGE', 10);

// ===== ROLE DAN PERMISSION =====
define('ROLE_ADMIN', 'admin');
define('ROLE_GURU', 'guru');
define('ROLE_SISWA', 'siswa'); // untuk future implementation

// ===== FUNGSI HELPER KEAMANAN =====

/**
 * Sanitasi input dari user untuk mencegah XSS
 * @param string $input Input dari user
 * @return string Input yang sudah di-sanitasi
 */
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Hash password menggunakan bcrypt
 * @param string $password Password plain text
 * @return string Password yang sudah di-hash
 */
function hashPassword($password)
{
    return password_hash($password, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
}

/**
 * Verifikasi password dengan hash
 * @param string $password Password plain text
 * @param string $hash Password hash
 * @return bool True jika cocok, False jika tidak
 */
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * Generate CSRF token untuk form protection
 * @return string CSRF token
 */
function generateCSRFToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifikasi CSRF token
 * @param string $token Token dari form
 * @return bool True jika valid, False jika tidak
 */
function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Escape output untuk HTML
 * @param string $output Output yang akan ditampilkan
 * @return string Output yang sudah di-escape
 */
function escapeOutput($output)
{
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}

/**
 * Log aktivitas user
 * @param string $action Aksi yang dilakukan
 * @param string $details Detail tambahan
 */
function logActivity($action, $details = '')
{
    $timestamp = date('Y-m-d H:i:s');
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'GUEST';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

    $log_message = "[$timestamp] User ID: $user_id | IP: $ip_address | Action: $action | Details: $details\n";

    error_log($log_message, 3, LOG_FILE_ACTIVITY);
}

/**
 * Cek apakah user sudah login
 * @return bool True jika sudah login, False jika belum
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Cek role user
 * @param string $required_role Role yang diperlukan
 * @return bool True jika user memiliki role, False jika tidak
 */
function hasRole($required_role)
{
    return isset($_SESSION['role']) && $_SESSION['role'] === $required_role;
}

/**
 * Redirect ke halaman lain
 * @param string $url URL tujuan
 * @param int $status_code HTTP status code (default 302)
 */
function redirect($url, $status_code = 302)
{
    header('Location: ' . $url, true, $status_code);
    exit();
}

/**
 * Set flashmessage (pesan sekali pakai)
 * @param string $type Tipe pesan: success, error, warning, info
 * @param string $message Pesan yang akan ditampilkan
 */
function setFlashMessage($type, $message)
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Ambil flashmessage
 * @return array|null Array dengan type dan message, atau null jika tidak ada
 */
function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// ===== KONFIGURASI SESSION =====
// Session secure cookie settings (untuk HTTPS)
$session_options = [
    'lifetime' => SESSION_TIMEOUT,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
    'secure' => false, // Set ke true jika menggunakan HTTPS
    'httponly' => true, // Hanya bisa diakses dari HTTP/HTTPS
    'samesite' => 'Strict' // Proteksi dari CSRF
];

session_set_cookie_params($session_options);

// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID untuk mencegah session fixation
if (!isset($_SESSION['session_created'])) {
    session_regenerate_id(true);
    $_SESSION['session_created'] = time();
}

// Check session timeout
if (isset($_SESSION['session_created']) && (time() - $_SESSION['session_created']) > SESSION_TIMEOUT) {
    session_destroy();
    redirect(APP_URL . 'login.php?expired=true');
}
