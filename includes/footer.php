<?php

/**
 * Footer Template - SMK Cyber Core Indonesia E-Journal
 * Penutup HTML dan library eksternal
 */
?>
</div>

</main>

</div>

<!-- Mobile Menu (Hidden by default) -->
<div id="mobile-menu" class="hidden fixed inset-0 bg-black/50 z-30 md:hidden">
    <div class="glass w-64 h-screen border-r border-slate-800 overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyber-400 to-cyber-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-lg font-bold text-white">SMK CCI</h1>
            </div>

            <nav class="space-y-2">
                <a href="<?php echo APP_URL; ?>dashboard.php" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">Dashboard</a>
                <a href="<?php echo APP_URL; ?>jurnal.php" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">Isi Jurnal</a>
                <a href="<?php echo APP_URL; ?>rekapitulasi.php" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">Rekapitulasi</a>
                <a href="<?php echo APP_URL; ?>siswa.php" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">Data Siswa</a>
                <hr class="my-4 border-slate-700">
                <a href="<?php echo APP_URL; ?>logout.php" class="block px-4 py-3 rounded-lg text-red-400 hover:bg-red-900/20">Logout</a>
            </nav>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

<!-- Custom Script untuk Mobile Menu -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });

            // Close menu jika klik di luar
            mobileMenu.addEventListener('click', function(e) {
                if (e.target === mobileMenu) {
                    mobileMenu.classList.add('hidden');
                }
            });

            // Close menu jika klik link
            const menuLinks = mobileMenu.querySelectorAll('a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                });
            });
        }

        // Initialize Lucide Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    // Helper function untuk SweetAlert2
    function showAlert(title, message, type = 'info', icon = null) {
        const typeIcon = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };

        Swal.fire({
            title: title,
            text: message,
            icon: icon || typeIcon[type] || 'info',
            confirmButtonColor: '#0ea5e9',
            confirmButtonText: 'OK',
            didOpen: (modal) => {
                modal.style.borderRadius = '12px';
            }
        });
    }

    // Confirm dialog
    function confirmAction(title, message, callback) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            didOpen: (modal) => {
                modal.style.borderRadius = '12px';
            }
        }).then((result) => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    }

    // Loading alert
    function showLoading(message = 'Memproses...') {
        Swal.fire({
            title: message,
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: (modal) => {
                Swal.showLoading();
                modal.style.borderRadius = '12px';
            }
        });
    }

    // Close alert
    function closeAlert() {
        Swal.close();
    }
</script>

</body>

</html>