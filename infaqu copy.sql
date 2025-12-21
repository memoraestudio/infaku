-- HAPUS DATABASE JIKA ADA & BUAT BARU
DROP DATABASE IF EXISTS `infaqu`;
CREATE DATABASE `infaqu`;
USE `infaqu`;

-- ==================== TABEL MASTER WILAYAH ====================

CREATE TABLE `master_kota` (
  `kota_id` VARCHAR(5) PRIMARY KEY,
  `nama_kota` VARCHAR(60) NOT NULL,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `master_daerah` (
  `daerah_id` VARCHAR(7) PRIMARY KEY,
  `kota_id` VARCHAR(5) NOT NULL,
  `nama_daerah` VARCHAR(60) NOT NULL,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`kota_id`) REFERENCES `master_kota`(`kota_id`)
);

CREATE TABLE `master_desa` (
  `desa_id` VARCHAR(9) PRIMARY KEY,
  `daerah_id` VARCHAR(7) NOT NULL,
  `nama_desa` VARCHAR(60) NOT NULL,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`daerah_id`) REFERENCES `master_daerah`(`daerah_id`)
);

CREATE TABLE `master_kelompok` (
  `kelompok_id` VARCHAR(11) PRIMARY KEY,
  `desa_id` VARCHAR(9) NOT NULL,
  `nama_kelompok` VARCHAR(60) NOT NULL,
  `alamat_masjid` TEXT,
  `nama_masjid` VARCHAR(100),
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`desa_id`) REFERENCES `master_desa`(`desa_id`)
);

-- ==================== TABEL MASTER DATA ====================

CREATE TABLE `master_dapuan` (
  `dapuan_id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_dapuan` VARCHAR(20) UNIQUE NOT NULL,
  `nama_dapuan` VARCHAR(100) NOT NULL,
  `tipe_jamaah` ENUM('DEWASA', 'REMAJA', 'LANSIA', 'ANAK') DEFAULT 'DEWASA',
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `kategori_keuangan` (
  `kategori_id` VARCHAR(5) PRIMARY KEY,
  `nama_kontribusi` VARCHAR(100) NOT NULL,
  `tipe_kategori` ENUM('PEMASUKAN', 'PENGELUARAN') NOT NULL,
  `jenis_ibadah` ENUM('INFAQ', 'SODAQOH', 'ZAKAT', 'QURBAN', 'WAKAF', 'FIDYAH', 'LAINNYA') NOT NULL,
  `level_penerapan` ENUM('pusat', 'DAERAH', 'DESA', 'KELOMPOK') DEFAULT 'KELOMPOK',
  `is_aktif` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `sub_kategori` (
  `sub_kategori_id` INT AUTO_INCREMENT PRIMARY KEY,
  `kategori_id` VARCHAR(5) NOT NULL,
  `kode_sub` VARCHAR(20) UNIQUE NOT NULL,
  `nama_sub` VARCHAR(100) NOT NULL,
  `default_nilai` DECIMAL(15,2) DEFAULT 0.00,
  `satuan` VARCHAR(20) DEFAULT 'IDR',
  `urutan` INT DEFAULT 0,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`kategori_id`) REFERENCES `kategori_keuangan`(`kategori_id`)
);

-- ==================== TABEL JAMAAH & KELUARGA ====================

CREATE TABLE `jamaah` (
  `jamaah_id` VARCHAR(10) PRIMARY KEY,
  `nik` VARCHAR(16) UNIQUE,
  `nama_lengkap` VARCHAR(255) NOT NULL,
  `tempat_lahir` VARCHAR(100),
  `tanggal_lahir` DATE,
  `jenis_kelamin` ENUM('L', 'P') NOT NULL,
  `alamat` TEXT,
  `telepon` VARCHAR(15),
  `email` VARCHAR(100),
  `pekerjaan` VARCHAR(100),
  `status_menikah` ENUM('BELUM_MENIKAH', 'MENIKAH', 'JANDA', 'DUDA') DEFAULT 'BELUM_MENIKAH',
  `golongan_darah` ENUM('A', 'B', 'AB', 'O', '-') DEFAULT '-',
  `dapuan_id` INT NOT NULL,
  `kelompok_id` VARCHAR(11) NOT NULL,
  `foto_profil` VARCHAR(255),
  `is_aktif` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`dapuan_id`) REFERENCES `master_dapuan`(`dapuan_id`),
  FOREIGN KEY (`kelompok_id`) REFERENCES `master_kelompok`(`kelompok_id`)
);

