<?php

/**
 * Module: Simpan Jurnal
 * Process untuk menyimpan atau update jurnal dengan Prepared Statements
 * 
 * POST Data (JSON):
 * - tanggal
 * - id_kelas
 * - mata_pelajaran
 * - materi
 * - jam_pelajaran
 * - sakit
 * - izin
 * - alfa
 * - keterangan
 * - edit_id (optional untuk update)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/koneksi.php';

// Set header JSON
header('Content-Type: application/json');

// Response handler
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Cek login
    if (!isLoggedIn()) {
        throw new Exception('User belum login');
    }

    // Ambil data JSON dari request
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Data input tidak valid');
    }

    // Validasi CSRF token (jika diperlukan)
    // if (!isset($input['csrf_token']) || !verifyCSRFToken($input['csrf_token'])) {
    //     throw new Exception('CSRF token tidak valid');
    // }

    // Sanitasi input
    $tanggal = sanitizeInput($input['tanggal'] ?? '');
    $id_kelas = intval($input['id_kelas'] ?? 0);
    $mata_pelajaran = sanitizeInput($input['mata_pelajaran'] ?? '');
    $materi = sanitizeInput($input['materi'] ?? '');
    $jam_pelajaran = sanitizeInput($input['jam_pelajaran'] ?? '');
    $sakit = intval($input['sakit'] ?? 0);
    $izin = intval($input['izin'] ?? 0);
    $alfa = intval($input['alfa'] ?? 0);
    $keterangan = sanitizeInput($input['keterangan'] ?? '');
    $edit_id = isset($input['edit_id']) && $input['edit_id'] ? intval($input['edit_id']) : null;

    // Validasi data wajib
    if (empty($tanggal) || $id_kelas == 0 || empty($mata_pelajaran) || empty($materi)) {
        throw new Exception('Data wajib tidak lengkap');
    }

    // Validasi format tanggal
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        throw new Exception('Format tanggal tidak valid');
    }

    // Validasi format jam pelajaran
    if (!preg_match('/^\d{2}:\d{2}-\d{2}:\d{2}$/', $jam_pelajaran)) {
        throw new Exception('Format jam pelajaran harus HH:MM-HH:MM');
    }

    $user_id = $_SESSION['user_id'];
    $id_user = intval($user_id);

    // Verifikasi kelas ada dan user memiliki akses
    $check_kelas = queryPrepared(
        "SELECT id FROM kelas WHERE id = ?",
        [$id_kelas],
        "i"
    );

    if ($check_kelas->num_rows == 0) {
        throw new Exception('Kelas tidak ditemukan');
    }

    if ($edit_id) {
        // Update mode - cek kepemilikan
        $check_jurnal = queryPrepared(
            "SELECT id FROM jurnal WHERE id = ? AND id_user = ?",
            [$edit_id, $id_user],
            "ii"
        );

        if ($check_jurnal->num_rows == 0) {
            throw new Exception('Jurnal tidak ditemukan atau Anda tidak memiliki akses');
        }

        // Update jurnal
        $rows_affected = updateData(
            "UPDATE jurnal 
             SET id_kelas = ?, tanggal = ?, mata_pelajaran = ?, materi = ?, 
                 jam_pelajaran = ?, sakit = ?, izin = ?, alfa = ?, 
                 keterangan = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND id_user = ?",
            [
                $id_kelas,
                $tanggal,
                $mata_pelajaran,
                $materi,
                $jam_pelajaran,
                $sakit,
                $izin,
                $alfa,
                $keterangan,
                $edit_id,
                $id_user
            ],
            "issssiiiiiii"
        );

        if ($rows_affected > 0) {
            $response['success'] = true;
            $response['message'] = 'Jurnal berhasil diperbarui';
            $response['data'] = ['id' => $edit_id];

            // Log aktivitas
            logActivity('UPDATE_JURNAL', "Jurnal ID: $edit_id updated");
        } else {
            throw new Exception('Gagal memperbarui jurnal');
        }
    } else {
        // Insert mode
        $jurnal_id = insertData(
            "INSERT INTO jurnal (id_user, id_kelas, tanggal, mata_pelajaran, materi, 
                                 jam_pelajaran, sakit, izin, alfa, keterangan)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $id_user,
                $id_kelas,
                $tanggal,
                $mata_pelajaran,
                $materi,
                $jam_pelajaran,
                $sakit,
                $izin,
                $alfa,
                $keterangan
            ],
            "iisssssiiis"
        );

        if ($jurnal_id > 0) {
            $response['success'] = true;
            $response['message'] = 'Jurnal berhasil disimpan';
            $response['data'] = ['id' => $jurnal_id];

            // Log aktivitas
            logActivity('CREATE_JURNAL', "Jurnal ID: $jurnal_id created");
        } else {
            throw new Exception('Gagal menyimpan jurnal');
        }
    }
} catch (Exception $e) {
    http_response_code(400);
    $response['success'] = false;
    $response['message'] = $e->getMessage();

    // Log error
    error_log('Jurnal Error: ' . $e->getMessage());
}

// Output JSON
echo json_encode($response);
exit;
