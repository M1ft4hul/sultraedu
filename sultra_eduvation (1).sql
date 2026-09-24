-- =====================================================================
-- DATABASE: SULTRA EDUVATION
-- Sistem Informasi Praktik Baik, Inovasi, Kompetisi, Apresiasi, Monev
-- Jenjang: SMA & SMK Negeri (Dinas Pendidikan Provinsi Sultra)
-- Referensi alur: BAB VI (Ketentuan Input & Validasi) & BAB VII (Dokumentasi & Publikasi)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS sultra_eduvation
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sultra_eduvation;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. TABEL SEKOLAH
-- Satuan pendidikan jenjang SMA/SMK negeri
-- ---------------------------------------------------------------------
CREATE TABLE sekolah (
    id_sekolah      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    npsn            VARCHAR(20)  NOT NULL UNIQUE,
    nama_sekolah    VARCHAR(150) NOT NULL,
    jenjang         ENUM('SMA','SMK') NOT NULL,
    alamat          TEXT,
    kecamatan       VARCHAR(100),
    kabupaten_kota  VARCHAR(100),
    telepon         VARCHAR(20),
    email           VARCHAR(100),
    status          ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sekolah_jenjang (jenjang),
    INDEX idx_sekolah_kabkota (kabupaten_kota)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. TABEL ADMIN
-- 3 role: petugas (dinas, kontrol penuh), admin_sekolah (otoritas sekolah),
-- penanggung_jawab (read-only monitoring & evaluasi)
-- ---------------------------------------------------------------------
CREATE TABLE admin (
    id_admin        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_admin      VARCHAR(100) NOT NULL,
    username        VARCHAR(50)  NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL COMMENT 'simpan hash (bcrypt/argon2), bukan plain text',
    role            ENUM('petugas','admin_sekolah','penanggung_jawab') NOT NULL,
    id_sekolah      INT UNSIGNED NULL COMMENT 'hanya diisi jika role = admin_sekolah',
    email           VARCHAR(100),
    status          ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_admin_sekolah
        FOREIGN KEY (id_sekolah) REFERENCES sekolah(id_sekolah)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_admin_role (role)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. TABEL GURU
-- Diinput oleh admin_sekolah, digunakan guru untuk login & upload praktik baik
-- ---------------------------------------------------------------------
CREATE TABLE guru (
    id_guru         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nip             VARCHAR(30)  UNIQUE,
    nama_guru       VARCHAR(100) NOT NULL,
    user_name       VARCHAR(50)  NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL COMMENT 'simpan hash, bukan plain text',
    id_sekolah      INT UNSIGNED NOT NULL COMMENT 'asal_sekolah',
    mapel           VARCHAR(100),
    status          ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_guru_sekolah
        FOREIGN KEY (id_sekolah) REFERENCES sekolah(id_sekolah)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_guru_sekolah (id_sekolah)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. TABEL PRAKTIK_BAIK
-- Diinput guru (satuan pendidikan), verifikasi berjenjang:
-- admin_sekolah (teknis) -> petugas (validasi dinas)
-- ---------------------------------------------------------------------
CREATE TABLE praktik_baik (
    id_praktik_baik             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_guru                     INT UNSIGNED NOT NULL,
    id_sekolah                  INT UNSIGNED NOT NULL,
    judul                       VARCHAR(200) NOT NULL,
    deskripsi                   TEXT,
    kategori                    VARCHAR(100) COMMENT 'mis: pembelajaran, manajemen, digital, dll',
    status_verifikasi_sekolah   ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
    id_verifikator_sekolah      INT UNSIGNED NULL,
    catatan_admin_sekolah       TEXT,
    tanggal_verifikasi_sekolah  DATETIME NULL,
    status_verifikasi_dinas     ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
    id_verifikator_dinas        INT UNSIGNED NULL,
    catatan_petugas             TEXT,
    tanggal_verifikasi_dinas    DATETIME NULL,
    tanggal_upload              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pb_guru FOREIGN KEY (id_guru) REFERENCES guru(id_guru)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pb_sekolah FOREIGN KEY (id_sekolah) REFERENCES sekolah(id_sekolah)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pb_verif_sekolah FOREIGN KEY (id_verifikator_sekolah) REFERENCES admin(id_admin)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_pb_verif_dinas FOREIGN KEY (id_verifikator_dinas) REFERENCES admin(id_admin)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_pb_status (status_verifikasi_sekolah, status_verifikasi_dinas),
    INDEX idx_pb_sekolah (id_sekolah)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4a. TABEL PRAKTIK_BAIK_DOKUMEN
-- Dokumentasi pendukung sesuai BAB VII: foto, video, dokumen program,
-- data hasil, bukti perubahan, penghargaan, tautan publikasi, dokumen lain
-- ---------------------------------------------------------------------
CREATE TABLE praktik_baik_dokumen (
    id_dokumen      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_praktik_baik INT UNSIGNED NOT NULL,
    jenis_dokumen   ENUM(
                        'foto','video','dokumen_program','data_hasil',
                        'bukti_perubahan','penghargaan','tautan_publikasi',
                        'dokumen_pendukung_lainnya'
                    ) NOT NULL,
    nama_file       VARCHAR(255),
    path_file       VARCHAR(255) COMMENT 'path/URL file atau tautan publikasi',
    keterangan      VARCHAR(255),
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_dok_praktik_baik FOREIGN KEY (id_praktik_baik) REFERENCES praktik_baik(id_praktik_baik)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_dok_jenis (jenis_dokumen)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. TABEL BANK_INOVASI
-- Verifikasi & validasi oleh Satuan Pendidikan/Admin
-- ---------------------------------------------------------------------
CREATE TABLE bank_inovasi (
    id_inovasi          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_praktik_baik     INT UNSIGNED NULL COMMENT 'opsional, jika inovasi berasal dari praktik baik terverifikasi',
    id_sekolah          INT UNSIGNED NOT NULL,
    id_guru             INT UNSIGNED NULL,
    judul_inovasi       VARCHAR(200) NOT NULL,
    deskripsi           TEXT,
    status_verifikasi   ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
    id_verifikator      INT UNSIGNED NULL,
    tanggal_verifikasi  DATETIME NULL,
    tanggal_publish     DATETIME NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bi_praktik_baik FOREIGN KEY (id_praktik_baik) REFERENCES praktik_baik(id_praktik_baik)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_bi_sekolah FOREIGN KEY (id_sekolah) REFERENCES sekolah(id_sekolah)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_bi_guru FOREIGN KEY (id_guru) REFERENCES guru(id_guru)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_bi_verifikator FOREIGN KEY (id_verifikator) REFERENCES admin(id_admin)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_bi_status (status_verifikasi)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. TABEL KOMPETISI
-- Dibuat oleh petugas (dinas)
-- ---------------------------------------------------------------------
CREATE TABLE kompetisi (
    id_kompetisi    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kompetisi  VARCHAR(200) NOT NULL,
    deskripsi       TEXT,
    tanggal_mulai   DATE,
    tanggal_selesai DATE,
    status          ENUM('draft','pendaftaran','berlangsung','selesai') NOT NULL DEFAULT 'draft',
    id_admin_pembuat INT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_kompetisi_admin FOREIGN KEY (id_admin_pembuat) REFERENCES admin(id_admin)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6a. TABEL KOMPETISI_PESERTA
-- Validasi oleh panitia/juri
-- ---------------------------------------------------------------------
CREATE TABLE kompetisi_peserta (
    id_peserta          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_kompetisi        INT UNSIGNED NOT NULL,
    id_praktik_baik     INT UNSIGNED NULL COMMENT 'karya yang dilombakan, jika berasal dari praktik baik',
    id_sekolah          INT UNSIGNED NOT NULL,
    id_guru             INT UNSIGNED NOT NULL,
    nilai               DECIMAL(5,2) NULL,
    peringkat           INT UNSIGNED NULL,
    status_validasi     ENUM('menunggu','tervalidasi','ditolak') NOT NULL DEFAULT 'menunggu',
    id_juri_validator   INT UNSIGNED NULL,
    catatan_juri        TEXT,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_kp_kompetisi FOREIGN KEY (id_kompetisi) REFERENCES kompetisi(id_kompetisi)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_kp_praktik_baik FOREIGN KEY (id_praktik_baik) REFERENCES praktik_baik(id_praktik_baik)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_kp_sekolah FOREIGN KEY (id_sekolah) REFERENCES sekolah(id_sekolah)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_kp_guru FOREIGN KEY (id_guru) REFERENCES guru(id_guru)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_kp_juri FOREIGN KEY (id_juri_validator) REFERENCES admin(id_admin)
        ON DELETE SET NULL ON UPDATE CASCADE,
    UNIQUE KEY uq_peserta_per_kompetisi (id_kompetisi, id_guru),
    INDEX idx_kp_status (status_validasi)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. TABEL APRESIASI
-- Verifikasi bukti apresiasi oleh Satuan Pendidikan/Admin
-- ---------------------------------------------------------------------
CREATE TABLE apresiasi (
    id_apresiasi        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_sekolah          INT UNSIGNED NOT NULL,
    id_guru             INT UNSIGNED NULL,
    jenis_apresiasi     VARCHAR(150),
    deskripsi           TEXT,
    bukti_file          VARCHAR(255),
    status_verifikasi   ENUM('menunggu','diverifikasi','ditolak') NOT NULL DEFAULT 'menunggu',
    id_verifikator      INT UNSIGNED NULL,
    tanggal_verifikasi  DATETIME NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ap_sekolah FOREIGN KEY (id_sekolah) REFERENCES sekolah(id_sekolah)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ap_guru FOREIGN KEY (id_guru) REFERENCES guru(id_guru)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_ap_verifikator FOREIGN KEY (id_verifikator) REFERENCES admin(id_admin)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_ap_status (status_verifikasi)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 8. TABEL MONEV
-- Monitoring & evaluasi, diakses penanggung_jawab (read-only di sisi aplikasi)
-- ---------------------------------------------------------------------
CREATE TABLE monev (
    id_monev            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_sekolah          INT UNSIGNED NOT NULL,
    id_penanggung_jawab INT UNSIGNED NOT NULL,
    aspek_monev         VARCHAR(150),
    deskripsi           TEXT,
    hasil_temuan        TEXT,
    rekomendasi         TEXT,
    tanggal_monev       DATE,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_monev_sekolah FOREIGN KEY (id_sekolah) REFERENCES sekolah(id_sekolah)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_monev_pj FOREIGN KEY (id_penanggung_jawab) REFERENCES admin(id_admin)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 9. TABEL SUARA
-- Kanal masukan/aduan dari sekolah/guru/pengguna umum, ditindaklanjuti pengelola (petugas)
-- ---------------------------------------------------------------------
CREATE TABLE suara (
    id_suara                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_sekolah              INT UNSIGNED NULL,
    id_guru                 INT UNSIGNED NULL,
    nama_pengirim           VARCHAR(100),
    email_pengirim          VARCHAR(100),
    kategori                ENUM('saran','keluhan','pertanyaan','lainnya') NOT NULL DEFAULT 'lainnya',
    isi_suara               TEXT NOT NULL,
    status_tindak_lanjut    ENUM('belum_ditindak','diproses','selesai') NOT NULL DEFAULT 'belum_ditindak',
    tanggapan               TEXT,
    id_admin_penindak       INT UNSIGNED NULL,
    tanggal_kirim           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tanggal_tindak_lanjut   DATETIME NULL,
    CONSTRAINT fk_suara_sekolah FOREIGN KEY (id_sekolah) REFERENCES sekolah(id_sekolah)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_suara_guru FOREIGN KEY (id_guru) REFERENCES guru(id_guru)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_suara_admin FOREIGN KEY (id_admin_penindak) REFERENCES admin(id_admin)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_suara_status (status_tindak_lanjut)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
