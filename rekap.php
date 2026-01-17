<?php
require_once 'config/config.php';
require_once 'config/koneksi.php';

if (!isLoggedIn()) {
    redirect(APP_URL . 'login.php');
}

$current_page = 'rekap';

// Get filter parameters
$filter_bulan = isset($_GET['bulan']) ? sanitizeInput($_GET['bulan']) : date('m');
$filter_tahun = isset($_GET['tahun']) ? sanitizeInput($_GET['tahun']) : date('Y');

// Determine user's access level
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Build base query
$query = "SELECT j.*, k.nama_kelas, u.nama FROM jurnal j
          LEFT JOIN kelas k ON j.id_kelas = k.id
          LEFT JOIN users u ON j.id_user = u.id
          WHERE 1=1";

// If guru, only show their own journals
if ($user_role === 'guru') {
    $query .= " AND j.id_user = ?";
}

// Add month and year filter
$query .= " AND MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?";
$query .= " ORDER BY j.tanggal DESC";

// Prepare and execute query
$stmt = $koneksi->prepare($query);
if ($user_role === 'guru') {
    $stmt->bind_param("iii", $user_id, $filter_bulan, $filter_tahun);
} else {
    $stmt->bind_param("ii", $filter_bulan, $filter_tahun);
}
$stmt->execute();
$result_jurnal = $stmt->get_result();

// Calculate statistics for selected month
$stat_query = "SELECT 
               COUNT(*) as total_jurnal,
               SUM(sakit) as total_sakit,
               SUM(izin) as total_izin,
               SUM(alfa) as total_alfa
               FROM jurnal
               WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?";

if ($user_role === 'guru') {
    $stat_query .= " AND id_user = ?";
}

$stat_stmt = $koneksi->prepare($stat_query);
if ($user_role === 'guru') {
    $stat_stmt->bind_param("iii", $filter_bulan, $filter_tahun, $user_id);
} else {
    $stat_stmt->bind_param("ii", $filter_bulan, $filter_tahun);
}
$stat_stmt->execute();
$stat_result = $stat_stmt->get_result();
$statistics = $stat_result->fetch_assoc();

// Get list of months and years for filters
$bulan_list = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Jurnal - SMK Cyber Core E-Journal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --cyber-50: #f0f9ff;
            --cyber-100: #e0f2fe;
            --cyber-200: #bae6fd;
            --cyber-300: #7dd3fc;
            --cyber-400: #38bdf8;
            --cyber-500: #0ea5e9;
            --cyber-600: #0284c7;
            --cyber-700: #0369a1;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            color: #f1f5f9;
        }

        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .cyber-gradient {
            background: linear-gradient(135deg, var(--cyber-600) 0%, var(--cyber-700) 100%);
        }

        .glow-effect {
            box-shadow: 0 0 20px rgba(2, 132, 199, 0.3);
        }

        table {
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <div class="flex">
        <?php include 'includes/header.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <div class="p-8 max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-white mb-2">Rekapitulasi Jurnal</h1>
                    <p class="text-slate-400">Ringkasan lengkap jurnal mengajar sesuai periode</p>
                </div>

                <!-- Filter Section -->
                <div class="glass rounded-lg border border-slate-800 p-6 mb-6">
                    <form method="GET" class="flex gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Bulan</label>
                            <select name="bulan" class="bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500">
                                <?php foreach ($bulan_list as $key => $value): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $filter_bulan === $key ? 'selected' : ''; ?>>
                                        <?php echo $value; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tahun</label>
                            <select name="tahun" class="bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500">
                                <?php
                                $tahun_sekarang = date('Y');
                                for ($i = $tahun_sekarang; $i >= $tahun_sekarang - 5; $i--):
                                ?>
                                    <option value="<?php echo $i; ?>" <?php echo $filter_tahun == $i ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-lg transition-colors font-medium">
                            Terapkan Filter
                        </button>
                    </form>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="glass rounded-lg border border-slate-800 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 text-sm">Total Jurnal</p>
                                <p class="text-2xl font-bold text-white mt-1"><?php echo $statistics['total_jurnal'] ?? 0; ?></p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center">
                                <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="glass rounded-lg border border-slate-800 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 text-sm">Total Sakit</p>
                                <p class="text-2xl font-bold text-orange-400 mt-1"><?php echo $statistics['total_sakit'] ?? 0; ?></p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-orange-500/20 flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="glass rounded-lg border border-slate-800 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 text-sm">Total Izin</p>
                                <p class="text-2xl font-bold text-yellow-400 mt-1"><?php echo $statistics['total_izin'] ?? 0; ?></p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="glass rounded-lg border border-slate-800 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 text-sm">Total Alfa</p>
                                <p class="text-2xl font-bold text-red-400 mt-1"><?php echo $statistics['total_alfa'] ?? 0; ?></p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-red-500/20 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m2-2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jurnal Table -->
                <div class="glass rounded-lg border border-slate-800 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-800 bg-slate-900/50">
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Guru</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Mata Pelajaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Materi</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Sakit</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Izin</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Alfa</th>
                                    <?php if ($user_role === 'admin'): ?>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <?php if ($result_jurnal->num_rows > 0):
                                    while ($jurnal = $result_jurnal->fetch_assoc()):
                                ?>
                                        <tr class="hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-white">
                                                <?php echo date('d/m/Y', strtotime($jurnal['tanggal'])); ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-300">
                                                <?php echo $jurnal['nama'] ?? '-'; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-300">
                                                <?php echo $jurnal['nama_kelas'] ?? '-'; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-300">
                                                <?php echo $jurnal['mata_pelajaran']; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-300 max-w-xs truncate">
                                                <?php echo substr($jurnal['materi'], 0, 50); ?>...
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <span class="bg-orange-500/20 text-orange-400 px-2 py-1 rounded text-xs font-medium">
                                                    <?php echo $jurnal['sakit']; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs font-medium">
                                                    <?php echo $jurnal['izin']; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs font-medium">
                                                    <?php echo $jurnal['alfa']; ?>
                                                </span>
                                            </td>
                                            <?php if ($user_role === 'admin'): ?>
                                                <td class="px-6 py-4 text-sm text-center">
                                                    <button onclick="editJurnal(<?php echo $jurnal['id']; ?>)" class="text-cyan-400 hover:text-cyan-300 transition-colors text-xs font-medium">
                                                        Edit
                                                    </button>
                                                    <span class="text-slate-600 mx-2">|</span>
                                                    <button onclick="deleteJurnal(<?php echo $jurnal['id']; ?>)" class="text-red-400 hover:text-red-300 transition-colors text-xs font-medium">
                                                        Hapus
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="<?php echo $user_role === 'admin' ? '9' : '8'; ?>" class="px-6 py-8 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p>Tidak ada jurnal untuk periode ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function editJurnal(id) {
            window.location.href = '<?php echo APP_URL; ?>isi-jurnal.php?edit=' + id;
        }

        function deleteJurnal(id) {
            Swal.fire({
                title: 'Hapus Jurnal',
                text: 'Apakah Anda yakin ingin menghapus jurnal ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('<?php echo APP_URL; ?>modules/hapus-jurnal.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                id: id
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Berhasil', 'Jurnal berhasil dihapus', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Gagal', data.message, 'error');
                            }
                        });
                }
            });
        }
    </script>
</body>

</html>