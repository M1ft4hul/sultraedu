-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Sep 2026 pada 13.57
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sultra_eduvation`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(10) UNSIGNED NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'simpan hash (bcrypt/argon2), bukan plain text',
  `role` enum('admin_pusat','admin_sekolah','tim_juri','guru') NOT NULL,
  `id_sekolah` int(10) UNSIGNED DEFAULT NULL COMMENT 'hanya diisi jika role = admin_sekolah',
  `email` varchar(100) DEFAULT NULL,
  `keterangan` varchar(150) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `username`, `password`, `role`, `id_sekolah`, `email`, `keterangan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Muh Syamdudin syawal', 'adminpusat', '$2y$10$c6LW2r9xPIv04VkAs0W46e/VSgA071Vcf8KBcCPJzPU4eJfP7iTha', 'admin_pusat', NULL, 'admin@eduvation.sultraprov.go.id', NULL, 'aktif', '2026-09-24 23:49:23', '2026-09-29 00:37:30'),
(7, 'Admin SLBS B-F Mandara', '40403980', '$2y$10$kur/D.OTYWjYlHMcDYy3XugjCw9yFt6Q0H8CLdAKNzj2pvbwvsspe', 'admin_sekolah', 3, 'slbmandara1@gmail.com', NULL, 'aktif', '2026-09-28 06:44:35', '2026-09-29 21:53:28'),
(9, 'Yauma Neuvilete', '40402627', '$2y$10$kmg4B70BrWfkHyeodR94LOkTWp/aQx4gxGLKc7tjWOGeW3qYb/E12', 'admin_sekolah', 2, 'smknegeri4kdi@gmail.com', NULL, 'aktif', '2026-09-28 06:44:35', '2026-09-29 02:42:16'),
(10, 'Admin SMKN 2 Kendari', '40402625', '$2y$10$YTfDZm.ZxJQHzAPIanZ5m.TlLzy92l4rcNda6pdjCN9XPXFPigDbC', 'admin_sekolah', 1, 'stmkdi@gmail.com', NULL, 'aktif', '2026-09-28 06:47:56', '2026-09-30 05:51:37'),
(13, 'Juri Uji', 'juriuji', '-', 'tim_juri', NULL, NULL, NULL, 'aktif', '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(14, 'Samsudin', 'samsu_din', '$2y$10$insrukWK7dLLdD6AOzq6Tu.nIF4/PMgxYskguPNKrrIX0Dn8ITkz2', 'admin_pusat', NULL, 'samsu@gmail.com', NULL, 'aktif', '2026-09-29 01:53:42', '2026-09-29 01:53:42'),
(15, 'Miftahul Jannah', 'mjcutter', '$2y$10$c/R17yXef1nth0qYz2Qaa.YwoZN8N9q0yPfU6uvVvg3jPi7qA.Nd.', 'admin_sekolah', 2, 'smknegeri4kdi@gmail.com', NULL, 'aktif', '2026-09-29 05:18:05', '2026-09-29 05:18:05'),
(16, 'Dr. Hasniah, M.Pd.', 'juri01', '$2y$10$hymkaMwVgyIL8QmYTAIJe.Y2pFJMuGYS/BJUNhiq/yxegQqxn0HSC', 'tim_juri', NULL, 'juri01@eduvation.sultraprov.go.id', 'Dosen Teknologi Pendidikan UHO', 'aktif', '2026-09-30 17:18:51', '2026-09-30 10:05:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `apresiasi`
--

CREATE TABLE `apresiasi` (
  `id_apresiasi` int(10) UNSIGNED NOT NULL,
  `id_peserta` int(10) UNSIGNED DEFAULT NULL,
  `id_sekolah` int(10) UNSIGNED NOT NULL,
  `id_guru` int(10) UNSIGNED DEFAULT NULL,
  `jenis_apresiasi` varchar(150) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `bukti_file` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('menunggu','diverifikasi','ditolak') NOT NULL DEFAULT 'menunggu',
  `id_verifikator` int(10) UNSIGNED DEFAULT NULL,
  `tanggal_verifikasi` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `apresiasi`
--

INSERT INTO `apresiasi` (`id_apresiasi`, `id_peserta`, `id_sekolah`, `id_guru`, `jenis_apresiasi`, `deskripsi`, `bukti_file`, `status_verifikasi`, `id_verifikator`, `tanggal_verifikasi`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 2, 'Juara 1 – Transformasi Digital Pembelajaran & Manajemen Sekolah', 'Karya Uji A', NULL, 'diverifikasi', 1, '2026-09-28 23:39:38', '2026-09-28 23:39:38', '2026-09-28 23:39:38'),
(2, 6, 2, 3, 'Juara 2 – Transformasi Digital Pembelajaran & Manajemen Sekolah', 'Karya Uji B', 'uploads/piagam/1790726719_7bbb253b7b07c199b406.png', 'diverifikasi', 1, '2026-09-28 23:39:38', '2026-09-28 23:39:38', '2026-09-30 00:05:19'),
(3, 7, 3, 4, 'Juara 3 – Transformasi Digital Pembelajaran & Manajemen Sekolah', 'Karya Uji C', NULL, 'diverifikasi', 1, '2026-09-28 23:39:38', '2026-09-28 23:39:38', '2026-09-28 23:39:38'),
(4, 13, 3, 4, 'Juara 1 – Pemerataan Akses & Inklusi Pendidikan', 'Taman Sensorik Tanaman Herbal Lokal', 'uploads/piagam/1790726555_3da041e69e9e5a93aaeb.png', 'diverifikasi', 1, '2026-09-29 15:06:02', '2026-09-29 15:06:02', '2026-09-30 00:02:35'),
(5, 14, 1, 2, 'Juara 2 – Pemerataan Akses & Inklusi Pendidikan', 'Jalur Tekstur Alam dari Limbah Bengkel', 'uploads/piagam/1790726693_a0c51b284d4519392cf2.png', 'diverifikasi', 1, '2026-09-29 15:06:02', '2026-09-29 15:06:02', '2026-09-30 00:04:53'),
(6, 15, 3, 11, 'Juara 3 – Pemerataan Akses & Inklusi Pendidikan', 'Kebun Aroma Terapi untuk Siswa Autis', 'uploads/piagam/1790745071_0918df1daf6529aee4fc.png', 'diverifikasi', 1, '2026-09-29 15:06:02', '2026-09-29 15:06:02', '2026-09-30 05:11:11'),
(7, 19, 2, 3, 'Juara 1 – Inovasi Pembelajaran Efektif & Potensi Peserta Didik', 'Eksplorasi Potensi Peserta Didik melalui Pembelajaran Inovatif', NULL, 'diverifikasi', 1, '2026-09-30 11:17:37', '2026-09-30 11:17:37', '2026-09-30 11:17:37'),
(8, 18, 3, 4, 'Juara 2 – Inovasi Pembelajaran Efektif & Potensi Peserta Didik', 'Optimalisasi Potensi Peserta Didik melalui Pembelajaran Kreatif dan Interaktif', NULL, 'diverifikasi', 1, '2026-09-30 11:17:37', '2026-09-30 11:17:37', '2026-09-30 11:17:37'),
(9, 20, 1, 2, 'Juara 3 – Inovasi Pembelajaran Efektif & Potensi Peserta Didik', 'Personalized Learning: Pembelajaran Efektif Sesuai Potensi Peserta Didik', NULL, 'diverifikasi', 1, '2026-09-30 11:17:37', '2026-09-30 11:17:37', '2026-09-30 11:17:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bank_inovasi`
--

CREATE TABLE `bank_inovasi` (
  `id_inovasi` int(10) UNSIGNED NOT NULL,
  `id_praktik_baik` int(10) UNSIGNED DEFAULT NULL COMMENT 'opsional, jika inovasi berasal dari praktik baik terverifikasi',
  `id_sekolah` int(10) UNSIGNED NOT NULL,
  `id_guru` int(10) UNSIGNED DEFAULT NULL,
  `judul_inovasi` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status_verifikasi_sekolah` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `id_verifikator_sekolah` int(10) UNSIGNED DEFAULT NULL,
  `catatan_sekolah` text DEFAULT NULL,
  `tanggal_verifikasi_sekolah` datetime DEFAULT NULL,
  `status_verifikasi` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `id_verifikator` int(10) UNSIGNED DEFAULT NULL,
  `catatan_verifikasi` text DEFAULT NULL,
  `catatan_revisi` text DEFAULT NULL,
  `jumlah_revisi` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `tanggal_revisi` datetime DEFAULT NULL,
  `tanggal_verifikasi` datetime DEFAULT NULL,
  `tanggal_publish` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bank_inovasi`
--

INSERT INTO `bank_inovasi` (`id_inovasi`, `id_praktik_baik`, `id_sekolah`, `id_guru`, `judul_inovasi`, `deskripsi`, `status_verifikasi_sekolah`, `id_verifikator_sekolah`, `catatan_sekolah`, `tanggal_verifikasi_sekolah`, `status_verifikasi`, `id_verifikator`, `catatan_verifikasi`, `catatan_revisi`, `jumlah_revisi`, `tanggal_revisi`, `tanggal_verifikasi`, `tanggal_publish`, `created_at`, `updated_at`) VALUES
(1, 5, 2, 3, 'Aplikasi Antrean Servis Bengkel Sekolah', 'Pengembangan dari praktik baik bengkel mini. Pelanggan mendaftar antrean servis melalui aplikasi web, lalu memantau status perbaikan secara real-time.\n\nWaktu tunggu pelanggan turun dari rata-rata 45 menit menjadi 15 menit.', 'menunggu', NULL, NULL, NULL, 'menunggu', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-09-24 10:42:47', '2026-09-24 10:42:47'),
(2, NULL, 2, 3, 'Simulator Kelistrikan Otomotif Berbasis Arduino', 'Papan simulasi kelistrikan bodi kendaraan yang dapat diprogram untuk memunculkan kerusakan tertentu, sehingga siswa berlatih diagnosis tanpa memerlukan kendaraan asli.', 'disetujui', 9, 'silahkan lanjutkan tugasnya', '2026-09-29 23:42:44', 'menunggu', NULL, NULL, NULL, 0, NULL, NULL, NULL, '2026-09-25 10:42:47', '2026-09-29 23:42:44'),
(3, 6, 2, 3, 'Buku Saku Digital Nilai Karakter Bengkel', 'Pengembangan dari kegiatan Jumat Mengaji: buku saku digital berisi 30 nilai karakter yang dikaitkan langsung dengan etika kerja di bengkel, dapat diakses melalui kode QR di setiap stan kerja.', 'disetujui', 9, 'okee lanjutkan', '2026-09-29 04:30:09', 'disetujui', 1, 'okee mantap', NULL, 0, NULL, '2026-09-29 04:30:40', '2026-09-29 04:30:40', '2026-09-26 10:42:47', '2026-09-29 04:30:40'),
(4, NULL, 2, 3, 'Kartu Kompetensi Siswa Berbasis QR', 'Setiap siswa memiliki kartu dengan kode QR yang menampilkan daftar kompetensi yang sudah dikuasai beserta tanda tangan digital guru pembimbing. Mitra industri dapat memindai kartu saat rekrutmen prakerin.', 'disetujui', 9, 'di acc tapi harus lengkapi dokumen', '2026-09-29 02:47:58', 'disetujui', 1, 'okeee lanjutkan', NULL, 0, NULL, '2026-09-29 02:48:31', '2026-09-29 02:48:31', '2026-09-28 10:42:47', '2026-09-29 02:48:31'),
(5, NULL, 2, 3, 'Panel Surya Mini untuk Praktik Kelistrikan Ramah Lingkungan', 'Rangkaian panel surya skala kecil sebagai sumber listrik alat praktik, sekaligus media belajar energi terbarukan bagi siswa.', 'disetujui', 9, 'okee mantap', '2026-09-30 10:09:34', 'menunggu', NULL, NULL, 'pak ini inovasi baru pak', 1, '2026-09-30 05:22:09', NULL, NULL, '2026-09-29 05:42:47', '2026-09-30 10:09:34'),
(6, 4, 3, 4, 'Harmoni Inklusif: Inovasi Musik Tradisional bagi Siswa Berkebutuhan Khusus', 'Pemanfaatan musik tradisional sebagai media pembelajaran yang inklusif bagi siswa berkebutuhan khusus. Kegiatan ini bertujuan meningkatkan kreativitas, ekspresi diri, dan keterlibatan siswa melalui pengalaman bermusik yang menyenangkan dan adaptif.', 'disetujui', 7, 'okee mantap. silahkan lanjutkan', '2026-09-30 03:55:41', 'disetujui', 1, 'okeee lanjutkan', NULL, 0, NULL, '2026-09-30 04:01:15', '2026-09-30 04:01:15', '2026-09-30 03:53:48', '2026-09-30 04:01:15'),
(7, NULL, 3, 4, 'Smart Attendance — absensi siswa berbasis QR Code atau pengenalan wajah', 'Pengembangan sistem absensi siswa berbasis QR Code atau pengenalan wajah untuk mempermudah proses pencatatan kehadiran secara cepat dan akurat. Sistem ini juga membantu guru dalam memantau serta mengelola data kehadiran siswa secara digital.', 'disetujui', 7, 'baik.. silahkan untuk bapak baca baik\" inovasi dari guru saya', '2026-09-30 04:29:58', 'disetujui', 1, 'ooh iyaa. bagus inovasimu.. semnagat', 'apa yang mau di revisi pak. saya pikir semuanya sudah jelas yah. bapak tolong baca baik\" inovasi saya', 1, '2026-09-30 04:29:09', '2026-09-30 04:30:30', '2026-09-30 04:30:30', '2026-09-30 03:54:41', '2026-09-30 04:30:30'),
(8, NULL, 2, 3, 'Absensi menggunakan AI', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.', 'disetujui', 9, 'accc', '2026-09-30 09:02:47', 'disetujui', 1, 'okee kerjakan', NULL, 0, NULL, '2026-09-30 09:03:14', '2026-09-30 09:03:14', '2026-09-30 09:01:30', '2026-09-30 09:03:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `id_guru` int(10) UNSIGNED NOT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `nama_guru` varchar(100) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `id_sekolah` int(10) UNSIGNED NOT NULL COMMENT 'asal_sekolah',
  `mapel` varchar(100) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `guru`
--

INSERT INTO `guru` (`id_guru`, `nip`, `nama_guru`, `username`, `password`, `id_sekolah`, `mapel`, `status`, `created_at`, `updated_at`) VALUES
(1, '7273248324734', 'Miftahul Jannah', 'Miftah', '$2y$10$7B2dnPsOAvH3151sfQ7g7uaqFu7tfw2ICQ6s8rwWiodDl6EqVbpbS', 1, 'Rekayasa Prangkat Lunak', 'aktif', '2026-09-28 15:17:41', '2026-09-28 15:17:41'),
(2, '35443534534', 'Shultonul Ma\'arif', 'guruuji1', '$2y$10$g9FTxFmbbpANCje2bg0MHeX..3LCVjQLr0gZLl/pZKcu9v3ojrhKi', 1, 'Kimia', 'aktif', '2026-09-29 07:36:58', '2026-09-30 05:54:20'),
(3, '474634345345', 'Okta Mada', 'guruuji2', '$2y$10$lDooYNArdcFhs4.OtdQpEOSQMHUrSKFOnyBwrwSRlG7akQ75L24Za', 2, 'Matematika', 'aktif', '2026-09-29 07:36:58', '2026-09-30 11:27:21'),
(4, '432423432434', 'Robiatul Adawiya', 'guruuji3', '$2y$10$GhLBGZIa09UUl3rAg1Xi.u2LGNtZ5shaQErntDGAI9es9myglm.3a', 3, 'Bahasa Inggris', 'aktif', '2026-09-29 07:36:58', '2026-09-30 11:33:38'),
(5, '197803142005012007', 'Hj. Nurhayati, S.Pd., M.Pd.', 'hayati', '$2y$10$ENwAHDNC3KFMY1befYzpeeobQMwbuh.GLqW8QhBmHo3TJZBl.K3iu', 2, 'Bahasa Indonesia', 'aktif', '2026-09-29 13:36:49', '2026-09-29 14:09:09'),
(6, '198511062010011019', 'Andi Firmansyah, S.T.', 'firman', '$2y$10$aOmZztTLsZKRxFGOVLAKPOmWu3xoZbu/jNmpSZjdWKtxpZsgyK0Ze', 2, 'Teknik Kendaraan Ringan', 'aktif', '2026-09-29 13:36:49', '2026-09-29 05:48:01'),
(7, '199002232019032011', 'Wa Ode Sitti Rahmah, S.Pd.', NULL, NULL, 2, 'Matematika', 'aktif', '2026-09-29 13:36:49', '2026-09-29 13:36:49'),
(8, '198207192009021004', 'La Ode Muh. Ikhsan, S.Kom.', 'ikhsan', '$2y$10$S758a3kpUwDVvxE6KWOoEel1LwVIMfOfOGeinPe0yI5fworMQ6QUW', 2, 'Rekayasa Perangkat Lunak', 'aktif', '2026-09-29 13:36:49', '2026-09-30 03:51:58'),
(9, '199405302022012015', 'Dewi Kartika Sari, S.Pd.', 'rahma', '$2y$10$Ko.ZMDHyA/2aTkRvdmRJD.YeOp7jLRYY1zqh6A.Hwp1IzeWuBCdF6', 2, 'Bahasa Inggris', 'aktif', '2026-09-29 13:36:49', '2026-09-29 05:42:55'),
(10, '4172659', 'Rini Andriani, S.Pd.', NULL, NULL, 2, 'Dasar Program Keahlian', 'aktif', '2026-09-29 22:57:36', '2026-09-29 22:57:36'),
(11, '3928416', 'Sitti Aminah, S.Pd.', 'aminah', '$2y$10$S0T1yQua7Ha1bvbf2Zgy8ewSEv7geULVUCOWhIg9CxCwwr1qcXUuS', 3, 'Bina Diri', 'aktif', '2026-09-29 23:05:04', '2026-09-29 21:56:19'),
(12, '734634823743834', 'Kartina', 'kartina', '$2y$10$pPcW0PI6yYGkcU1oa1WsXuuePBGGkxaHxSMlQiE6.J62gjIx5krEC', 3, 'Bahasa Jepang', 'aktif', '2026-09-29 21:56:03', '2026-09-29 21:56:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `juri_penugasan`
--

CREATE TABLE `juri_penugasan` (
  `id_penugasan` int(10) UNSIGNED NOT NULL,
  `id_juri` int(10) UNSIGNED NOT NULL,
  `id_kategori` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `juri_penugasan`
--

INSERT INTO `juri_penugasan` (`id_penugasan`, `id_juri`, `id_kategori`, `created_at`) VALUES
(1, 16, 6, '2026-09-30 09:50:52'),
(2, 13, 7, '2026-09-30 11:36:04'),
(3, 16, 7, '2026-09-30 11:36:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kompetisi`
--

CREATE TABLE `kompetisi` (
  `id_kompetisi` int(10) UNSIGNED NOT NULL,
  `nama_kompetisi` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('draft','pendaftaran','berlangsung','selesai') NOT NULL DEFAULT 'draft',
  `hasil_diumumkan` tinyint(1) NOT NULL DEFAULT 0,
  `tanggal_pengumuman` datetime DEFAULT NULL,
  `id_admin_pembuat` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kompetisi`
--

INSERT INTO `kompetisi` (`id_kompetisi`, `nama_kompetisi`, `deskripsi`, `tanggal_mulai`, `tanggal_selesai`, `status`, `hasil_diumumkan`, `tanggal_pengumuman`, `id_admin_pembuat`, `created_at`, `updated_at`) VALUES
(1, 'Kompetisi Inovasi Pendidikan 2026', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.', '2026-09-28', '2026-10-28', 'selesai', 1, '2026-09-28 23:39:38', 1, '2026-09-28 08:04:15', '2026-09-30 11:20:17'),
(2, 'Sensory garden terapi inklusi', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like)', '2026-09-30', '2026-10-30', 'selesai', 1, '2026-09-29 15:06:02', 1, '2026-09-28 08:09:43', '2026-09-29 15:06:02'),
(4, 'Kompetisi Guru Inovatif', 'Kompetisi yang mendorong guru untuk menciptakan dan menerapkan inovasi pembelajaran yang kreatif, efektif, dan sesuai dengan kebutuhan peserta didik. Kegiatan ini bertujuan meningkatkan kualitas pembelajaran serta mengembangkan potensi dan kreativitas siswa.', '2026-09-30', '2026-10-10', 'selesai', 1, '2026-09-30 11:17:37', 1, '2026-09-29 01:40:08', '2026-09-30 11:17:37'),
(5, 'Pembelajaran Berbasis Proyek untuk Mengembangkan Kreativitas Siswa', 'Inovasi pembelajaran yang melibatkan peserta didik dalam mengerjakan proyek secara aktif dan kreatif. Kegiatan ini mendorong siswa untuk mengembangkan ide, memecahkan masalah, bekerja sama, serta menghasilkan karya nyata sesuai dengan minat dan kemampuan mereka.', '2026-09-25', '2026-10-02', 'berlangsung', 0, NULL, 1, '2026-09-30 11:22:54', '2026-09-30 11:45:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kompetisi_kategori`
--

CREATE TABLE `kompetisi_kategori` (
  `id_kategori` int(10) UNSIGNED NOT NULL,
  `id_kompetisi` int(10) UNSIGNED NOT NULL,
  `nama_kategori` varchar(150) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `urutan` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kompetisi_kategori`
--

INSERT INTO `kompetisi_kategori` (`id_kategori`, `id_kompetisi`, `nama_kategori`, `deskripsi`, `urutan`, `created_at`) VALUES
(1, 1, 'Transformasi Digital Pembelajaran & Manajemen Sekolah', NULL, 1, '2026-09-28 16:04:15'),
(5, 2, 'Pemerataan Akses & Inklusi Pendidikan', NULL, 1, '2026-09-29 09:39:31'),
(6, 4, 'Inovasi Pembelajaran Efektif & Potensi Peserta Didik', NULL, 1, '2026-09-29 09:40:08'),
(7, 5, 'Inovasi Pembelajaran Efektif &amp; Potensi Peserta Didik', NULL, 1, '2026-09-30 19:22:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kompetisi_kriteria`
--

CREATE TABLE `kompetisi_kriteria` (
  `id_kriteria` int(10) UNSIGNED NOT NULL,
  `id_kompetisi` int(10) UNSIGNED NOT NULL,
  `nama_kriteria` varchar(150) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `skor_maks` tinyint(3) UNSIGNED NOT NULL,
  `urutan` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kompetisi_kriteria`
--

INSERT INTO `kompetisi_kriteria` (`id_kriteria`, `id_kompetisi`, `nama_kriteria`, `keterangan`, `skor_maks`, `urutan`) VALUES
(1, 1, 'Relevansi Permasalahan', 'Kesesuaian solusi dengan masalah nyata sekolah', 15, 1),
(2, 1, 'Kebaruan / Inovasi', 'Unsur kebaruan dan nilai keunikan strategi', 20, 2),
(3, 1, 'Efektivitas & Hasil', 'Capaian target dan efektivitas implementasi', 20, 3),
(4, 1, 'Manfaat & Dampak', 'Dampak bagi peserta didik & satuan pendidikan', 15, 4),
(5, 1, 'Keberlanjutan', 'Potensi inovasi terus berjalan jangka panjang', 10, 5),
(6, 1, 'Potensi Replikasi', 'Kemudahan diadopsi oleh sekolah lain', 10, 6),
(7, 1, 'Dokumentasi & Video', 'Kejelasan penyajian video 3 menit #sultraeduvation', 10, 7),
(23, 2, 'Relevansi Permasalahan', 'Kesesuaian solusi dengan masalah nyata sekolah', 15, 1),
(24, 2, 'Kebaruan / Inovasi', 'Unsur kebaruan dan nilai keunikan strategi', 20, 2),
(25, 2, 'Efektivitas & Hasil', 'Capaian target dan efektivitas implementasi', 20, 3),
(26, 2, 'Manfaat & Dampak', 'Dampak bagi peserta didik & satuan pendidikan', 15, 4),
(27, 2, 'Keberlanjutan', 'Potensi inovasi terus berjalan jangka panjang', 10, 5),
(28, 2, 'Potensi Replikasi', 'Kemudahan diadopsi oleh sekolah lain', 10, 6),
(29, 2, 'Dokumentasi & Video', 'Kejelasan penyajian video 3 menit #sultraeduvation', 10, 7),
(30, 4, 'Relevansi Permasalahan', 'Kesesuaian solusi dengan masalah nyata sekolah', 15, 1),
(31, 4, 'Kebaruan / Inovasi', 'Unsur kebaruan dan nilai keunikan strategi', 20, 2),
(32, 4, 'Efektivitas & Hasil', 'Capaian target dan efektivitas implementasi', 20, 3),
(33, 4, 'Manfaat & Dampak', 'Dampak bagi peserta didik & satuan pendidikan', 15, 4),
(34, 4, 'Keberlanjutan', 'Potensi inovasi terus berjalan jangka panjang', 10, 5),
(35, 4, 'Potensi Replikasi', 'Kemudahan diadopsi oleh sekolah lain', 10, 6),
(36, 4, 'Dokumentasi & Video', 'Kejelasan penyajian video 3 menit #sultraeduvation', 10, 7),
(37, 5, 'Relevansi Permasalahan', 'Kesesuaian solusi dengan masalah nyata sekolah', 15, 1),
(38, 5, 'Kebaruan / Inovasi', 'Unsur kebaruan dan nilai keunikan strategi', 20, 2),
(39, 5, 'Efektivitas &amp; Hasil', 'Capaian target dan efektivitas implementasi', 20, 3),
(40, 5, 'Manfaat &amp; Dampak', 'Dampak bagi peserta didik &amp; satuan pendidikan', 15, 4),
(41, 5, 'Keberlanjutan', 'Potensi inovasi terus berjalan jangka panjang', 10, 5),
(42, 5, 'Potensi Replikasi', 'Kemudahan diadopsi oleh sekolah lain', 10, 6),
(43, 5, 'Dokumentasi &amp; Video', 'Kejelasan penyajian video 3 menit #sultraeduvation', 10, 7);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kompetisi_nilai`
--

CREATE TABLE `kompetisi_nilai` (
  `id_nilai` int(10) UNSIGNED NOT NULL,
  `id_peserta` int(10) UNSIGNED NOT NULL,
  `id_kriteria` int(10) UNSIGNED NOT NULL,
  `id_juri` int(10) UNSIGNED NOT NULL,
  `skor` decimal(5,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kompetisi_nilai`
--

INSERT INTO `kompetisi_nilai` (`id_nilai`, `id_peserta`, `id_kriteria`, `id_juri`, `skor`, `created_at`, `updated_at`) VALUES
(15, 5, 1, 13, 13.50, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(16, 5, 2, 13, 18.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(17, 5, 3, 13, 18.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(18, 5, 4, 13, 13.50, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(19, 5, 5, 13, 9.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(20, 5, 6, 13, 9.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(21, 5, 7, 13, 9.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(22, 6, 1, 13, 12.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(23, 6, 2, 13, 16.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(24, 6, 3, 13, 16.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(25, 6, 4, 13, 12.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(26, 6, 5, 13, 8.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(27, 6, 6, 13, 8.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(28, 6, 7, 13, 8.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(29, 7, 1, 13, 9.75, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(30, 7, 2, 13, 13.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(31, 7, 3, 13, 13.00, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(32, 7, 4, 13, 9.75, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(33, 7, 5, 13, 6.50, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(34, 7, 6, 13, 6.50, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(35, 7, 7, 13, 6.50, '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(36, 8, 1, 13, 9.30, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(37, 8, 2, 13, 12.40, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(38, 8, 3, 13, 12.40, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(39, 8, 4, 13, 9.30, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(40, 8, 5, 13, 6.20, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(41, 8, 6, 13, 6.20, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(42, 8, 7, 13, 6.20, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(43, 9, 1, 13, 8.70, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(44, 9, 2, 13, 11.60, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(45, 9, 3, 13, 11.60, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(46, 9, 4, 13, 8.70, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(47, 9, 5, 13, 5.80, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(48, 9, 6, 13, 5.80, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(49, 9, 7, 13, 5.80, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(50, 10, 1, 13, 8.25, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(51, 10, 2, 13, 11.00, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(52, 10, 3, 13, 11.00, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(53, 10, 4, 13, 8.25, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(54, 10, 5, 13, 5.50, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(55, 10, 6, 13, 5.50, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(56, 10, 7, 13, 5.50, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(57, 11, 1, 13, 7.50, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(58, 11, 2, 13, 10.00, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(59, 11, 3, 13, 10.00, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(60, 11, 4, 13, 7.50, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(61, 11, 5, 13, 5.00, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(62, 11, 6, 13, 5.00, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(63, 11, 7, 13, 5.00, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(64, 12, 1, 13, 6.75, '2026-09-29 22:57:36', '2026-09-29 22:57:36'),
(65, 12, 2, 13, 9.00, '2026-09-29 22:57:36', '2026-09-29 22:57:36'),
(66, 12, 3, 13, 9.00, '2026-09-29 22:57:36', '2026-09-29 22:57:36'),
(67, 12, 4, 13, 6.75, '2026-09-29 22:57:36', '2026-09-29 22:57:36'),
(68, 12, 5, 13, 4.50, '2026-09-29 22:57:36', '2026-09-29 22:57:36'),
(69, 12, 6, 13, 4.50, '2026-09-29 22:57:36', '2026-09-29 22:57:36'),
(70, 12, 7, 13, 4.50, '2026-09-29 22:57:36', '2026-09-29 22:57:36'),
(71, 13, 23, 13, 13.80, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(72, 13, 24, 13, 18.40, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(73, 13, 25, 13, 18.40, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(74, 13, 26, 13, 13.80, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(75, 13, 27, 13, 9.20, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(76, 13, 28, 13, 9.20, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(77, 13, 29, 13, 9.20, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(78, 14, 23, 13, 11.70, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(79, 14, 24, 13, 15.60, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(80, 14, 25, 13, 15.60, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(81, 14, 26, 13, 11.70, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(82, 14, 27, 13, 7.80, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(83, 14, 28, 13, 7.80, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(84, 14, 29, 13, 7.80, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(85, 15, 23, 13, 10.50, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(86, 15, 24, 13, 14.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(87, 15, 25, 13, 14.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(88, 15, 26, 13, 10.50, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(89, 15, 27, 13, 7.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(90, 15, 28, 13, 7.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(91, 15, 29, 13, 7.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(92, 16, 23, 13, 9.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(93, 16, 24, 13, 12.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(94, 16, 25, 13, 12.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(95, 16, 26, 13, 9.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(96, 16, 27, 13, 6.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(97, 16, 28, 13, 6.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(98, 16, 29, 13, 6.00, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(99, 17, 23, 13, 7.80, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(100, 17, 24, 13, 10.40, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(101, 17, 25, 13, 10.40, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(102, 17, 26, 13, 7.80, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(103, 17, 27, 13, 5.20, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(104, 17, 28, 13, 5.20, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(105, 17, 29, 13, 5.20, '2026-09-29 23:05:04', '2026-09-29 23:05:04'),
(106, 18, 30, 16, 53.00, '2026-09-30 11:12:48', '2026-09-30 11:13:34'),
(107, 18, 31, 16, 70.00, '2026-09-30 11:12:48', '2026-09-30 11:13:34'),
(108, 18, 32, 16, 83.00, '2026-09-30 11:12:48', '2026-09-30 11:13:34'),
(109, 18, 33, 16, 61.00, '2026-09-30 11:12:48', '2026-09-30 11:13:34'),
(110, 18, 34, 16, 33.00, '2026-09-30 11:12:48', '2026-09-30 11:13:34'),
(111, 18, 36, 16, 66.00, '2026-09-30 11:12:48', '2026-09-30 11:13:34'),
(112, 19, 30, 16, 76.00, '2026-09-30 11:13:18', '2026-09-30 11:13:18'),
(113, 19, 31, 16, 78.00, '2026-09-30 11:13:18', '2026-09-30 11:13:18'),
(114, 19, 32, 16, 86.00, '2026-09-30 11:13:18', '2026-09-30 11:13:18'),
(115, 19, 33, 16, 86.00, '2026-09-30 11:13:18', '2026-09-30 11:13:18'),
(116, 19, 34, 16, 80.00, '2026-09-30 11:13:18', '2026-09-30 11:13:18'),
(117, 19, 35, 16, 81.00, '2026-09-30 11:13:18', '2026-09-30 11:13:18'),
(118, 19, 36, 16, 75.00, '2026-09-30 11:13:18', '2026-09-30 11:13:18'),
(119, 18, 35, 16, 77.00, '2026-09-30 11:13:34', '2026-09-30 11:13:34'),
(120, 20, 30, 16, 28.00, '2026-09-30 11:13:54', '2026-09-30 11:13:54'),
(121, 20, 31, 16, 26.00, '2026-09-30 11:13:54', '2026-09-30 11:13:54'),
(122, 20, 32, 16, 85.00, '2026-09-30 11:13:54', '2026-09-30 11:13:54'),
(123, 20, 33, 16, 88.00, '2026-09-30 11:13:54', '2026-09-30 11:13:54'),
(124, 20, 34, 16, 83.00, '2026-09-30 11:13:54', '2026-09-30 11:13:54'),
(125, 20, 35, 16, 74.00, '2026-09-30 11:13:54', '2026-09-30 11:13:54'),
(126, 20, 36, 16, 79.00, '2026-09-30 11:13:54', '2026-09-30 11:13:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kompetisi_peserta`
--

CREATE TABLE `kompetisi_peserta` (
  `id_peserta` int(10) UNSIGNED NOT NULL,
  `id_kompetisi` int(10) UNSIGNED NOT NULL,
  `id_kategori` int(10) UNSIGNED DEFAULT NULL,
  `id_praktik_baik` int(10) UNSIGNED DEFAULT NULL COMMENT 'karya yang dilombakan, jika berasal dari praktik baik',
  `id_sekolah` int(10) UNSIGNED NOT NULL,
  `id_guru` int(10) UNSIGNED NOT NULL,
  `judul_karya` varchar(200) NOT NULL,
  `deskripsi_karya` text DEFAULT NULL,
  `link_video` varchar(255) DEFAULT NULL,
  `nilai` decimal(5,2) DEFAULT NULL,
  `peringkat` int(10) UNSIGNED DEFAULT NULL,
  `status_validasi` enum('menunggu','tervalidasi','ditolak') NOT NULL DEFAULT 'menunggu',
  `id_juri_validator` int(10) UNSIGNED DEFAULT NULL,
  `catatan_juri` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kompetisi_peserta`
--

INSERT INTO `kompetisi_peserta` (`id_peserta`, `id_kompetisi`, `id_kategori`, `id_praktik_baik`, `id_sekolah`, `id_guru`, `judul_karya`, `deskripsi_karya`, `link_video`, `nilai`, `peringkat`, `status_validasi`, `id_juri_validator`, `catatan_juri`, `created_at`, `updated_at`) VALUES
(5, 1, 1, NULL, 1, 2, 'Karya Uji A', NULL, 'https://youtu.be/uji-a', 90.00, 1, 'tervalidasi', NULL, NULL, '2026-09-29 07:36:58', '2026-09-29 07:39:38'),
(6, 1, 1, NULL, 2, 3, 'Karya Uji B', NULL, 'https://youtu.be/uji-b', 80.00, 2, 'tervalidasi', NULL, NULL, '2026-09-29 07:36:58', '2026-09-29 07:39:38'),
(7, 1, 1, NULL, 3, 4, 'Karya Uji C', NULL, 'https://youtu.be/uji-c', 65.00, 3, 'tervalidasi', NULL, NULL, '2026-09-29 07:36:58', '2026-09-29 07:39:38'),
(8, 1, 1, NULL, 2, 5, 'Modul Literasi Otomotif Berbasis Cerita Rakyat Tolaki', 'Modul bacaan yang mengenalkan istilah teknik otomotif melalui cerita rakyat Tolaki, untuk meningkatkan minat baca siswa SMK.', 'https://youtu.be/uji-literasi-otomotif', 62.00, NULL, 'tervalidasi', NULL, NULL, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(9, 1, 1, NULL, 2, 6, 'Aplikasi Kuis Diagnosis Mesin Berbasis Android', 'Aplikasi kuis interaktif berisi studi kasus kerusakan mesin, dilengkapi suara mesin asli sebagai petunjuk diagnosis.', 'https://youtu.be/uji-kuis-diagnosis', 58.00, NULL, 'tervalidasi', NULL, NULL, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(10, 1, 1, NULL, 2, 7, 'Media Pembelajaran Matematika Berbasis Kunci Pas', 'Mengajarkan konsep pecahan dan skala menggunakan ukuran kunci pas di bengkel, sehingga matematika terasa dekat dengan dunia kerja siswa.', 'https://youtu.be/uji-matematika-kunci-pas', 55.00, NULL, 'tervalidasi', NULL, NULL, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(11, 1, 1, NULL, 2, 8, 'Sistem Absensi Bengkel dengan RFID', 'Kartu RFID untuk mencatat kehadiran siswa di bengkel praktik sekaligus peminjaman alat, sehingga alat yang hilang bisa dilacak.', 'https://youtu.be/uji-absensi-rfid', 50.00, NULL, 'tervalidasi', NULL, NULL, '2026-09-29 14:13:19', '2026-09-29 14:13:19'),
(12, 1, 1, NULL, 2, 10, 'Papan Informasi Digital Bengkel Berbasis Raspberry Pi', 'Layar informasi di bengkel yang menampilkan jadwal praktik, prosedur keselamatan kerja, dan antrean servis secara otomatis.', 'https://youtu.be/uji-papan-informasi', 45.00, NULL, 'tervalidasi', NULL, NULL, '2026-09-29 22:57:36', '2026-09-29 22:57:36'),
(13, 2, 5, NULL, 3, 4, 'Taman Sensorik Tanaman Herbal Lokal', 'Taman terapi berisi tanaman herbal khas Sulawesi Tenggara yang dapat diraba, dicium, dan dikenali siswa berkebutuhan khusus untuk melatih fokus dan motorik halus.', 'https://youtu.be/uji-sensory-herbal', 92.00, 1, 'tervalidasi', NULL, NULL, '2026-09-29 23:05:04', '2026-09-29 23:06:02'),
(14, 2, 5, NULL, 1, 2, 'Jalur Tekstur Alam dari Limbah Bengkel', 'Jalur pijakan sensorik dari potongan kayu, kerikil, dan limbah karet bengkel yang diolah aman untuk terapi keseimbangan.', 'https://youtu.be/uji-jalur-tekstur', 78.00, 2, 'tervalidasi', NULL, NULL, '2026-09-29 23:05:04', '2026-09-29 23:06:02'),
(15, 2, 5, NULL, 3, 11, 'Kebun Aroma Terapi untuk Siswa Autis', 'Area kebun kecil dengan tanaman beraroma lembut untuk membantu siswa autis menenangkan diri saat mengalami kelebihan stimulasi.', 'https://youtu.be/uji-kebun-aroma', 70.00, 3, 'tervalidasi', NULL, NULL, '2026-09-29 23:05:04', '2026-09-29 23:06:02'),
(16, 2, 5, NULL, 2, 3, 'Pot Hidroponik Sensorik Otomatis', 'Pot hidroponik dengan penyiram otomatis dan lampu indikator warna, sebagai media terapi merawat tanaman bagi siswa inklusi.', 'https://youtu.be/uji-pot-hidroponik', 60.00, NULL, 'tervalidasi', NULL, NULL, '2026-09-29 23:05:04', '2026-09-29 15:06:02'),
(17, 2, 5, NULL, 2, 5, 'Panel Bunyi Interaktif Taman Inklusi', 'Panel kayu berisi tombol yang mengeluarkan bunyi alam saat disentuh, untuk stimulasi pendengaran siswa berkebutuhan khusus.', 'https://youtu.be/uji-panel-bunyi', 52.00, NULL, 'tervalidasi', NULL, NULL, '2026-09-29 23:05:04', '2026-09-29 15:06:02'),
(18, 4, 6, NULL, 3, 4, 'Optimalisasi Potensi Peserta Didik melalui Pembelajaran Kreatif dan Interaktif', 'Inovasi pembelajaran yang dirancang untuk mengoptimalkan potensi, minat, dan bakat peserta didik melalui metode pembelajaran yang aktif, kreatif, dan sesuai dengan kebutuhan masing-masing siswa. Karya ini mendorong peserta didik untuk lebih aktif, percaya diri, dan mampu mengembangkan kemampuan secara optimal.', 'https://www.youtube.com/watch?v=2z_Pc0OrG1Q', 79.95, 2, 'tervalidasi', 16, NULL, '2026-09-30 04:39:04', '2026-09-30 19:17:37'),
(19, 4, 6, NULL, 2, 3, 'Eksplorasi Potensi Peserta Didik melalui Pembelajaran Inovatif', 'Inovasi pembelajaran yang menggunakan metode kreatif dan interaktif untuk menggali minat, bakat, serta kemampuan peserta didik. Kegiatan ini mendorong siswa untuk aktif dalam proses belajar, mengembangkan kreativitas, dan mengoptimalkan potensi yang dimiliki.', 'https://www.youtube.com/watch?v=JC2V0TswHjs', 98.79, 1, 'tervalidasi', 16, NULL, '2026-09-30 04:47:27', '2026-09-30 19:17:37'),
(20, 4, 6, NULL, 1, 2, 'Personalized Learning: Pembelajaran Efektif Sesuai Potensi Peserta Didik', 'Inovasi pembelajaran yang menyesuaikan metode, materi, dan aktivitas belajar dengan kebutuhan, minat, serta kemampuan masing-masing peserta didik. Pendekatan ini membantu siswa belajar secara lebih aktif, efektif, dan sesuai dengan potensi yang dimilikinya.', 'https://www.youtube.com/watch?v=JC2V0TswHjs', 76.10, 3, 'tervalidasi', 16, NULL, '2026-09-30 05:58:52', '2026-09-30 19:17:37'),
(21, 5, 7, NULL, 2, 3, 'Proyek Mini Kebun Sekolah', 'Proyek pembelajaran yang mengajak siswa membuat dan merawat kebun sederhana di lingkungan sekolah. Kegiatan ini bertujuan mengembangkan kreativitas, tanggung jawab, kerja sama, serta kepedulian siswa terhadap lingkungan melalui pengalaman belajar secara langsung.', 'https://www.youtube.com/watch?v=A8ksvnbKK6o', NULL, NULL, 'menunggu', NULL, NULL, '2026-09-30 11:31:31', '2026-09-30 11:31:31'),
(22, 5, 7, NULL, 3, 4, 'Eksperimen Sains Sederhana', 'Kegiatan pembelajaran berbasis eksperimen yang mengajak siswa melakukan percobaan sains sederhana secara langsung. Kegiatan ini bertujuan meningkatkan rasa ingin tahu, kreativitas, kemampuan berpikir kritis, serta membantu siswa memahami konsep sains melalui pengalaman nyata.', 'https://www.youtube.com/watch?v=4SBbLRLySJY', NULL, NULL, 'menunggu', NULL, NULL, '2026-09-30 11:35:30', '2026-09-30 11:35:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `monev`
--

CREATE TABLE `monev` (
  `id_monev` int(10) UNSIGNED NOT NULL,
  `id_peserta` int(10) UNSIGNED DEFAULT NULL,
  `id_sekolah` int(10) UNSIGNED NOT NULL,
  `id_penanggung_jawab` int(10) UNSIGNED DEFAULT NULL,
  `id_admin_pembuat` int(10) UNSIGNED DEFAULT NULL,
  `aspek_monev` varchar(150) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `hasil_temuan` text DEFAULT NULL,
  `rekomendasi` text DEFAULT NULL,
  `status` enum('dijadwalkan','selesai') NOT NULL DEFAULT 'dijadwalkan',
  `file_hasil` varchar(255) DEFAULT NULL,
  `tanggal_hasil` datetime DEFAULT NULL,
  `tanggal_monev` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `monev`
--

INSERT INTO `monev` (`id_monev`, `id_peserta`, `id_sekolah`, `id_penanggung_jawab`, `id_admin_pembuat`, `aspek_monev`, `deskripsi`, `hasil_temuan`, `rekomendasi`, `status`, `file_hasil`, `tanggal_hasil`, `tanggal_monev`, `created_at`, `updated_at`) VALUES
(2, 6, 2, NULL, 1, 'Implementasi di Sekolah', 'Periksa kesesuaian pelaksanaan dengan yang dipaparkan pada saat kompetisi.', NULL, NULL, 'dijadwalkan', NULL, NULL, '2026-10-08', '2026-09-29 09:32:59', '2026-09-29 01:33:15'),
(3, 7, 3, NULL, 1, 'Kesiapan Replikasi ke Sekolah Lain', 'Nilai apakah inovasi siap diadopsi oleh SLB lain di wilayah Kota Kendari.', NULL, NULL, 'dijadwalkan', NULL, NULL, '2026-10-06', '2026-09-29 09:32:59', '2026-09-29 09:32:59'),
(4, 5, 1, NULL, 1, 'Dampak bagi Peserta Didik', 'Wawancara 5 siswa dan 2 guru terkait manfaat inovasi.', 'Siswa merasa lebih mudah memahami materi. Nilai rata-rata ulangan naik dari 71 menjadi 79.', 'Perlu pendampingan agar inovasi dapat dipakai di kelas lain.', 'selesai', NULL, '2026-09-11 09:32:59', '2026-09-09', '2026-09-08 09:32:59', '2026-09-11 09:32:59'),
(6, 5, 1, NULL, 1, 'Implementasi di Sekolah', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.', NULL, NULL, 'dijadwalkan', NULL, NULL, '2026-09-24', '2026-09-29 01:38:35', '2026-09-29 01:38:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `praktik_baik`
--

CREATE TABLE `praktik_baik` (
  `id_praktik_baik` int(10) UNSIGNED NOT NULL,
  `id_guru` int(10) UNSIGNED NOT NULL,
  `id_sekolah` int(10) UNSIGNED NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL COMMENT 'mis: pembelajaran, manajemen, digital, dll',
  `status_verifikasi_sekolah` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `id_verifikator_sekolah` int(10) UNSIGNED DEFAULT NULL,
  `catatan_admin_sekolah` text DEFAULT NULL,
  `tanggal_verifikasi_sekolah` datetime DEFAULT NULL,
  `status_verifikasi_dinas` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `id_verifikator_dinas` int(10) UNSIGNED DEFAULT NULL,
  `catatan_petugas` text DEFAULT NULL,
  `catatan_revisi` text DEFAULT NULL,
  `jumlah_revisi` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `tanggal_revisi` datetime DEFAULT NULL,
  `tanggal_verifikasi_dinas` datetime DEFAULT NULL,
  `tanggal_upload` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `praktik_baik`
--

INSERT INTO `praktik_baik` (`id_praktik_baik`, `id_guru`, `id_sekolah`, `judul`, `deskripsi`, `kategori`, `status_verifikasi_sekolah`, `id_verifikator_sekolah`, `catatan_admin_sekolah`, `tanggal_verifikasi_sekolah`, `status_verifikasi_dinas`, `id_verifikator_dinas`, `catatan_petugas`, `catatan_revisi`, `jumlah_revisi`, `tanggal_revisi`, `tanggal_verifikasi_dinas`, `tanggal_upload`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Pojok Literasi Digital Kelas', 'Penyediaan pojok baca digital di setiap kelas dengan tablet dan e-book offline.', 'Literasi', 'disetujui', 9, 'menunggu informasi dari dinas', '2026-09-28 15:29:00', 'disetujui', 1, NULL, NULL, 0, NULL, '2026-09-28 07:34:21', '2026-09-28 15:28:16', '2026-09-28 15:28:16', '2026-09-28 07:34:21'),
(2, 2, 1, 'Kelas Literasi Pagi Berbasis Podcast', 'Siswa membuat podcast singkat 5 menit setiap pagi tentang buku yang mereka baca.\n\nKegiatan dilaksanakan 3 kali seminggu sebelum jam pelajaran pertama. Hasil rekaman diputar ulang melalui pengeras suara sekolah.', 'Literasi', 'disetujui', 10, 'Kegiatan sudah berjalan 4 bulan dan didukung kepala sekolah.', '2026-09-28 08:12:55', 'disetujui', 1, NULL, NULL, 0, NULL, '2026-09-29 01:15:49', '2026-09-24 08:12:55', '2026-09-29 08:12:55', '2026-09-29 01:15:49'),
(3, 3, 2, 'Bank Sampah Sekolah Terintegrasi Koperasi', 'Siswa menyetorkan sampah plastik yang sudah dipilah, lalu ditukar menjadi saldo tabungan di koperasi sekolah.\n\nDalam 3 bulan terkumpul lebih dari 400 kg sampah plastik.', 'Karakter & Lingkungan', 'disetujui', 9, NULL, '2026-09-29 02:10:02', 'disetujui', 1, 'sangat bagus okee saya ACC ya', NULL, 0, NULL, '2026-09-29 02:10:58', '2026-09-22 08:12:55', '2026-09-29 08:12:55', '2026-09-29 02:10:58'),
(4, 4, 3, 'Terapi Musik Tradisional untuk Siswa Berkebutuhan Khusus', 'Pemanfaatan alat musik tradisional Sulawesi Tenggara sebagai media terapi untuk melatih fokus dan motorik siswa berkebutuhan khusus.', 'Inklusi', 'disetujui', 7, 'baik.. silahkan lanjutkan', '2026-09-30 02:19:39', 'disetujui', 1, 'baik, silahkan di kerjakan', 'saya sudah memperbaiki file yang tidak bisa di buka .. terimakasih atas kerja samanya.', 1, '2026-09-30 02:19:01', '2026-09-30 02:20:58', '2026-09-27 08:12:56', '2026-09-29 08:12:56', '2026-09-30 02:20:58'),
(5, 3, 2, 'Pembelajaran Berbasis Proyek Bengkel Mini Otomotif', 'Siswa kelas XI TKR mengelola bengkel mini di sekolah untuk melayani servis ringan kendaraan guru dan warga sekitar.\n\nSetiap kelompok bertanggung jawab penuh mulai dari penerimaan kendaraan, diagnosis, perbaikan, hingga pencatatan biaya.', 'Pembelajaran Vokasi', 'disetujui', 15, 'gaskan kalian', '2026-09-29 05:43:14', 'disetujui', 1, 'okee acc', NULL, 0, NULL, '2026-09-30 08:59:54', '2026-09-25 10:14:50', '2026-09-29 10:14:50', '2026-09-30 08:59:54'),
(6, 3, 2, 'Jumat Mengaji dan Literasi Keagamaan', 'Setiap Jumat pagi siswa membaca dan mengkaji teks keagamaan selama 30 menit, dilanjutkan diskusi nilai-nilai karakter yang dapat diterapkan di bengkel dan kelas.', 'Karakter', 'disetujui', 9, NULL, '2026-09-29 03:58:56', 'disetujui', 1, NULL, NULL, 0, NULL, '2026-09-29 03:59:42', '2026-09-27 10:14:51', '2026-09-29 10:14:51', '2026-09-29 03:59:42'),
(7, 3, 2, 'Pojok Konseling Sebaya', 'Siswa terlatih menjadi konselor sebaya yang siap mendengarkan keluhan teman di pojok konseling setiap jam istirahat.', 'Kesejahteraan Siswa', 'disetujui', 9, 'tidak lampiran tp its oke', '2026-09-29 02:49:40', 'disetujui', 1, 'acc', NULL, 0, NULL, '2026-09-29 03:57:50', '2026-09-29 04:14:51', '2026-09-29 10:14:51', '2026-09-29 03:57:50'),
(8, 4, 3, 'Pembuatan Aplikasi Absensi menggunakan teknologi Kecerdasan Buatan', 'Pengembangan aplikasi absensi berbasis Kecerdasan Buatan (AI) untuk mempermudah proses pencatatan dan pemantauan kehadiran. Aplikasi dirancang agar proses absensi lebih cepat, akurat, dan efisien serta memudahkan pengelolaan data kehadiran.', 'Pembelajaran', 'disetujui', 7, 'waaw keren banget', '2026-09-30 02:26:57', 'disetujui', 1, 'waaawww', NULL, 0, NULL, '2026-09-30 04:01:56', '2026-09-30 02:25:32', '2026-09-30 02:25:32', '2026-09-30 04:01:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `praktik_baik_dokumen`
--

CREATE TABLE `praktik_baik_dokumen` (
  `id_dokumen` int(10) UNSIGNED NOT NULL,
  `id_praktik_baik` int(10) UNSIGNED NOT NULL,
  `jenis_dokumen` enum('foto','video','dokumen_program','data_hasil','bukti_perubahan','penghargaan','tautan_publikasi','dokumen_pendukung_lainnya') NOT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `path_file` varchar(255) DEFAULT NULL COMMENT 'path/URL file atau tautan publikasi',
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `praktik_baik_dokumen`
--

INSERT INTO `praktik_baik_dokumen` (`id_dokumen`, `id_praktik_baik`, `jenis_dokumen`, `nama_file`, `path_file`, `keterangan`, `created_at`) VALUES
(1, 1, 'foto', 'foto-kegiatan.jpg', 'uploads/praktik_baik/foto-kegiatan.jpg', 'Foto kegiatan membaca pagi', '2026-09-28 15:28:16'),
(2, 1, 'dokumen_program', 'program-literasi.pdf', 'uploads/praktik_baik/program-literasi.pdf', 'Dokumen rancangan program', '2026-09-28 15:28:16'),
(3, 1, 'data_hasil', 'data-peminjaman-buku.pdf', 'uploads/praktik_baik/data-peminjaman-buku.pdf', 'Rekap peningkatan minat baca', '2026-09-28 15:28:16'),
(4, 2, 'foto', 'rekaman-podcast.jpg', 'uploads/praktik_baik/rekaman-podcast.jpg', 'Siswa merekam podcast', '2026-09-29 08:12:55'),
(5, 2, 'dokumen_program', 'jadwal-literasi.pdf', 'uploads/praktik_baik/jadwal-literasi.pdf', 'Jadwal kegiatan literasi', '2026-09-29 08:12:55'),
(6, 3, 'data_hasil', 'rekap-setoran-sampah.pdf', 'uploads/praktik_baik/rekap-setoran-sampah.pdf', 'Rekap setoran per bulan', '2026-09-29 08:12:55'),
(9, 5, 'foto', 'bengkel-mini.jpg', 'uploads/praktik_baik/bengkel-mini.jpg', 'Kegiatan servis oleh siswa', '2026-09-29 10:14:50'),
(10, 5, 'data_hasil', 'rekap-layanan-bengkel.pdf', 'uploads/praktik_baik/rekap-layanan-bengkel.pdf', 'Rekap 60 layanan servis', '2026-09-29 10:14:50'),
(11, 6, 'foto', 'jumat-mengaji.jpg', 'uploads/praktik_baik/jumat-mengaji.jpg', 'Kegiatan Jumat pagi', '2026-09-29 10:14:51'),
(12, 4, 'foto', 'Screenshot 2026-05-10 134457.png', 'uploads/praktik_baik/1790734741_1fbbee1a9584cee3f36d.png', 'foto alur pengerjaan', '2026-09-30 02:19:01'),
(13, 8, 'foto', 'Screenshot 2026-05-10 143137.png', 'uploads/praktik_baik/1790735132_fe5a8fb7de8a13e0ca46.png', 'foto struktur kerja', '2026-09-30 02:25:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sekolah`
--

CREATE TABLE `sekolah` (
  `id_sekolah` int(10) UNSIGNED NOT NULL,
  `npsn` varchar(20) NOT NULL,
  `nama_sekolah` varchar(150) NOT NULL,
  `jenjang` enum('SMA','SMK','SLB') NOT NULL,
  `alamat` text DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kabupaten_kota` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sekolah`
--

INSERT INTO `sekolah` (`id_sekolah`, `npsn`, `nama_sekolah`, `jenjang`, `alamat`, `kecamatan`, `kabupaten_kota`, `telepon`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, '40402625', 'SMKN 2 Kendari', 'SMK', 'Jl. Jend. Achmad Yani No. 13, Kadia, Kec. Kadia, Kota Kendari, Sulawesi Tenggara', 'Kadia', 'Kota Kendari', '04013190456', 'stmkdi@gmail.com', 'aktif', '2026-09-28 05:43:45', '2026-09-28 07:23:14'),
(2, '40402627', 'SMKN 4 Kendari', 'SMK', 'Jl. Kijang, Kelurahan Rahandouna, Kecamatan Poasia, Kota Kendari, Sulawesi Tenggara', 'Poasia', 'Kota Kendari', '04013193756', 'smknegeri4kdi@gmail.com', 'aktif', '2026-09-28 05:49:24', '2026-09-28 05:59:15'),
(3, '40403980', 'SLBS B-F Mandara', 'SLB', 'Jln. Antero Hamra, Kelurahan Baruga, Kecamatan Baruga, Kota Kendari', 'Baruga', 'Kota Kendari', '04013124846', 'slbmandara1@gmail.com', 'aktif', '2026-09-28 05:55:39', '2026-09-29 05:17:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `suara`
--

CREATE TABLE `suara` (
  `id_suara` int(10) UNSIGNED NOT NULL,
  `id_sekolah` int(10) UNSIGNED DEFAULT NULL,
  `id_guru` int(10) UNSIGNED DEFAULT NULL,
  `id_admin_pengirim` int(10) UNSIGNED DEFAULT NULL,
  `nama_pengirim` varchar(100) DEFAULT NULL,
  `email_pengirim` varchar(100) DEFAULT NULL,
  `kategori` enum('saran','keluhan','pertanyaan','lainnya') NOT NULL DEFAULT 'lainnya',
  `isi_suara` text NOT NULL,
  `status_tindak_lanjut` enum('belum_ditindak','diproses','selesai') NOT NULL DEFAULT 'belum_ditindak',
  `tanggapan` text DEFAULT NULL,
  `id_admin_penindak` int(10) UNSIGNED DEFAULT NULL,
  `tanggal_kirim` datetime NOT NULL DEFAULT current_timestamp(),
  `tanggal_tindak_lanjut` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `suara`
--

INSERT INTO `suara` (`id_suara`, `id_sekolah`, `id_guru`, `id_admin_pengirim`, `nama_pengirim`, `email_pengirim`, `kategori`, `isi_suara`, `status_tindak_lanjut`, `tanggapan`, `id_admin_penindak`, `tanggal_kirim`, `tanggal_tindak_lanjut`) VALUES
(1, 1, 1, NULL, NULL, NULL, 'saran', 'Mohon ditambahkan kategori inovasi pelestarian bahasa daerah pada kompetisi berikutnya.', 'belum_ditindak', NULL, NULL, '2026-09-28 16:21:27', NULL),
(2, 1, NULL, NULL, 'Budi Santoso', 'budi.santoso@gmail.com', 'keluhan', 'Jadwal pengumpulan video kompetisi terlalu singkat untuk sekolah di wilayah kepulauan.', 'selesai', 'okee gapapa, yang penting sudah menajawab', 1, '2026-09-28 16:21:27', '2026-09-30 01:24:02'),
(3, 2, NULL, 9, 'Yauma Neuvilete', 'smknegeri4kdi@gmail.com', 'keluhan', 'aduhh pak kenapa saya tidak cair sudah 3 bulan lebih. tolonglah kerja samanya. cape loh kerja ga di bayar bayar, mana PPG di jadikan gaji pokok yaAllah... mana sy anak yatim', 'selesai', 'iyaiya.. bendahara nya abis uangnyaa', 1, '2026-09-29 05:11:52', '2026-09-30 01:23:39'),
(4, 3, NULL, 7, 'Admin SLBS B-F Mandara', 'slbmandara1@gmail.com', 'keluhan', 'assalamu alaikum pak.. tolong cairkan PPG KAMI', 'diproses', 'tunggu bulan depan yah', 1, '2026-09-29 21:54:37', '2026-09-30 01:23:18'),
(5, 1, 2, NULL, 'Shultonul Ma\'arif', NULL, 'saran', 'bapaakk tolong dengarkan suara guru guru iniiii', 'belum_ditindak', NULL, NULL, '2026-09-30 06:03:37', NULL),
(6, 3, 4, NULL, 'Robiatul Adawiya', NULL, 'saran', 'pak saya kurang mbgnya', 'belum_ditindak', NULL, NULL, '2026-09-30 09:10:20', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_admin_sekolah` (`id_sekolah`),
  ADD KEY `idx_admin_role` (`role`);

--
-- Indeks untuk tabel `apresiasi`
--
ALTER TABLE `apresiasi`
  ADD PRIMARY KEY (`id_apresiasi`),
  ADD UNIQUE KEY `satu_apresiasi_per_karya` (`id_peserta`),
  ADD KEY `fk_ap_sekolah` (`id_sekolah`),
  ADD KEY `fk_ap_guru` (`id_guru`),
  ADD KEY `fk_ap_verifikator` (`id_verifikator`),
  ADD KEY `idx_ap_status` (`status_verifikasi`);

--
-- Indeks untuk tabel `bank_inovasi`
--
ALTER TABLE `bank_inovasi`
  ADD PRIMARY KEY (`id_inovasi`),
  ADD KEY `fk_bi_praktik_baik` (`id_praktik_baik`),
  ADD KEY `fk_bi_sekolah` (`id_sekolah`),
  ADD KEY `fk_bi_guru` (`id_guru`),
  ADD KEY `fk_bi_verifikator` (`id_verifikator`),
  ADD KEY `idx_bi_status` (`status_verifikasi`);

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id_guru`),
  ADD UNIQUE KEY `user_name` (`username`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD KEY `idx_guru_sekolah` (`id_sekolah`);

--
-- Indeks untuk tabel `juri_penugasan`
--
ALTER TABLE `juri_penugasan`
  ADD PRIMARY KEY (`id_penugasan`),
  ADD UNIQUE KEY `satu_tugas` (`id_juri`,`id_kategori`),
  ADD KEY `fk_tugas_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `kompetisi`
--
ALTER TABLE `kompetisi`
  ADD PRIMARY KEY (`id_kompetisi`),
  ADD KEY `fk_kompetisi_admin` (`id_admin_pembuat`);

--
-- Indeks untuk tabel `kompetisi_kategori`
--
ALTER TABLE `kompetisi_kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD KEY `id_kompetisi` (`id_kompetisi`);

--
-- Indeks untuk tabel `kompetisi_kriteria`
--
ALTER TABLE `kompetisi_kriteria`
  ADD PRIMARY KEY (`id_kriteria`),
  ADD KEY `id_kompetisi` (`id_kompetisi`);

--
-- Indeks untuk tabel `kompetisi_nilai`
--
ALTER TABLE `kompetisi_nilai`
  ADD PRIMARY KEY (`id_nilai`),
  ADD UNIQUE KEY `satu_nilai_per_juri` (`id_peserta`,`id_kriteria`,`id_juri`),
  ADD KEY `fk_nilai_kriteria` (`id_kriteria`),
  ADD KEY `fk_nilai_juri` (`id_juri`);

--
-- Indeks untuk tabel `kompetisi_peserta`
--
ALTER TABLE `kompetisi_peserta`
  ADD PRIMARY KEY (`id_peserta`),
  ADD UNIQUE KEY `uq_peserta_per_kompetisi` (`id_kompetisi`,`id_guru`),
  ADD KEY `fk_kp_praktik_baik` (`id_praktik_baik`),
  ADD KEY `fk_kp_sekolah` (`id_sekolah`),
  ADD KEY `fk_kp_guru` (`id_guru`),
  ADD KEY `fk_kp_juri` (`id_juri_validator`),
  ADD KEY `idx_kp_status` (`status_validasi`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `monev`
--
ALTER TABLE `monev`
  ADD PRIMARY KEY (`id_monev`),
  ADD KEY `fk_monev_sekolah` (`id_sekolah`),
  ADD KEY `fk_monev_pj` (`id_penanggung_jawab`),
  ADD KEY `id_peserta` (`id_peserta`);

--
-- Indeks untuk tabel `praktik_baik`
--
ALTER TABLE `praktik_baik`
  ADD PRIMARY KEY (`id_praktik_baik`),
  ADD KEY `fk_pb_guru` (`id_guru`),
  ADD KEY `fk_pb_verif_sekolah` (`id_verifikator_sekolah`),
  ADD KEY `fk_pb_verif_dinas` (`id_verifikator_dinas`),
  ADD KEY `idx_pb_status` (`status_verifikasi_sekolah`,`status_verifikasi_dinas`),
  ADD KEY `idx_pb_sekolah` (`id_sekolah`);

--
-- Indeks untuk tabel `praktik_baik_dokumen`
--
ALTER TABLE `praktik_baik_dokumen`
  ADD PRIMARY KEY (`id_dokumen`),
  ADD KEY `fk_dok_praktik_baik` (`id_praktik_baik`),
  ADD KEY `idx_dok_jenis` (`jenis_dokumen`);

--
-- Indeks untuk tabel `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`id_sekolah`),
  ADD UNIQUE KEY `npsn` (`npsn`),
  ADD KEY `idx_sekolah_jenjang` (`jenjang`),
  ADD KEY `idx_sekolah_kabkota` (`kabupaten_kota`);

--
-- Indeks untuk tabel `suara`
--
ALTER TABLE `suara`
  ADD PRIMARY KEY (`id_suara`),
  ADD KEY `fk_suara_sekolah` (`id_sekolah`),
  ADD KEY `fk_suara_guru` (`id_guru`),
  ADD KEY `fk_suara_admin` (`id_admin_penindak`),
  ADD KEY `idx_suara_status` (`status_tindak_lanjut`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `apresiasi`
--
ALTER TABLE `apresiasi`
  MODIFY `id_apresiasi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `bank_inovasi`
--
ALTER TABLE `bank_inovasi`
  MODIFY `id_inovasi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id_guru` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `juri_penugasan`
--
ALTER TABLE `juri_penugasan`
  MODIFY `id_penugasan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kompetisi`
--
ALTER TABLE `kompetisi`
  MODIFY `id_kompetisi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kompetisi_kategori`
--
ALTER TABLE `kompetisi_kategori`
  MODIFY `id_kategori` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kompetisi_kriteria`
--
ALTER TABLE `kompetisi_kriteria`
  MODIFY `id_kriteria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT untuk tabel `kompetisi_nilai`
--
ALTER TABLE `kompetisi_nilai`
  MODIFY `id_nilai` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT untuk tabel `kompetisi_peserta`
--
ALTER TABLE `kompetisi_peserta`
  MODIFY `id_peserta` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `monev`
--
ALTER TABLE `monev`
  MODIFY `id_monev` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `praktik_baik`
--
ALTER TABLE `praktik_baik`
  MODIFY `id_praktik_baik` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `praktik_baik_dokumen`
--
ALTER TABLE `praktik_baik_dokumen`
  MODIFY `id_dokumen` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `id_sekolah` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `suara`
--
ALTER TABLE `suara`
  MODIFY `id_suara` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_sekolah` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `apresiasi`
--
ALTER TABLE `apresiasi`
  ADD CONSTRAINT `fk_ap_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ap_sekolah` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ap_verifikator` FOREIGN KEY (`id_verifikator`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_apresiasi_peserta` FOREIGN KEY (`id_peserta`) REFERENCES `kompetisi_peserta` (`id_peserta`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bank_inovasi`
--
ALTER TABLE `bank_inovasi`
  ADD CONSTRAINT `fk_bi_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bi_praktik_baik` FOREIGN KEY (`id_praktik_baik`) REFERENCES `praktik_baik` (`id_praktik_baik`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bi_sekolah` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bi_verifikator` FOREIGN KEY (`id_verifikator`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `fk_guru_sekolah` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `juri_penugasan`
--
ALTER TABLE `juri_penugasan`
  ADD CONSTRAINT `fk_tugas_juri` FOREIGN KEY (`id_juri`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tugas_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kompetisi_kategori` (`id_kategori`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kompetisi`
--
ALTER TABLE `kompetisi`
  ADD CONSTRAINT `fk_kompetisi_admin` FOREIGN KEY (`id_admin_pembuat`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kompetisi_kategori`
--
ALTER TABLE `kompetisi_kategori`
  ADD CONSTRAINT `fk_kategori_kompetisi` FOREIGN KEY (`id_kompetisi`) REFERENCES `kompetisi` (`id_kompetisi`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kompetisi_kriteria`
--
ALTER TABLE `kompetisi_kriteria`
  ADD CONSTRAINT `fk_kriteria_kompetisi` FOREIGN KEY (`id_kompetisi`) REFERENCES `kompetisi` (`id_kompetisi`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kompetisi_nilai`
--
ALTER TABLE `kompetisi_nilai`
  ADD CONSTRAINT `fk_nilai_juri` FOREIGN KEY (`id_juri`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `fk_nilai_kriteria` FOREIGN KEY (`id_kriteria`) REFERENCES `kompetisi_kriteria` (`id_kriteria`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_nilai_peserta` FOREIGN KEY (`id_peserta`) REFERENCES `kompetisi_peserta` (`id_peserta`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kompetisi_peserta`
--
ALTER TABLE `kompetisi_peserta`
  ADD CONSTRAINT `fk_kp_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kp_juri` FOREIGN KEY (`id_juri_validator`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kp_kompetisi` FOREIGN KEY (`id_kompetisi`) REFERENCES `kompetisi` (`id_kompetisi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kp_praktik_baik` FOREIGN KEY (`id_praktik_baik`) REFERENCES `praktik_baik` (`id_praktik_baik`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kp_sekolah` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_peserta_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kompetisi_kategori` (`id_kategori`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `monev`
--
ALTER TABLE `monev`
  ADD CONSTRAINT `fk_monev_peserta` FOREIGN KEY (`id_peserta`) REFERENCES `kompetisi_peserta` (`id_peserta`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_monev_pj` FOREIGN KEY (`id_penanggung_jawab`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_monev_sekolah` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `praktik_baik`
--
ALTER TABLE `praktik_baik`
  ADD CONSTRAINT `fk_pb_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pb_sekolah` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pb_verif_dinas` FOREIGN KEY (`id_verifikator_dinas`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pb_verif_sekolah` FOREIGN KEY (`id_verifikator_sekolah`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `praktik_baik_dokumen`
--
ALTER TABLE `praktik_baik_dokumen`
  ADD CONSTRAINT `fk_dok_praktik_baik` FOREIGN KEY (`id_praktik_baik`) REFERENCES `praktik_baik` (`id_praktik_baik`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `suara`
--
ALTER TABLE `suara`
  ADD CONSTRAINT `fk_suara_admin` FOREIGN KEY (`id_admin_penindak`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_suara_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_suara_sekolah` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
