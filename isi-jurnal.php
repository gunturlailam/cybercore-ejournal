<?php

/**
 * Form Isi Jurnal - SMK Cyber Core Indonesia E-Journal
 * Form modern dengan floating labels
 */

require_once 'config/config.php';
require_once 'config/koneksi.php';

// Cek login
if (!isLoggedIn()) {
    redirect(APP_URL . 'login.php');
}

// Set page title
$page_title = 'Isi Jurnal';

// Check if edit mode
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$jurnal_data = null;

if ($edit_id) {
    // Get jurnal data for editing
    $result = queryPrepared(
        "SELECT * FROM jurnal WHERE id = ? AND id_user = ?",
        [$edit_id, $_SESSION['user_id']],
        "ii"
    );

    if ($result->num_rows > 0) {
        $jurnal_data = $result->fetch_assoc();
    } else {
        redirect(APP_URL . 'index.php');
    }
}

// Get all classes
$result_kelas = queryPrepared(
    "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC",
    [],
    ""
);

require_once 'includes/header.php';
?>

<!-- Form Section -->
<div class="max-w-2xl mx-auto">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white"><?php echo $edit_id ? 'Edit Jurnal' : 'Isi Jurnal Mengajar'; ?></h1>
        <p class="text-slate-400 mt-2">Isikan data mengajar Anda dengan lengkap dan akurat</p>
    </div>

    <!-- Form Card -->
    <form id="formJurnal" class="glass rounded-lg border border-slate-800 p-8 space-y-6">

        <!-- Row 1: Tanggal & Jam Pelajaran -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Tanggal -->
            <div class="relative">
                <input
                    type="date"
                    id="tanggal"
                    name="tanggal"
                    value="<?php echo $jurnal_data ? $jurnal_data['tanggal'] : date('Y-m-d'); ?>"
                    required
                    class="peer w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-transparent focus:outline-none focus:border-cyber-500 focus:ring-2 focus:ring-cyber-500/20 transition-all duration-200">
                <label for="tanggal" class="absolute left-4 -top-2.5 text-sm font-medium text-slate-400 bg-slate-950 px-1 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-base peer-focus:-top-2.5 peer-focus:text-cyber-400 transition-all duration-200">
                    Tanggal
                </label>
            </div>

            <!-- Jam Pelajaran -->
            <div class="relative">
                <input
                    type="text"
                    id="jam_pelajaran"
                    name="jam_pelajaran"
                    placeholder="08:00-09:00"
                    value="<?php echo $jurnal_data ? $jurnal_data['jam_pelajaran'] : ''; ?>"
                    required
                    class="peer w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-transparent focus:outline-none focus:border-cyber-500 focus:ring-2 focus:ring-cyber-500/20 transition-all duration-200">
                <label for="jam_pelajaran" class="absolute left-4 -top-2.5 text-sm font-medium text-slate-400 bg-slate-950 px-1 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-base peer-focus:-top-2.5 peer-focus:text-cyber-400 transition-all duration-200">
                    Jam Pelajaran
                </label>
            </div>

        </div>

        <!-- Row 2: Kelas & Mata Pelajaran -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Pilih Kelas -->
            <div class="relative">
                <select
                    id="id_kelas"
                    name="id_kelas"
                    required
                    class="peer w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white focus:outline-none focus:border-cyber-500 focus:ring-2 focus:ring-cyber-500/20 transition-all duration-200 appearance-none cursor-pointer">
                    <option value="" disabled selected></option>
                    <?php
                    if ($result_kelas->num_rows > 0) {
                        while ($kelas = $result_kelas->fetch_assoc()) {
                            $selected = ($jurnal_data && $jurnal_data['id_kelas'] == $kelas['id']) ? 'selected' : '';
                            echo '<option value="' . $kelas['id'] . '" ' . $selected . '>' . escapeOutput($kelas['nama_kelas']) . '</option>';
                        }
                    }
                    ?>
                </select>
                <label for="id_kelas" class="absolute left-4 -top-2.5 text-sm font-medium text-slate-400 bg-slate-950 px-1 peer-invalid:-top-2.5 peer-focus:-top-2.5 peer-focus:text-cyber-400 transition-all duration-200">
                    Kelas
                </label>
                <!-- Dropdown Arrow -->
                <svg class="absolute right-4 top-3.5 w-5 h-5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>

            <!-- Mata Pelajaran -->
            <div class="relative">
                <input
                    type="text"
                    id="mata_pelajaran"
                    name="mata_pelajaran"
                    placeholder="Nama Mata Pelajaran"
                    value="<?php echo $jurnal_data ? escapeOutput($jurnal_data['mata_pelajaran']) : ''; ?>"
                    required
                    class="peer w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-transparent focus:outline-none focus:border-cyber-500 focus:ring-2 focus:ring-cyber-500/20 transition-all duration-200">
                <label for="mata_pelajaran" class="absolute left-4 -top-2.5 text-sm font-medium text-slate-400 bg-slate-950 px-1 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-base peer-focus:-top-2.5 peer-focus:text-cyber-400 transition-all duration-200">
                    Mata Pelajaran
                </label>
            </div>

        </div>

        <!-- Materi Pelajaran -->
        <div class="relative">
            <textarea
                id="materi"
                name="materi"
                placeholder="Deskripsi materi yang diajarkan"
                rows="4"
                required
                class="peer w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-transparent focus:outline-none focus:border-cyber-500 focus:ring-2 focus:ring-cyber-500/20 transition-all duration-200 resize-none"><?php echo $jurnal_data ? escapeOutput($jurnal_data['materi']) : ''; ?></textarea>
            <label for="materi" class="absolute left-4 -top-2.5 text-sm font-medium text-slate-400 bg-slate-950 px-1 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-base peer-focus:-top-2.5 peer-focus:text-cyber-400 transition-all duration-200">
                Materi Pelajaran
            </label>
        </div>

        <!-- Divider -->
        <div class="border-t border-slate-700 pt-6">
            <h3 class="text-lg font-semibold text-white mb-4">Data Presensi Siswa</h3>
            <p class="text-slate-400 text-sm mb-4">Masukkan jumlah siswa untuk setiap kategori presensi</p>
        </div>

        <!-- Presensi Inputs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Sakit -->
            <div class="relative">
                <input
                    type="number"
                    id="sakit"
                    name="sakit"
                    min="0"
                    value="<?php echo $jurnal_data ? $jurnal_data['sakit'] : '0'; ?>"
                    class="peer w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-transparent focus:outline-none focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500/20 transition-all duration-200">
                <label for="sakit" class="absolute left-4 -top-2.5 text-sm font-medium text-slate-400 bg-slate-950 px-1 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-base peer-focus:-top-2.5 peer-focus:text-yellow-400 transition-all duration-200">
                    Sakit (Orang)
                </label>
            </div>

            <!-- Izin -->
            <div class="relative">
                <input
                    type="number"
                    id="izin"
                    name="izin"
                    min="0"
                    value="<?php echo $jurnal_data ? $jurnal_data['izin'] : '0'; ?>"
                    class="peer w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-transparent focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200">
                <label for="izin" class="absolute left-4 -top-2.5 text-sm font-medium text-slate-400 bg-slate-950 px-1 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-base peer-focus:-top-2.5 peer-focus:text-blue-400 transition-all duration-200">
                    Izin (Orang)
                </label>
            </div>

            <!-- Alfa -->
            <div class="relative">
                <input
                    type="number"
                    id="alfa"
                    name="alfa"
                    min="0"
                    value="<?php echo $jurnal_data ? $jurnal_data['alfa'] : '0'; ?>"
                    class="peer w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-transparent focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition-all duration-200">
                <label for="alfa" class="absolute left-4 -top-2.5 text-sm font-medium text-slate-400 bg-slate-950 px-1 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-base peer-focus:-top-2.5 peer-focus:text-red-400 transition-all duration-200">
                    Alfa (Orang)
                </label>
            </div>

        </div>

        <!-- Keterangan -->
        <div class="relative">
            <textarea
                id="keterangan"
                name="keterangan"
                placeholder="Tambahan keterangan atau catatan (opsional)"
                rows="3"
                class="peer w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-transparent focus:outline-none focus:border-cyber-500 focus:ring-2 focus:ring-cyber-500/20 transition-all duration-200 resize-none"><?php echo $jurnal_data ? escapeOutput($jurnal_data['keterangan']) : ''; ?></textarea>
            <label for="keterangan" class="absolute left-4 -top-2.5 text-sm font-medium text-slate-400 bg-slate-950 px-1 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-base peer-focus:-top-2.5 peer-focus:text-cyber-400 transition-all duration-200">
                Keterangan (Opsional)
            </label>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 pt-6">
            <button
                type="submit"
                class="flex-1 px-6 py-3 rounded-lg bg-gradient-to-r from-cyber-500 to-cyber-600 hover:from-cyber-600 hover:to-cyber-700 text-white font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <?php echo $edit_id ? 'Update Jurnal' : 'Simpan Jurnal'; ?>
            </button>
            <a
                href="<?php echo APP_URL; ?>index.php"
                class="px-6 py-3 rounded-lg border border-slate-700 hover:border-slate-600 bg-slate-800/50 hover:bg-slate-800 text-white font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Batal
            </a>
        </div>

        <!-- Hidden input untuk edit mode -->
        <?php if ($edit_id): ?>
            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
        <?php endif; ?>

    </form>

