-- Database untuk SMK Cyber Core Indonesia E-Journal
CREATE DATABASE IF NOT EXISTS cybercore_ejournal;
USE cybercore_ejournal;

-- Tabel Users (Admin/Guru)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'guru') NOT NULL DEFAULT 'guru',
    email VARCHAR(100),
    no_telepon VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Kelas
CREATE TABLE IF NOT EXISTS kelas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kelas VARCHAR(50) NOT NULL UNIQUE,
    tingkat INT NOT NULL COMMENT '10, 11, 12',
    jurusan VARCHAR(100),
    wali_kelas_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nama_kelas (nama_kelas),
    FOREIGN KEY (wali_kelas_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Siswa
CREATE TABLE IF NOT EXISTS siswa (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_kelas INT NOT NULL,
    nama_siswa VARCHAR(100) NOT NULL,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nisn VARCHAR(20),
    jenis_kelamin ENUM('L', 'P'),
    tanggal_lahir DATE,
    alamat TEXT,
    no_telepon VARCHAR(15),
    email_siswa VARCHAR(100),
    nama_wali VARCHAR(100),
    no_wali VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nis (nis),
    INDEX idx_id_kelas (id_kelas),
    FOREIGN KEY (id_kelas) REFERENCES kelas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Jurnal (Catatan Mengajar)
CREATE TABLE IF NOT EXISTS jurnal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    id_kelas INT NOT NULL,
    tanggal DATE NOT NULL,
    mata_pelajaran VARCHAR(100) NOT NULL,
    materi TEXT NOT NULL,
    jam_pelajaran VARCHAR(20) NOT NULL COMMENT 'Contoh: 08:00-09:00',
    sakit INT DEFAULT 0,
    izin INT DEFAULT 0,
    alfa INT DEFAULT 0,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_tanggal (id_user, tanggal),
    INDEX idx_kelas_tanggal (id_kelas, tanggal),
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Presensi Detail (optional untuk tracking per siswa)
CREATE TABLE IF NOT EXISTS presensi_detail (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_jurnal INT NOT NULL,
    id_siswa INT NOT NULL,
    status ENUM('hadir', 'sakit', 'izin', 'alfa') DEFAULT 'hadir',
    keterangan VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_jurnal) REFERENCES jurnal(id) ON DELETE CASCADE,
    FOREIGN KEY (id_siswa) REFERENCES siswa(id) ON DELETE CASCADE,
    UNIQUE KEY unique_jurnal_siswa (id_jurnal, id_siswa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert data dummy untuk testing
INSERT INTO users (nama, username, password, role, email) VALUES
('Admin SMK', 'admin', '$2y$10$jOFNBhNeFkLu0DqPqGBv6OHp8jPn/eqp2GK6.e6ePe.K/m7A5cGK6', 'admin', 'admin@cybercore.id'),
('Budi Santoso', 'budi_guru', '$2y$10$jOFNBhNeFkLu0DqPqGBv6OHp8jPn/eqp2GK6.e6ePe.K/m7A5cGK6', 'guru', 'budi@cybercore.id');

INSERT INTO kelas (nama_kelas, tingkat, jurusan) VALUES
('X RPL A', 10, 'Rekayasa Perangkat Lunak'),
('X RPL B', 10, 'Rekayasa Perangkat Lunak'),
('XI TJKT', 11, 'Teknik Jaringan Komputer dan Telekomunikasi');

-- Password hash di atas adalah hash dari password 'password123' menggunakan bcrypt