CREATE TABLE `keluarga` (
  `keluarga_id` VARCHAR(10) PRIMARY KEY,
  `no_kk` VARCHAR(16) UNIQUE,
  `kepala_keluarga_id` VARCHAR(10) NOT NULL UNIQUE,
  `nama_keluarga` VARCHAR(100),
  `alamat` TEXT,
  `telepon` VARCHAR(15),
  `kelompok_id` VARCHAR(11) NOT NULL,
  `total_anggota` INT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`kepala_keluarga_id`) REFERENCES `jamaah`(`jamaah_id`),
  FOREIGN KEY (`kelompok_id`) REFERENCES `master_kelompok`(`kelompok_id`)
);

CREATE TABLE `anggota_keluarga` (
  `anggota_id` INT AUTO_INCREMENT PRIMARY KEY,
  `keluarga_id` VARCHAR(10) NOT NULL,
  `jamaah_id` VARCHAR(10) NOT NULL UNIQUE,
  `status_hubungan` ENUM('KEPALA_KELUARGA', 'ISTRI', 'ANAK', 'MENANTU', 'CUCU', 'ORANGTUA', 'SAUDARA', 'LAINNYA') NOT NULL,
  `urutan` INT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`keluarga_id`) REFERENCES `keluarga`(`keluarga_id`),
  FOREIGN KEY (`jamaah_id`) REFERENCES `jamaah`(`jamaah_id`)
);

-- ==================== TABEL KEUANGAN ====================

CREATE TABLE `transaksi` (
  `transaksi_id` VARCHAR(20) PRIMARY KEY,
  `kode_transaksi` VARCHAR(50) UNIQUE NOT NULL,
  `tgl_transaksi` DATE NOT NULL,
  `jamaah_id` VARCHAR(10) NOT NULL,
  `kategori_id` VARCHAR(5) NOT NULL,
  `sub_kategori_id` INT,
  `jumlah` DECIMAL(15,2) NOT NULL,
  `satuan` VARCHAR(20) DEFAULT 'IDR',
  `keterangan` TEXT,
  `metode_bayar` ENUM('TUNAI', 'TRANSFER', 'QRIS', 'LAINNYA') DEFAULT 'TUNAI',
  `bukti_bayar` VARCHAR(255),
  `status` ENUM('PENDING', 'VERIFIED', 'REJECTED') DEFAULT 'PENDING',
  `verified_by` INT,
  `verified_at` TIMESTAMP NULL,
  `created_by` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`jamaah_id`) REFERENCES `jamaah`(`jamaah_id`),
  FOREIGN KEY (`kategori_id`) REFERENCES `kategori_keuangan`(`kategori_id`),
  FOREIGN KEY (`sub_kategori_id`) REFERENCES `sub_kategori`(`sub_kategori_id`)
);

CREATE TABLE `laporan_keuangan` (
  `laporan_id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_laporan` VARCHAR(50) UNIQUE NOT NULL,
  `judul_laporan` VARCHAR(255) NOT NULL,
  `tgl_awal` DATE NOT NULL,
  `tgl_akhir` DATE NOT NULL,
  `tipe_laporan` ENUM('HARIAN', 'MINGGUAN', 'BULANAN', 'TAHUNAN', 'KHUSUS') NOT NULL,
  `total_pemasukan` DECIMAL(15,2) DEFAULT 0.00,
  `total_pengeluaran` DECIMAL(15,2) DEFAULT 0.00,
  `saldo_akhir` DECIMAL(15,2) DEFAULT 0.00,
  `created_by` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `laporan_detail` (
  `detail_id` INT AUTO_INCREMENT PRIMARY KEY,
  `laporan_id` INT NOT NULL,
  `transaksi_id` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`laporan_id`) REFERENCES `laporan_keuangan`(`laporan_id`),
  FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi`(`transaksi_id`)
);

-- ==================== TABEL USER & AUTH ====================

CREATE TABLE `roles` (
  `role_id` VARCHAR(10) PRIMARY KEY,
  `nama_role` VARCHAR(50) NOT NULL,
  `level` ENUM('Pusat','Daerah','Desa','Kelompok','Ruyah') NOT NULL,
  `permissions` JSON,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(255) NOT NULL,
  `telepon` VARCHAR(15),
  `role_id` VARCHAR(10) NOT NULL,
  `jamaah_id` VARCHAR(10) UNIQUE,
  `wilayah_id` VARCHAR(20),
  `foto_profil` VARCHAR(255),
  `is_aktif` BOOLEAN DEFAULT TRUE,
  `last_login` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`),
  FOREIGN KEY (`jamaah_id`) REFERENCES `jamaah`(`jamaah_id`)
);

