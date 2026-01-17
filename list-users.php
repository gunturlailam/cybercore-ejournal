<?php

/**
 * List Users - Daftar Semua User
 */

$koneksi = new mysqli('localhost', 'root', '', 'cybercore_ejournal');

if ($koneksi->connect_error) {
    die('❌ Database Error: ' . $koneksi->connect_error);
}

$result = $koneksi->query("SELECT id, nama, username, role, email, is_active, created_at FROM users ORDER BY created_at DESC");

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar User - SMK Cyber Core Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen p-4">

    <div class="max-w-4xl mx-auto pt-8">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">👥 Daftar User</h1>
                <p class="text-slate-400 mt-1">Total: <?php echo $result->num_rows; ?> user</p>
            </div>
            <a href="create-user.php" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                ➕ Buat User Baru
            </a>
        </div>

        <!-- Users Table -->
        <div class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-700">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-300">Nama</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-300">Username</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-300">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-300">Role</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-slate-300">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-slate-300">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($user = $result->fetch_assoc()) {
                            $role_badge = $user['role'] === 'admin' ? '🔐 Admin' : '👨‍🏫 Guru';
                            $status_badge = $user['is_active'] ? '<span class="text-green-400">✅ Aktif</span>' : '<span class="text-red-400">❌ Tidak Aktif</span>';
                            $tanggal = date('d M Y', strtotime($user['created_at']));
                    ?>
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="px-6 py-3 text-sm text-white font-medium"><?php echo htmlspecialchars($user['nama']); ?></td>
                                <td class="px-6 py-3 text-sm text-slate-300"><code class="bg-slate-900 px-2 py-1 rounded text-cyan-300"><?php echo htmlspecialchars($user['username']); ?></code></td>
                                <td class="px-6 py-3 text-sm text-slate-400"><?php echo htmlspecialchars($user['email'] ?: '-'); ?></td>
                                <td class="px-6 py-3 text-sm"><?php echo $role_badge; ?></td>
                                <td class="px-6 py-3 text-sm text-center"><?php echo $status_badge; ?></td>
                                <td class="px-6 py-3 text-sm text-slate-400"><?php echo $tanggal; ?></td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                Belum ada user. <a href="create-user.php" class="text-cyan-400 hover:text-cyan-300">Buat user pertama</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <a href="login.php" class="text-cyan-400 hover:text-cyan-300">
                ← Kembali ke Login
            </a>
        </div>

    </div>

</body>

</html>
<?php $koneksi->close(); ?>