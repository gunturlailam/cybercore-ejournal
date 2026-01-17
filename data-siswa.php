<?php
require_once 'config/config.php';
require_once 'config/koneksi.php';

if (!isLoggedIn()) {
    redirect(APP_URL . 'login.php');
}

$current_page = 'data-siswa';

// Get kelas filter
$filter_kelas = isset($_GET['kelas']) ? sanitizeInput($_GET['kelas']) : '';

// Build query
$query = "SELECT s.*, k.nama_kelas FROM siswa s
          LEFT JOIN kelas k ON s.id_kelas = k.id
          WHERE 1=1";

if (!empty($filter_kelas)) {
    $query .= " AND s.id_kelas = ?";
}

$query .= " ORDER BY k.nama_kelas ASC, s.nama_siswa ASC";

// Prepare and execute
$stmt = $koneksi->prepare($query);
if (!empty($filter_kelas)) {
    $stmt->bind_param("i", $filter_kelas);
}
$stmt->execute();
$result_siswa = $stmt->get_result();

// Get all kelas for filter dropdown
$kelas_query = "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
$result_kelas = $koneksi->query($kelas_query);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - SMK Cyber Core E-Journal</title>
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
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Data Siswa</h1>
                        <p class="text-slate-400">Daftar lengkap siswa di setiap kelas</p>
                    </div>
                    <button onclick="tambahSiswa()" class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-lg transition-colors font-medium flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Siswa
                    </button>
                </div>

                <!-- Filter Section -->
                <div class="glass rounded-lg border border-slate-800 p-6 mb-6">
                    <form method="GET" class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Filter Kelas</label>
                            <select name="kelas" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500">
                                <option value="">Semua Kelas</option>
                                <?php while ($kelas = $result_kelas->fetch_assoc()): ?>
                                    <option value="<?php echo $kelas['id']; ?>" <?php echo $filter_kelas == $kelas['id'] ? 'selected' : ''; ?>>
                                        <?php echo $kelas['nama_kelas']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-lg transition-colors font-medium">
                            Terapkan Filter
                        </button>
                    </form>
                </div>

                <!-- Siswa Table -->
                <div class="glass rounded-lg border border-slate-800 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-800 bg-slate-900/50">
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">NIS</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Jenis Kelamin</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Tempat Lahir</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal Lahir</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No Telepon</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <?php
                                if ($result_siswa->num_rows > 0):
                                    $no = 1;
                                    while ($siswa = $result_siswa->fetch_assoc()):
                                ?>
                                        <tr class="hover:bg-slate-800/50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-white font-medium"><?php echo $no++; ?></td>
                                            <td class="px-6 py-4 text-sm text-white">
                                                <span class="bg-slate-700 px-2 py-1 rounded text-xs"><?php echo $siswa['nis']; ?></span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-300">
                                                <?php echo $siswa['nama_siswa']; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-300">
                                                <?php echo $siswa['nama_kelas'] ?? '-'; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-300">
                                                <?php
                                                if ($siswa['jenis_kelamin'] === 'L') {
                                                    echo '<span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">Laki-laki</span>';
                                                } else {
                                                    echo '<span class="bg-pink-500/20 text-pink-400 px-2 py-1 rounded text-xs">Perempuan</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-300">
                                                <?php echo $siswa['alamat'] ? substr($siswa['alamat'], 0, 30) . '...' : '-'; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-300">
                                                <?php echo date('d/m/Y', strtotime($siswa['tanggal_lahir'])); ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-300">
                                                <?php echo $siswa['no_telepon'] ?? '-'; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <button onclick="editSiswa(<?php echo $siswa['id']; ?>)" class="text-cyan-400 hover:text-cyan-300 transition-colors text-xs font-medium">
                                                    Edit
                                                </button>
                                                <span class="text-slate-600 mx-2">|</span>
                                                <button onclick="deleteSiswa(<?php echo $siswa['id']; ?>)" class="text-red-400 hover:text-red-300 transition-colors text-xs font-medium">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12v-2a3 3 0 00-3-3H9a3 3 0 00-3 3v2z"></path>
                                                </svg>
                                                <p>Tidak ada data siswa</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 glass rounded-lg border border-slate-800 p-4">
                    <p class="text-sm text-slate-400">
                        <strong>Catatan:</strong> Fitur edit dan hapus siswa sedang dalam pengembangan. Hubungi administrator untuk memodifikasi data siswa saat ini.
                    </p>
                </div>

            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function tambahSiswa() {
            Swal.fire({
                title: 'Tambah Siswa',
                html: `
                    <div class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama Siswa</label>
                            <input type="text" id="nama_siswa" placeholder="Masukkan nama siswa" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-300 mb-2">NIS</label>
                            <input type="text" id="nis" placeholder="Masukkan NIS" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Kelas</label>
                            <select id="id_kelas" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white">
                                <option value="">Pilih Kelas</option>
                                <?php
                                $kelas_query2 = "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
                                $result_kelas2 = $koneksi->query($kelas_query2);
                                while ($k = $result_kelas2->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $k['id']; ?>"><?php echo $k['nama_kelas']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#0284c7',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Info', 'Fitur tambah siswa sedang dalam pengembangan. Hubungi administrator untuk menambahkan siswa baru.', 'info');
                }
            });
        }

        function editSiswa(id) {
            Swal.fire('Info', 'Fitur edit sedang dalam pengembangan.', 'info');
        }

        function deleteSiswa(id) {
            Swal.fire('Info', 'Fitur hapus sedang dalam pengembangan.', 'info');
        }
    </script>
</body>

</html>