-- ==================== TABEL SYSTEM ====================

CREATE TABLE `system_settings` (
  `setting_id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) UNIQUE NOT NULL,
  `setting_value` TEXT,
  `setting_group` VARCHAR(50) DEFAULT 'general',
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `activity_logs` (
  `log_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
);

-- MASTER: kategori utama (SODAQOH, INFAQ, QURBAN, ZAKAT, ...)
CREATE TABLE master_kontribusi (
  master_kontribusi_id INT AUTO_INCREMENT PRIMARY KEY,
  kode_kontribusi VARCHAR(30) UNIQUE NOT NULL,
  nama_kontribusi VARCHAR(100) NOT NULL,
  keterangan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SUB: item kontribusi (nama | value | jenis | keterangan | level)
CREATE TABLE sub_kontribusi (
  sub_kat_id INT AUTO_INCREMENT PRIMARY KEY,
  master_kontribusi_id INT NOT NULL,
  nama_kontribusi VARCHAR(150) NOT NULL,
  value DECIMAL(15,4) NOT NULL DEFAULT 0, -- untuk persen gunakan desimal 0.05 = 5%
  jenis ENUM('percentage','nominal') NOT NULL DEFAULT 'nominal',
  keterangan VARCHAR(255),
  level VARCHAR(20) NOT NULL DEFAULT 'Kelompok', -- pusat / daerah / desa / kelompok
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (master_kontribusi_id) REFERENCES master_kontribusi(master_kontribusi_id)
);

-- ==================== INDEXES ====================

CREATE INDEX `idx_jamaah_nama` ON `jamaah`(`nama_lengkap`);
CREATE INDEX `idx_jamaah_telepon` ON `jamaah`(`telepon`);
CREATE INDEX `idx_transaksi_tanggal` ON `transaksi`(`tgl_transaksi`);
CREATE INDEX `idx_transaksi_jamaah` ON `transaksi`(`jamaah_id`);
CREATE INDEX `idx_transaksi_status` ON `transaksi`(`status`);
CREATE INDEX `idx_keluarga_kepala` ON `keluarga`(`kepala_keluarga_id`);
CREATE INDEX `idx_anggota_keluarga` ON `anggota_keluarga`(`keluarga_id`, `jamaah_id`);
CREATE INDEX `idx_users_role` ON `users`(`role_id`);
CREATE INDEX `idx_users_wilayah` ON `users`(`wilayah_id`);

INSERT INTO master_kota (kota_id, nama_kota, keterangan) VALUES
('K001', 'Kota Bandung', 'Wilayah pusat'),
('K002', 'Kota Cimahi', 'Wilayah barat');

INSERT INTO master_daerah (daerah_id, kota_id, nama_daerah, keterangan) VALUES
('D00101', 'K001', 'Coblong', 'Daerah kampus'),
('D00102', 'K001', 'Antapani', 'Daerah timur'),
('D00201', 'K002', 'Cibeber', 'Daerah pemukiman');

INSERT INTO master_desa (desa_id, daerah_id, nama_desa, keterangan) VALUES
('DS001001', 'D00101', 'Dago', 'Dekat ITB'),
('DS001002', 'D00102', 'Antapani Wetan', 'Wilayah padat'),
('DS002001', 'D00201', 'Cibeber Girang', 'Perbukitan');

INSERT INTO master_kelompok (kelompok_id, desa_id, nama_kelompok, alamat_masjid, nama_masjid) VALUES
('KL00100001', 'DS001001', 'Kelompok Dago 01', 'Jl Dago No 21', 'Masjid Al-Ihsan'),
('KL00100002', 'DS001002', 'Kelompok Antapani 01', 'Jl Antapani No 10', 'Masjid Al-Jihad'),
('KL00200001', 'DS002001', 'Kelompok Cibeber 01', 'Jl Cibeber No 5', 'Masjid Al-Ikhlas');

INSERT INTO master_dapuan (kode_dapuan, nama_dapuan, tipe_jamaah) VALUES
('DP001', 'Kajian Dewasa', 'DEWASA'),
('DP002', 'Remaja Masjid', 'REMAJA'),
('DP003', 'Kajian Lansia', 'LANSIA');

INSERT INTO kategori_keuangan 
(kategori_id, nama_kontribusi, tipe_kategori, jenis_ibadah, level_penerapan)
VALUES
('K01', 'Infaq Jumat', 'PEMASUKAN', 'INFAQ', 'KELOMPOK'),
('K02', 'Sodaqoh Umum', 'PEMASUKAN', 'SODAQOH', 'KELOMPOK'),
('K03', 'Operasional Masjid', 'PENGELUARAN', 'LAINNYA', 'KELOMPOK');

INSERT INTO sub_kategori
(kategori_id, kode_sub, nama_sub, default_nilai, satuan, urutan)
VALUES
('K01', 'INFQ-JMT', 'Infaq Kotak Jumat', 0, 'IDR', 1),
('K02', 'SDQ-UMM', 'Sodaqoh Bebas', 0, 'IDR', 1),
('K03', 'OPS-LISTRIK', 'Pembayaran Listrik', 0, 'IDR', 1);

INSERT INTO jamaah
(jamaah_id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, telepon, pekerjaan, status_menikah, dapuan_id, kelompok_id)
VALUES
('JM00000001','3211111111111111','Ahmad Fauzi','Bandung','1990-05-22','L','Jl Dago 21','081234111001','Karyawan','MENIKAH',1,'KL00100001'),
('JM00000002','3211111111111112','Siti Aminah','Bandung','1992-08-12','P','Jl Dago 21','081234111002','Ibu Rumah Tangga','MENIKAH',1,'KL00100001'),
('JM00000003','3211111111111113','Budi Rahman','Cimahi','1985-02-14','L','Jl Antapani 12','081234111003','Wiraswasta','MENIKAH',2,'KL00100002'),
('JM00000004','3211111111111114','Rahma Salma','Cimahi','2005-10-10','P','Jl Cibeber 5','081234111004','Pelajar','BELUM_MENIKAH',2,'KL00200001');

INSERT INTO keluarga (keluarga_id, no_kk, kepala_keluarga_id, nama_keluarga, alamat, telepon, kelompok_id, total_anggota)
VALUES
('KF0000001','3211111111111111','JM00000001','Keluarga Fauzi','Jl Dago 21','081234111001','KL00100001',2),
('KF0000002','3211111111111113','JM00000003','Keluarga Budi','Jl Antapani 12','081234111003','KL00100002',1);

INSERT INTO anggota_keluarga (keluarga_id, jamaah_id, status_hubungan, urutan)
VALUES
('KF0000001','JM00000001','KEPALA_KELUARGA',1),
('KF0000001','JM00000002','ISTRI',2),
('KF0000002','JM00000003','KEPALA_KELUARGA',1);

INSERT INTO transaksi
(transaksi_id, kode_transaksi, tgl_transaksi, jamaah_id, kategori_id, sub_kategori_id, jumlah, metode_bayar, status, created_by)
VALUES
('TRX00001','INFQ-20250101-001','2025-01-01','JM00000001','K01',1,50000,'TUNAI','VERIFIED',1),
('TRX00002','SDQ-20250102-001','2025-01-02','JM00000003','K02',2,75000,'QRIS','VERIFIED',1),
('TRX00003','OPS-20250103-001','2025-01-03','JM00000001','K03',3,200000,'TRANSFER','VERIFIED',1);

INSERT INTO laporan_keuangan
(kode_laporan, judul_laporan, tgl_awal, tgl_akhir, tipe_laporan, total_pemasukan, total_pengeluaran, saldo_akhir, created_by)
VALUES
('LPR-2025-01','Laporan Januari 2025','2025-01-01','2025-01-31','BULANAN',125000,200000,-75000,1);

INSERT INTO laporan_detail (laporan_id, transaksi_id) VALUES
(1, 'TRX00001'),
(1, 'TRX00002'),
(1, 'TRX00003');

INSERT INTO roles (role_id, nama_role, level, permissions) VALUES
('RL001','Admin pusat','Pusat','["manage_all"]'),
('RL002','Admin Daerah','Daerah','["manage_daerah"]'),
('RL003','Admin Desa','Desa','["manage_desa"]'),
('RL004','Admin Kelompok','Kelompok','["manage_kelompok"]'),
('RL005','Jamaah','Ruyah','["view_self"]');

INSERT INTO users (username, email, password, nama_lengkap, telepon, role_id, jamaah_id, wilayah_id)
VALUES
('Admin Pusat', 'adminpusat@infaqu.com', 'password_hash', 'Administrator', '081200000001', 'RL001', 'JM00000001', 'K001'),
('Admin Daerah', 'admindaerah@infaqu.com', 'password_hash', 'Ahmad Fauzi', '081234111001', 'RL002', 'JM00000002', 'D00102'),
('Admin Desa', 'admindesa@infaqu.com', 'password_hash', 'Budi Rahman', '081234111003', 'RL003', 'JM00000003', 'DS001002'),
('Admin Kelompok', 'adminkelompok@infaqu.com', 'password_hash', 'Budi Rahman', '081234111003', 'RL004', 'JM00000004', 'KL00100002');

INSERT INTO system_settings (setting_key, setting_value, setting_group) VALUES
('app_name', 'Infaqu Management System', 'general'),
('currency', 'IDR', 'general');

INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent)
VALUES
(1, 'LOGIN', 'Admin login pertama kali', '127.0.0.1', 'Chrome Windows'),
(2, 'ADD_TRANSAKSI', 'Menambahkan transaksi infaq', '127.0.0.1', 'Chrome Windows');

