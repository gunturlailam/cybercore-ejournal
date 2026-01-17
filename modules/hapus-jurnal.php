<?php

/**
 * Module: Hapus Jurnal
 * Delete jurnal dengan JSON response
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/koneksi.php';

// Set header JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

try {
    // Cek login
    if (!isLoggedIn()) {
        throw new Exception('User belum login');
    }

    // Ambil data JSON
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['id'])) {
        throw new Exception('ID jurnal tidak valid');
    }

    $jurnal_id = intval($input['id']);
    $user_id = intval($_SESSION['user_id']);

    // Cek kepemilikan jurnal
    $check_jurnal = queryPrepared(
        "SELECT id FROM jurnal WHERE id = ? AND id_user = ?",
        [$jurnal_id, $user_id],
        "ii"
    );

    if ($check_jurnal->num_rows == 0) {
        throw new Exception('Jurnal tidak ditemukan atau Anda tidak memiliki akses');
    }

    // Delete jurnal
    $rows_affected = updateData(
        "DELETE FROM jurnal WHERE id = ? AND id_user = ?",
        [$jurnal_id, $user_id],
        "ii"
    );

    if ($rows_affected > 0) {
        $response['success'] = true;
        $response['message'] = 'Jurnal berhasil dihapus';

        logActivity('DELETE_JURNAL', "Jurnal ID: $jurnal_id deleted");
    } else {
        throw new Exception('Gagal menghapus jurnal');
    }
} catch (Exception $e) {
    http_response_code(400);
    $response['message'] = $e->getMessage();
    error_log('Delete Jurnal Error: ' . $e->getMessage());
}

echo json_encode($response);
exit;