</div>

<!-- Form Script -->
<script>
    document.getElementById('formJurnal').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = {
            tanggal: formData.get('tanggal'),
            id_kelas: formData.get('id_kelas'),
            mata_pelajaran: formData.get('mata_pelajaran'),
            materi: formData.get('materi'),
            jam_pelajaran: formData.get('jam_pelajaran'),
            sakit: formData.get('sakit') || 0,
            izin: formData.get('izin') || 0,
            alfa: formData.get('alfa') || 0,
            keterangan: formData.get('keterangan'),
            edit_id: formData.get('edit_id') || null
        };

        // Validasi input
        if (!data.id_kelas || !data.mata_pelajaran || !data.materi) {
            showAlert('Error', 'Harap isi semua field yang wajib', 'error');
            return;
        }

        showLoading('<?php echo $edit_id ? 'Memperbarui...' : 'Menyimpan...'; ?>');

        try {
            const response = await fetch('<?php echo APP_URL; ?>modules/simpan-jurnal.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showAlert('Berhasil', '<?php echo $edit_id ? 'Jurnal berhasil diperbarui' : 'Jurnal berhasil disimpan'; ?>', 'success');
                setTimeout(() => {
                    window.location.href = '<?php echo APP_URL; ?>index.php';
                }, 1500);
            } else {
                showAlert('Error', result.message || 'Terjadi kesalahan', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Error', 'Terjadi kesalahan saat menyimpan', 'error');
        }
    });
</script>

<?php
require_once 'includes/footer.php';
?>