INSERT INTO master_kontribusi (kode_kontribusi, nama_kontribusi, keterangan) VALUES
('SOD','SODAQOH','Sodaqoh / sedekah rutin'),
('INF','INFAQ','Infaq kotak / infaq lainnya'),
('QUR','QURBAN','Qurban / penyembelihan hewan'),
('ZKT','ZAKAT','Zakat (fitrah / mal)');

INSERT INTO sub_kontribusi (master_kontribusi_id, nama_kontribusi, value, jenis, keterangan, level) VALUES
(1, 'Sodaqoh Rutin Bulanan', 0, 'nominal', 'Seikhlasnya', 'kelompok'),
(1, 'Sodaqoh Jumat Pagi', 0, 'nominal', 'Kotak masjid setiap Jumat', 'kelompok'),
(1, 'Sodaqoh Khusus Kegiatan', 0, 'nominal', 'Donasi untuk kegiatan khusus', 'kelompok'),
(2, 'Infaq Rezeki', 0.0005, 'percentage', '0.05% dari gaji (jika di-set sebagai percentage)', 'kelompok'),
(2, 'Ukhro MT', 25000, 'nominal', 'Ukhro MT (fixed)', 'kelompok'),
(2, 'Perawatan Masjid', 0, 'nominal', 'Seikhlasnya untuk perawatan masjid', 'kelompok'),
(3, 'Qurban Sapi (patungan)', 3500000, 'nominal', 'Patungan sapi (estimasi)', 'pusat'),
(3, 'Qurban Kambing', 2500000, 'nominal', 'Qurban kambing', 'pusat'),
(3, 'Patungan Qurban', 500000, 'nominal', 'Sumbangan patungan', 'kelompok'),
(4, 'Zakat Fitrah (beras)', 3.5, 'nominal', 'Kg beras per orang', 'pusat'),
(4, 'Zakat Mal (persentase)', 0.0250, 'percentage', '2.5% dari harta', 'pusat');
