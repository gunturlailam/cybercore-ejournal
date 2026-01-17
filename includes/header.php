<?php

/**
 * Header Template - SMK Cyber Core Indonesia E-Journal
 * Template modern dengan Tailwind CSS dan Glassmorphism effect
 */

// Pastikan sudah include config
if (!defined('NAMA_SEKOLAH')) {
    require_once dirname(__DIR__) . '/config/config.php';
}

// Get current page untuk active menu
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$user_name = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// Fungsi untuk cek active menu
function isActive($page)
{
    global $current_page;
    return $current_page === $page ? true : false;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? escapeOutput($page_title) . ' - ' : '';
            echo NAMA_APLIKASI; ?></title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Inter dari Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    'sans': ['Inter', 'sans-serif'],
                },
                extend: {
                    colors: {
                        'cyber': {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c3d66',
                        },
                    },
                    backdropBlur: {
                        'xs': '2px',
                    }
                },
            }
        }
    </script>

    <!-- Custom Styles -->
    <style>
        /* Glassmorphism effect */
        .glass {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(148, 163, 184, 0.1);
        }

        .glass-sm {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(148, 163, 184, 0.05);
        }

        /* Smooth scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(71, 85, 105, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(71, 85, 105, 0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(71, 85, 105, 0.5);
        }

        /* Smooth transition */
        * {
            transition-property: background-color, border-color, color;
            transition-duration: 200ms;
            transition-timing-function: ease-in-out;
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-100 font-sans">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="hidden md:flex fixed left-0 top-0 h-screen w-64 glass flex-col border-r border-slate-800 z-50">

            <!-- Header Sidebar -->
            <div class="p-6 border-b border-slate-800">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyber-400 to-cyber-600 flex items-center justify-center shadow-lg shadow-cyber-500/50">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-white">SMK CCI</h1>
                        <p class="text-xs text-slate-400">E-Journal 2026</p>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">

                <!-- Dashboard -->
                <a href="<?php echo APP_URL; ?>index.php" class="group flex items-center gap-3 px-4 py-3 rounded-lg <?php echo isActive('index') ? 'bg-gradient-to-r from-cyber-600 to-cyber-700 text-white shadow-lg shadow-cyber-500/30' : 'text-slate-300 hover:bg-slate-800'; ?> transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m2-2l6.3-1.89c.26-.1.54-.1.8 0L17 5m-10 6l.89-2.6c.1-.3.37-.5.66-.5h.28c.29 0 .56.2.66.5L11 18m-5 0v2a1 1 0 001 1h12a1 1 0 001-1v-2"></path>
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Isi Jurnal -->
                <a href="<?php echo APP_URL; ?>isi-jurnal.php" class="group flex items-center gap-3 px-4 py-3 rounded-lg <?php echo isActive('isi-jurnal') ? 'bg-gradient-to-r from-cyber-600 to-cyber-700 text-white shadow-lg shadow-cyber-500/30' : 'text-slate-300 hover:bg-slate-800'; ?> transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747C22 10.998 17.5 6.253 12 6.253z"></path>
                    </svg>
                    <span class="font-medium">Isi Jurnal</span>
                </a>

                <!-- Rekapitulasi -->
                <a href="<?php echo APP_URL; ?>rekap.php" class="group flex items-center gap-3 px-4 py-3 rounded-lg <?php echo isActive('rekap') ? 'bg-gradient-to-r from-cyber-600 to-cyber-700 text-white shadow-lg shadow-cyber-500/30' : 'text-slate-300 hover:bg-slate-800'; ?> transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="font-medium">Rekapitulasi</span>
                </a>

                <!-- Data Siswa -->
                <a href="<?php echo APP_URL; ?>data-siswa.php" class="group flex items-center gap-3 px-4 py-3 rounded-lg <?php echo isActive('data-siswa') ? 'bg-gradient-to-r from-cyber-600 to-cyber-700 text-white shadow-lg shadow-cyber-500/30' : 'text-slate-300 hover:bg-slate-800'; ?> transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span class="font-medium">Data Siswa</span>
                </a>

            </nav>

            <!-- User Info & Logout -->
            <div class="p-4 border-t border-slate-800">
                <div class="glass-sm rounded-lg p-3 mb-3">
                    <p class="text-xs text-slate-400">Logged in as</p>
                    <p class="text-sm font-semibold text-white truncate"><?php echo escapeOutput($user_name); ?></p>
                    <p class="text-xs text-cyber-400 capitalize"><?php echo escapeOutput($user_role); ?></p>
                </div>
                <a
                    href="javascript:void(0)"
                    onclick="confirmLogout()"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-red-900/20 hover:bg-red-900/30 text-red-400 hover:text-red-300 border border-red-800/30 font-medium text-sm transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </a>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="w-full md:ml-64">

            <!-- Top Navigation Bar (Mobile) -->
            <div class="md:hidden glass sticky top-0 border-b border-slate-800 z-40">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-cyber-400 to-cyber-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h1 class="text-sm font-bold text-white">SMK CCI</h1>
                    </div>
                    <button class="text-slate-300 hover:text-white" id="mobile-menu-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Flash Message (jika ada) -->
            <?php
            $flash_message = getFlashMessage();
            if ($flash_message):
            ?>
                <div class="p-4">
                    <div class="glass-sm rounded-lg p-4 border-l-4 <?php echo $flash_message['type'] === 'success' ? 'border-green-500' : ($flash_message['type'] === 'error' ? 'border-red-500' : 'border-yellow-500'); ?>">
                        <p class="text-sm <?php echo $flash_message['type'] === 'success' ? 'text-green-400' : ($flash_message['type'] === 'error' ? 'text-red-400' : 'text-yellow-400'); ?>">
                            <?php echo escapeOutput($flash_message['message']); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Content Wrapper -->
            <div class="p-6">