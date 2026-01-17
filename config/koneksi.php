<?php

/**
 * File Konfigurasi Koneksi Database
 * Menggunakan MySQLi dengan standar keamanan PHP modern
 */

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cybercore_ejournal');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Buat koneksi MySQLi dengan error handling
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Periksa koneksi
if ($koneksi->connect_error) {
    // Log error ke file, jangan tampilkan ke user
    error_log('Database Connection Failed: ' . $koneksi->connect_error);

    // Tampilkan pesan yang aman ke user
    die('Terjadi kesalahan koneksi database. Silakan hubungi administrator.');
}

// Set charset ke UTF-8 untuk mendukung karakter Unicode
if (!$koneksi->set_charset(DB_CHARSET)) {
    error_log('Error loading character set utf8mb4: ' . $koneksi->error);
    die('Terjadi kesalahan konfigurasi database.');
}

// Disable autocommit untuk transaksi yang lebih aman
$koneksi->autocommit(FALSE);

/**
 * Fungsi helper untuk query yang aman dengan prepared statement
 * @param string $query Query SQL dengan placeholder ?
 * @param array $params Parameter yang akan di-bind
 * @param string $types Tipe parameter (s=string, i=integer, d=double, b=blob)
 * @return mysqli_result|bool Result object atau FALSE jika gagal
 */
function queryPrepared($query, $params = array(), $types = '')
{
    global $koneksi;

    // Prepare statement
    $stmt = $koneksi->prepare($query);

    if (!$stmt) {
        error_log('Prepare failed: ' . $koneksi->error);
        return false;
    }

    // Bind parameters jika ada
    if (!empty($params)) {
        if (empty($types)) {
            // Auto-detect types jika tidak disediakan
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
        }

        if (!$stmt->bind_param($types, ...$params)) {
            error_log('Bind failed: ' . $stmt->error);
            return false;
        }
    }

    // Execute statement
    if (!$stmt->execute()) {
        error_log('Execute failed: ' . $stmt->error);
        return false;
    }

    return $stmt->get_result();
}

/**
 * Fungsi untuk insert data
 * @param string $query Query SQL dengan placeholder ?
 * @param array $params Parameter yang akan di-bind
 * @param string $types Tipe parameter
 * @return int ID baris terakhir atau 0 jika gagal
 */
function insertData($query, $params = array(), $types = '')
{
    global $koneksi;

    // Prepare statement
    $stmt = $koneksi->prepare($query);

    if (!$stmt) {
        error_log('Prepare failed: ' . $koneksi->error);
        return 0;
    }

    // Bind parameters
    if (!empty($params)) {
        if (empty($types)) {
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
        }

        if (!$stmt->bind_param($types, ...$params)) {
            error_log('Bind failed: ' . $stmt->error);
            return 0;
        }
    }

    // Execute statement
    if (!$stmt->execute()) {
        error_log('Execute failed: ' . $stmt->error);
        return 0;
    }

    return $koneksi->insert_id;
}

/**
 * Fungsi untuk update atau delete data
 * @param string $query Query SQL dengan placeholder ?
 * @param array $params Parameter yang akan di-bind
 * @param string $types Tipe parameter
 * @return int Jumlah baris yang terpengaruh atau 0 jika gagal
 */
function updateData($query, $params = array(), $types = '')
{
    global $koneksi;

    // Prepare statement
    $stmt = $koneksi->prepare($query);

    if (!$stmt) {
        error_log('Prepare failed: ' . $koneksi->error);
        return 0;
    }

    // Bind parameters
    if (!empty($params)) {
        if (empty($types)) {
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
        }

        if (!$stmt->bind_param($types, ...$params)) {
            error_log('Bind failed: ' . $stmt->error);
            return 0;
        }
    }

    // Execute statement
    if (!$stmt->execute()) {
        error_log('Execute failed: ' . $stmt->error);
        return 0;
    }

    return $koneksi->affected_rows;
}

/**
 * Fungsi untuk commit transaksi
 */
function commitTransaction()
{
    global $koneksi;
    if (!$koneksi->commit()) {
        error_log('Commit failed: ' . $koneksi->error);
        return false;
    }
    return true;
}

/**
 * Fungsi untuk rollback transaksi
 */
function rollbackTransaction()
{
    global $koneksi;
    if (!$koneksi->rollback()) {
        error_log('Rollback failed: ' . $koneksi->error);
        return false;
    }
    return true;
}

/**
 * Fungsi untuk menutup koneksi
 */
function closeConnection()
{
    global $koneksi;
    if ($koneksi->close()) {
        return true;
    }
    return false;
}
