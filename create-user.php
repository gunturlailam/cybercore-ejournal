<?php

/**
 * Create User - Buat User Baru
 * Interface untuk menambah user ke database
 */

$koneksi = new mysqli('localhost', 'root', '', 'cybercore_ejournal');

if ($koneksi->connect_error) {
    die('❌ Database Error: ' . $koneksi->connect_error);
}

$koneksi->set_charset('utf8mb4');

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'guru');
    $email = trim($_POST['email'] ?? '');

    // Validasi
    $errors = [];

    if (empty($nama)) $errors[] = 'Nama harus diisi';
    if (empty($username)) $errors[] = 'Username harus diisi';
    if (empty($password)) $errors[] = 'Password harus diisi';
    if (strlen($username) < 3) $errors[] = 'Username minimal 3 karakter';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
    if (!in_array($role, ['admin', 'guru'])) $errors[] = 'Role tidak valid';

    // Cek username sudah ada
    if (empty($errors)) {
        $check = $koneksi->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param('s', $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = 'Username sudah terdaftar!';
        }
        $check->close();
    }

    if (!empty($errors)) {
        $message = implode('<br>', $errors);
    } else {
        // Hash password dan insert
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        $stmt = $koneksi->prepare("INSERT INTO users (nama, username, password, role, email, is_active) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->bind_param('sssss', $nama, $username, $password_hash, $role, $email);

        if ($stmt->execute()) {
            $success = true;
            $message = '✅ User berhasil dibuat!';
        } else {
            $message = '❌ Error: ' . $stmt->error;
        }

        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - SMK Cyber Core Indonesia</title>
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

    <div class="max-w-md mx-auto pt-8">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">➕ Buat User Baru</h1>
            <p class="text-slate-400">Tambah akun user ke sistem</p>
        </div>

        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="bg-green-500/10 border border-green-500/50 rounded-lg p-4 mb-6">
                <p class="text-green-400 font-semibold">✅ <?php echo $message; ?></p>
                <p class="text-green-300 text-sm mt-2">Silakan login dengan akun baru Anda</p>
                <a href="login.php" class="inline-block mt-3 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
                    Ke Halaman Login
                </a>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (!$success && $message): ?>
            <div class="bg-red-500/10 border border-red-500/50 rounded-lg p-4 mb-6">
                <p class="text-red-400 font-semibold">❌ Error</p>
                <p class="text-red-300 text-sm mt-2"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-xl p-6 space-y-4">

            <!-- Nama -->
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Nama Lengkap</label>
                <input
                    type="text"
                    name="nama"
                    placeholder="Contoh: Budi Santoso"
                    required
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 transition">
            </div>

            <!-- Username -->
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Username</label>
                <input
                    type="text"
                    name="username"
                    placeholder="Contoh: budi_guru"
                    required
                    minlength="3"
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 transition">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Minimal 6 karakter"
                    required
                    minlength="6"
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 transition">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Email (Opsional)</label>
                <input
                    type="email"
                    name="email"
                    placeholder="contoh@email.com"
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 transition">
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Role</label>
                <select
                    name="role"
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 rounded-lg text-white focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 transition">
                    <option value="guru">👨‍🏫 Guru</option>
                    <option value="admin">🔐 Admin</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-semibold rounded-lg transition-all duration-200 hover:shadow-lg hover:shadow-cyan-500/50">
                ✅ Buat User
            </button>

        </form>

        <!-- Quick Links -->
        <div class="mt-6 space-y-2">
            <a href="login.php" class="block text-center text-cyan-400 hover:text-cyan-300 text-sm">
                Kembali ke Login
            </a>
            <a href="list-users.php" class="block text-center text-slate-400 hover:text-slate-300 text-sm">
                Lihat Daftar User
            </a>
        </div>

    </div>

</body>

</html>
<?php $koneksi->close(); ?>