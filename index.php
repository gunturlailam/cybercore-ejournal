<?php

/**
 * Dashboard - SMK Cyber Core Indonesia E-Journal
 * Halaman utama dengan statistik dan jurnal terbaru
 */

require_once 'config/config.php';
require_once 'config/koneksi.php';

// Cek login
if (!isLoggedIn()) {
    redirect(APP_URL . 'login.php');
}

// Set page title
$page_title = 'Dashboard';

// Query statistik
// 1. Jurnal Hari Ini
$today = date('Y-m-d');
$result_jurnal_hari_ini = queryPrepared(
    "SELECT COUNT(*) as total FROM jurnal WHERE DATE(tanggal) = ? AND id_user = ?",
    [$today, $_SESSION['user_id']],
    "si"
);
$data_jurnal_hari_ini = $result_jurnal_hari_ini->fetch_assoc();
$jurnal_hari_ini = $data_jurnal_hari_ini['total'] ?? 0;

// 2. Total Kelas
$result_total_kelas = queryPrepared(
    "SELECT COUNT(*) as total FROM kelas",
    [],
    ""
);
$data_total_kelas = $result_total_kelas->fetch_assoc();
$total_kelas = $data_total_kelas['total'] ?? 0;

// 3. Total Siswa Absen Hari Ini
$result_siswa_absen = queryPrepared(
    "SELECT SUM(alfa) as total FROM jurnal WHERE DATE(tanggal) = ?",
    [$today],
    "s"
);
$data_siswa_absen = $result_siswa_absen->fetch_assoc();
$siswa_absen = $data_siswa_absen['total'] ?? 0;

// Query Jurnal Terbaru (limit 5)
$result_jurnal_terbaru = queryPrepared(
    "SELECT j.id, j.tanggal, j.mata_pelajaran, j.materi, j.jam_pelajaran, 
            j.sakit, j.izin, j.alfa, k.nama_kelas
     FROM jurnal j
     LEFT JOIN kelas k ON j.id_kelas = k.id
     WHERE j.id_user = ?
     ORDER BY j.tanggal DESC, j.jam_pelajaran DESC
     LIMIT 5",
    [$_SESSION['user_id']],
    "i"
);

require_once 'includes/header.php';
?>

