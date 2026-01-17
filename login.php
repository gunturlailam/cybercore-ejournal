<?php

/**
 * Login Page - SMK Cyber Core Indonesia E-Journal
 * Halaman login dengan design Dark Mode Luxury & Glassmorphism
 * Perbaikan: Gunakan mysqli prepare, password_verify, dan session management yang benar
 */

// Start session
session_start();

// Koneksi database
$koneksi = new mysqli('localhost', 'root', '', 'cybercore_ejournal');

if ($koneksi->connect_error) {
    die('Database Error: ' . $koneksi->connect_error);
}

$koneksi->set_charset('utf8mb4');

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Handle login process
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi input
    if (empty($username) || empty($password)) {
        $error_message = 'Username dan password harus diisi';
    } else {
        // Query user dengan mysqli prepare (aman dari SQL injection)
        $stmt = $koneksi->prepare("SELECT id, nama, username, password, role FROM users WHERE username = ? AND is_active = 1 LIMIT 1");

        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verifikasi password dengan password_verify
                if (password_verify($password, $user['password'])) {
                    // Password benar - set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['login_time'] = time();

                    $success_message = 'Login berhasil! Mengalihkan...';

                    // Redirect ke dashboard
                    header('Refresh: 0.5; url=index.php');
                    exit();
                } else {
                    // Password salah
                    $error_message = 'Username atau password salah';
                }
            } else {
                // User tidak ditemukan
                $error_message = 'Username atau password salah';
            }

            $stmt->close();
        } else {
            $error_message = 'Terjadi kesalahan database: ' . $koneksi->error;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMK Cyber Core Indonesia - Login E-Journal</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Inter dari Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- SweetAlert2 untuk notifikasi -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Glassmorphism effect */
        .glass {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(148, 163, 184, 0.15);
        }

        /* Animated gradient background */
        .gradient-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            z-index: -1;
        }

        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            z-index: -1;
        }

        .blob-1 {
            width: 500px;
            height: 500px;
            background: #0ea5e9;
            top: -200px;
            left: -200px;
            animation: float 8s ease-in-out infinite;
        }

        .blob-2 {
            width: 400px;
            height: 400px;
            background: #06b6d4;
            bottom: -100px;
            right: -100px;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(30px);
            }
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #94a3b8;
            pointer-events: none;
        }

        input:focus~.input-icon,
        input:not(:placeholder-shown)~.input-icon {
            color: #0ea5e9;
        }

        input {
            padding-left: 44px;
        }

        .btn-login {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(14, 165, 233, 0.4);
        }

        .btn-login:active {
            transform: translateY(0px);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .logo-box {
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>

<body>
    <!-- Animated Background -->
    <div class="gradient-bg"></div>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <!-- Main Container -->
    <div class="relative z-10 flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md">

            <!-- Logo Section -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-600 shadow-2xl shadow-cyan-500/50 mb-6 logo-box">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">Selamat Datang</h1>
                <p class="text-slate-400 text-lg">SMK Cyber Core Indonesia</p>
                <p class="text-slate-500 text-sm mt-1">E-Journal Management System</p>
            </div>

            <!-- Login Card -->
            <div class="glass rounded-3xl p-8 space-y-6 border border-slate-700/50 shadow-2xl mb-6">

                <!-- Error Message Alert -->
                <?php if ($error_message): ?>
                    <div class="bg-red-500/10 border border-red-500/50 rounded-xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-red-400 text-sm font-medium">Login Gagal</p>
                            <p class="text-red-300 text-xs mt-1"><?php echo htmlspecialchars($error_message); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="" class="space-y-5">

                    <!-- Username Input -->
                    <div class="relative">
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Username"
                            required
                            autocomplete="username"
                            class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 transition-all duration-200">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>

                    <!-- Password Input -->
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Password"
                            required
                            autocomplete="current-password"
                            class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 transition-all duration-200">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="w-4 h-4 rounded border-slate-600 bg-slate-900 text-cyan-500 focus:ring-2 focus:ring-cyan-500 cursor-pointer">
                        <label for="remember" class="ml-2 text-sm text-slate-400 cursor-pointer hover:text-slate-300 transition-colors">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button
                        type="submit"
                        class="btn-login w-full py-3 px-4 text-white font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg mt-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h6a3 3 0 013 3v1"></path>
                        </svg>
                        Masuk ke Dashboard
                    </button>

                </form>

                <!-- Divider -->
                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-700"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-3 bg-slate-900/50 text-slate-400 text-xs uppercase tracking-wider">Demo Login</span>
                    </div>
                </div>

                <!-- Demo Credentials -->
                <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50 space-y-2">
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Akun Demo:</p>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-slate-900/50 rounded px-3 py-2">
                            <p class="text-slate-500">Username</p>
                            <p class="text-cyan-400 font-mono font-bold mt-1">admin</p>
                        </div>
                        <div class="bg-slate-900/50 rounded px-3 py-2">
                            <p class="text-slate-500">Password</p>
                            <p class="text-cyan-400 font-mono font-bold mt-1">password123</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="text-center space-y-2">
                <p class="text-slate-500 text-xs">
                    © 2026 SMK Cyber Core Indonesia. All rights reserved.
                </p>
                <p class="text-slate-600 text-xs">
                    E-Journal Management System v1.0.0
                </p>
            </div>

        </div>
    </div>

    <script>
        // Auto dismiss error setelah 5 detik
        const errorAlert = document.querySelector('[class*="bg-red-500"]');
        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.transition = 'all 0.3s ease';
                errorAlert.style.opacity = '0';
                errorAlert.style.transform = 'translateY(-10px)';
                setTimeout(() => errorAlert.remove(), 300);
            }, 5000);
        }

        // Form submit handler
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            button.disabled = true;
            button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Memproses...';
        });

        // Initialize Lucide Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>

</body>

</html>