<!-- Main Dashboard Content -->
<div class="space-y-6">

    <!-- Welcome Section -->
    <div>
        <h1 class="text-3xl font-bold text-white">Selamat Datang, <?php echo escapeOutput(explode(' ', $_SESSION['nama'])[0]); ?>! 👋</h1>
        <p class="text-slate-400 mt-1">Kelola jurnal mengajar Anda dengan mudah dan efisien</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Card 1: Jurnal Hari Ini -->
        <div class="glass rounded-lg p-6 border border-slate-800 hover:border-cyber-500/30 transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-400 text-sm font-medium">Jurnal Hari Ini</p>
                    <h3 class="text-4xl font-bold mt-2 bg-gradient-to-r from-teal-400 to-blue-500 bg-clip-text text-transparent">
                        <?php echo $jurnal_hari_ini; ?>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-teal-500/20 to-blue-500/20 flex items-center justify-center group-hover:from-teal-500/40 group-hover:to-blue-500/40 transition-all">
                    <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747C22 10.998 17.5 6.253 12 6.253z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-700">
                <a href="<?php echo APP_URL; ?>isi-jurnal.php" class="text-teal-400 hover:text-teal-300 text-sm font-medium flex items-center gap-1">
                    Isi Jurnal
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Card 2: Total Kelas -->
        <div class="glass rounded-lg p-6 border border-slate-800 hover:border-cyber-500/30 transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-400 text-sm font-medium">Total Kelas</p>
                    <h3 class="text-4xl font-bold mt-2 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                        <?php echo $total_kelas; ?>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center group-hover:from-blue-500/40 group-hover:to-purple-500/40 transition-all">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-700">
                <p class="text-slate-400 text-sm">Kelas aktif semester ini</p>
            </div>
        </div>

        <!-- Card 3: Siswa Absen Hari Ini -->
        <div class="glass rounded-lg p-6 border border-slate-800 hover:border-cyber-500/30 transition-all duration-300 group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-400 text-sm font-medium">Siswa Absen Hari Ini</p>
                    <h3 class="text-4xl font-bold mt-2 bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">
                        <?php echo $siswa_absen; ?>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-orange-500/20 to-red-500/20 flex items-center justify-center group-hover:from-orange-500/40 group-hover:to-red-500/40 transition-all">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-700">
                <p class="text-slate-400 text-sm">Tercatat dari semua kelas</p>
            </div>
        </div>

    </div>

    <!-- Jurnal Terbaru Section -->
    <div class="glass rounded-lg border border-slate-800 overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">Jurnal Terbaru</h2>
            <a href="<?php echo APP_URL; ?>rekap.php" class="text-cyber-400 hover:text-cyber-300 text-sm font-medium">
                Lihat Semua →
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-900/50">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Jam</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Presensi</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php
                    if ($result_jurnal_terbaru->num_rows > 0) {
                        while ($jurnal = $result_jurnal_terbaru->fetch_assoc()) {
                            $tanggal = date('d M Y', strtotime($jurnal['tanggal']));
                            $total_presensi = ($jurnal['sakit'] + $jurnal['izin'] + $jurnal['alfa']);
                    ?>
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-3 text-sm text-slate-300"><?php echo $tanggal; ?></td>
                                <td class="px-6 py-3 text-sm">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-cyber-500/20 text-cyber-300">
                                        <?php echo escapeOutput($jurnal['nama_kelas']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-300"><?php echo escapeOutput($jurnal['mata_pelajaran']); ?></td>
                                <td class="px-6 py-3 text-sm text-slate-400"><?php echo $jurnal['jam_pelajaran']; ?></td>
                                <td class="px-6 py-3 text-sm">
                                    <div class="flex items-center justify-center gap-2 text-xs">
                                        <?php if ($jurnal['sakit'] > 0): ?>
                                            <span class="px-2 py-1 rounded bg-yellow-500/20 text-yellow-300">S: <?php echo $jurnal['sakit']; ?></span>
                                        <?php endif; ?>
                                        <?php if ($jurnal['izin'] > 0): ?>
                                            <span class="px-2 py-1 rounded bg-blue-500/20 text-blue-300">I: <?php echo $jurnal['izin']; ?></span>
                                        <?php endif; ?>
                                        <?php if ($jurnal['alfa'] > 0): ?>
                                            <span class="px-2 py-1 rounded bg-red-500/20 text-red-300">A: <?php echo $jurnal['alfa']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="editJurnal(<?php echo $jurnal['id']; ?>)" class="text-cyber-400 hover:text-cyber-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="deleteJurnal(<?php echo $jurnal['id']; ?>)" class="text-red-400 hover:text-red-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p>Belum ada jurnal yang tercatat</p>
                                <a href="<?php echo APP_URL; ?>isi-jurnal.php" class="text-cyber-400 hover:text-cyber-300 text-sm mt-2 inline-block">Buat Jurnal Pertama</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- Script untuk fungsi aksi -->
<script>
    function editJurnal(id) {
        window.location.href = `<?php echo APP_URL; ?>isi-jurnal.php?edit=${id}`;
    }

    function deleteJurnal(id) {
        confirmAction('Hapus Jurnal', 'Apakah Anda yakin ingin menghapus jurnal ini?', function() {
            showLoading('Menghapus jurnal...');
            fetch(`<?php echo APP_URL; ?>modules/hapus-jurnal.php`, {
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
                        showAlert('Berhasil', 'Jurnal berhasil dihapus', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('Error', data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error', 'Terjadi kesalahan saat menghapus', 'error');
                });
        });
    }
</script>

<?php
require_once 'includes/footer.php';
?>