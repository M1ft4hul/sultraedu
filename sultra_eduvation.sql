-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Sep 2026 pada 08.32
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
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `username`, `password`, `role`, `id_sekolah`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Muh Syamdudin syawal', 'adminpusat', '$2y$10$c6LW2r9xPIv04VkAs0W46e/VSgA071Vcf8KBcCPJzPU4eJfP7iTha', 'admin_pusat', NULL, 'admin@eduvation.sultraprov.go.id', 'aktif', '2026-09-24 23:49:23', '2026-09-29 00:37:30'),
(7, 'Admin SLBS B-F Mandara', '40403980', '$2y$10$eAT4kzP0ftKyo1xwLQFIj.IlVxLMIlO003RBjJEEe/u/iF8wbzUIi', 'admin_sekolah', 3, 'slbmandara1@gmail.com', 'aktif', '2026-09-28 06:44:35', '2026-09-29 05:17:10'),
(9, 'Yauma Neuvilete', '40402627', '$2y$10$kmg4B70BrWfkHyeodR94LOkTWp/aQx4gxGLKc7tjWOGeW3qYb/E12', 'admin_sekolah', 2, 'smknegeri4kdi@gmail.com', 'aktif', '2026-09-28 06:44:35', '2026-09-29 02:42:16'),
(10, 'Admin SMKN 2 Kendari', '40402625', '$2y$10$w.jAJ/HPAlrN4SoBBE7.MuTBRTtwJiC7BVZkOnwd6rsqbKKjzJeJq', 'admin_sekolah', 1, 'stmkdi@gmail.com', 'aktif', '2026-09-28 06:47:56', '2026-09-28 06:47:56'),
(13, 'Juri Uji', 'juriuji', '-', 'tim_juri', NULL, NULL, 'aktif', '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(14, 'Samsudin', 'samsu_din', '$2y$10$insrukWK7dLLdD6AOzq6Tu.nIF4/PMgxYskguPNKrrIX0Dn8ITkz2', 'admin_pusat', NULL, 'samsu@gmail.com', 'aktif', '2026-09-29 01:53:42', '2026-09-29 01:53:42'),
(15, 'Miftahul Jannah', 'mjcutter', '$2y$10$c/R17yXef1nth0qYz2Qaa.YwoZN8N9q0yPfU6uvVvg3jPi7qA.Nd.', 'admin_sekolah', 2, 'smknegeri4kdi@gmail.com', 'aktif', '2026-09-29 05:18:05', '2026-09-29 05:18:05');

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
(2, 6, 2, 3, 'Juara 2 – Transformasi Digital Pembelajaran & Manajemen Sekolah', 'Karya Uji B', NULL, 'diverifikasi', 1, '2026-09-28 23:39:38', '2026-09-28 23:39:38', '2026-09-28 23:39:38'),
(3, 7, 3, 4, 'Juara 3 – Transformasi Digital Pembelajaran & Manajemen Sekolah', 'Karya Uji C', NULL, 'diverifikasi', 1, '2026-09-28 23:39:38', '2026-09-28 23:39:38', '2026-09-28 23:39:38');

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
  `tanggal_verifikasi` datetime DEFAULT NULL,
  `tanggal_publish` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bank_inovasi`
--

INSERT INTO `bank_inovasi` (`id_inovasi`, `id_praktik_baik`, `id_sekolah`, `id_guru`, `judul_inovasi`, `deskripsi`, `status_verifikasi_sekolah`, `id_verifikator_sekolah`, `catatan_sekolah`, `tanggal_verifikasi_sekolah`, `status_verifikasi`, `id_verifikator`, `catatan_verifikasi`, `tanggal_verifikasi`, `tanggal_publish`, `created_at`, `updated_at`) VALUES
(1, 5, 2, 3, 'Aplikasi Antrean Servis Bengkel Sekolah', 'Pengembangan dari praktik baik bengkel mini. Pelanggan mendaftar antrean servis melalui aplikasi web, lalu memantau status perbaikan secara real-time.\n\nWaktu tunggu pelanggan turun dari rata-rata 45 menit menjadi 15 menit.', 'menunggu', NULL, NULL, NULL, 'menunggu', NULL, NULL, NULL, NULL, '2026-09-24 10:42:47', '2026-09-24 10:42:47'),
(2, NULL, 2, 3, 'Simulator Kelistrikan Otomotif Berbasis Arduino', 'Papan simulasi kelistrikan bodi kendaraan yang dapat diprogram untuk memunculkan kerusakan tertentu, sehingga siswa berlatih diagnosis tanpa memerlukan kendaraan asli.', 'menunggu', NULL, NULL, NULL, 'menunggu', NULL, NULL, NULL, NULL, '2026-09-25 10:42:47', '2026-09-25 10:42:47'),
(3, 6, 2, 3, 'Buku Saku Digital Nilai Karakter Bengkel', 'Pengembangan dari kegiatan Jumat Mengaji: buku saku digital berisi 30 nilai karakter yang dikaitkan langsung dengan etika kerja di bengkel, dapat diakses melalui kode QR di setiap stan kerja.', 'disetujui', 9, 'okee lanjutkan', '2026-09-29 04:30:09', 'disetujui', 1, 'okee mantap', '2026-09-29 04:30:40', '2026-09-29 04:30:40', '2026-09-26 10:42:47', '2026-09-29 04:30:40'),
(4, NULL, 2, 3, 'Kartu Kompetensi Siswa Berbasis QR', 'Setiap siswa memiliki kartu dengan kode QR yang menampilkan daftar kompetensi yang sudah dikuasai beserta tanda tangan digital guru pembimbing. Mitra industri dapat memindai kartu saat rekrutmen prakerin.', 'disetujui', 9, 'di acc tapi harus lengkapi dokumen', '2026-09-29 02:47:58', 'disetujui', 1, 'okeee lanjutkan', '2026-09-29 02:48:31', '2026-09-29 02:48:31', '2026-09-28 10:42:47', '2026-09-29 02:48:31'),
(5, NULL, 2, 3, 'Panel Surya Mini untuk Praktik Kelistrikan Ramah Lingkungan', 'Rangkaian panel surya skala kecil sebagai sumber listrik alat praktik, sekaligus media belajar energi terbarukan bagi siswa.', 'ditolak', 9, 'lampiran tidak tersedia', '2026-09-29 02:46:35', 'menunggu', NULL, NULL, NULL, NULL, '2026-09-29 05:42:47', '2026-09-29 02:46:35');

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
(2, 'UJI0001', 'Guru Uji 1', 'guruuji1', '-', 1, 'Uji Coba', 'aktif', '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(3, 'UJI0002', 'Guru Uji 2', 'guruuji2', '$2y$10$RZXhlv8O5bMvN7nVxbfBAOEAmDFx9Gqg/HkLUb8Jkz5iJ5BxedNfK', 2, 'Uji Coba', 'aktif', '2026-09-29 07:36:58', '2026-09-29 05:37:39'),
(4, 'UJI0003', 'Guru Uji 3', 'guruuji3', '-', 3, 'Uji Coba', 'aktif', '2026-09-29 07:36:58', '2026-09-29 07:36:58'),
(5, '197803142005012007', 'Hj. Nurhayati, S.Pd., M.Pd.', NULL, NULL, 2, 'Bahasa Indonesia', 'aktif', '2026-09-29 13:36:49', '2026-09-29 13:36:49'),
(6, '198511062010011019', 'Andi Firmansyah, S.T.', 'firman', '$2y$10$aOmZztTLsZKRxFGOVLAKPOmWu3xoZbu/jNmpSZjdWKtxpZsgyK0Ze', 2, 'Teknik Kendaraan Ringan', 'aktif', '2026-09-29 13:36:49', '2026-09-29 05:48:01'),
(7, '199002232019032011', 'Wa Ode Sitti Rahmah, S.Pd.', NULL, NULL, 2, 'Matematika', 'aktif', '2026-09-29 13:36:49', '2026-09-29 13:36:49'),
(8, '198207192009021004', 'La Ode Muh. Ikhsan, S.Kom.', NULL, NULL, 2, 'Rekayasa Perangkat Lunak', 'aktif', '2026-09-29 13:36:49', '2026-09-29 13:36:49'),
(9, '199405302022012015', 'Dewi Kartika Sari, S.Pd.', 'rahma', '$2y$10$Ko.ZMDHyA/2aTkRvdmRJD.YeOp7jLRYY1zqh6A.Hwp1IzeWuBCdF6', 2, 'Bahasa Inggris', 'aktif', '2026-09-29 13:36:49', '2026-09-29 05:42:55');

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
(1, 'Kompetisi Inovasi Pendidikan 2026', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.', '2026-09-28', '2026-10-28', 'selesai', 1, '2026-09-28 23:39:38', 1, '2026-09-28 08:04:15', '2026-09-28 23:39:38'),
(2, 'Sensory garden terapi inklusi', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like)', '2026-09-30', '2026-10-30', 'pendaftaran', 0, NULL, 1, '2026-09-28 08:09:43', '2026-09-29 01:39:31'),
(4, 'Lorem Ipsum', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since 1966, when designers at Letraset and James Mosley, the librarian at St Bride Printing Library in London, took a 1914 Cicero translation and scrambled it to make dummy text for Letraset\'s Body Type sheets. It has survived not only many decades, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised thanks to these sheets and more recently with desktop publishing software like Aldus PageMaker and Microsoft Word including versions of Lorem Ipsum.', '2026-10-01', '2026-10-08', 'pendaftaran', 0, NULL, 1, '2026-09-29 01:40:08', '2026-09-29 01:40:13');

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
(6, 4, 'Inovasi Pembelajaran Efektif & Potensi Peserta Didik', NULL, 1, '2026-09-29 09:40:08');

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
(36, 4, 'Dokumentasi & Video', 'Kejelasan penyajian video 3 menit #sultraeduvation', 10, 7);

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
(63, 11, 7, 13, 5.00, '2026-09-29 14:13:19', '2026-09-29 14:13:19');

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
(11, 1, 1, NULL, 2, 8, 'Sistem Absensi Bengkel dengan RFID', 'Kartu RFID untuk mencatat kehadiran siswa di bengkel praktik sekaligus peminjaman alat, sehingga alat yang hilang bisa dilacak.', 'https://youtu.be/uji-absensi-rfid', 50.00, NULL, 'tervalidasi', NULL, NULL, '2026-09-29 14:13:19', '2026-09-29 14:13:19');

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
  `tanggal_verifikasi_dinas` datetime DEFAULT NULL,
  `tanggal_upload` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `praktik_baik`
--

INSERT INTO `praktik_baik` (`id_praktik_baik`, `id_guru`, `id_sekolah`, `judul`, `deskripsi`, `kategori`, `status_verifikasi_sekolah`, `id_verifikator_sekolah`, `catatan_admin_sekolah`, `tanggal_verifikasi_sekolah`, `status_verifikasi_dinas`, `id_verifikator_dinas`, `catatan_petugas`, `tanggal_verifikasi_dinas`, `tanggal_upload`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Pojok Literasi Digital Kelas', 'Penyediaan pojok baca digital di setiap kelas dengan tablet dan e-book offline.', 'Literasi', 'disetujui', 9, 'menunggu informasi dari dinas', '2026-09-28 15:29:00', 'disetujui', 1, NULL, '2026-09-28 07:34:21', '2026-09-28 15:28:16', '2026-09-28 15:28:16', '2026-09-28 07:34:21'),
(2, 2, 1, 'Kelas Literasi Pagi Berbasis Podcast', 'Siswa membuat podcast singkat 5 menit setiap pagi tentang buku yang mereka baca.\n\nKegiatan dilaksanakan 3 kali seminggu sebelum jam pelajaran pertama. Hasil rekaman diputar ulang melalui pengeras suara sekolah.', 'Literasi', 'disetujui', 10, 'Kegiatan sudah berjalan 4 bulan dan didukung kepala sekolah.', '2026-09-28 08:12:55', 'disetujui', 1, NULL, '2026-09-29 01:15:49', '2026-09-24 08:12:55', '2026-09-29 08:12:55', '2026-09-29 01:15:49'),
(3, 3, 2, 'Bank Sampah Sekolah Terintegrasi Koperasi', 'Siswa menyetorkan sampah plastik yang sudah dipilah, lalu ditukar menjadi saldo tabungan di koperasi sekolah.\n\nDalam 3 bulan terkumpul lebih dari 400 kg sampah plastik.', 'Karakter & Lingkungan', 'disetujui', 9, NULL, '2026-09-29 02:10:02', 'disetujui', 1, 'sangat bagus okee saya ACC ya', '2026-09-29 02:10:58', '2026-09-22 08:12:55', '2026-09-29 08:12:55', '2026-09-29 02:10:58'),
(4, 4, 3, 'Terapi Musik Tradisional untuk Siswa Berkebutuhan Khusus', 'Pemanfaatan alat musik tradisional Sulawesi Tenggara sebagai media terapi untuk melatih fokus dan motorik siswa berkebutuhan khusus.', 'Inklusi', 'disetujui', 7, 'Mohon dipertimbangkan untuk direplikasi ke SLB lain.', '2026-09-29 05:12:56', 'ditolak', 1, 'datanya tidak valid', '2026-09-29 00:16:28', '2026-09-27 08:12:56', '2026-09-29 08:12:56', '2026-09-29 00:16:28'),
(5, 3, 2, 'Pembelajaran Berbasis Proyek Bengkel Mini Otomotif', 'Siswa kelas XI TKR mengelola bengkel mini di sekolah untuk melayani servis ringan kendaraan guru dan warga sekitar.\n\nSetiap kelompok bertanggung jawab penuh mulai dari penerimaan kendaraan, diagnosis, perbaikan, hingga pencatatan biaya.', 'Pembelajaran Vokasi', 'disetujui', 15, 'gaskan kalian', '2026-09-29 05:43:14', 'menunggu', NULL, NULL, NULL, '2026-09-25 10:14:50', '2026-09-29 10:14:50', '2026-09-29 05:43:14'),
(6, 3, 2, 'Jumat Mengaji dan Literasi Keagamaan', 'Setiap Jumat pagi siswa membaca dan mengkaji teks keagamaan selama 30 menit, dilanjutkan diskusi nilai-nilai karakter yang dapat diterapkan di bengkel dan kelas.', 'Karakter', 'disetujui', 9, NULL, '2026-09-29 03:58:56', 'disetujui', 1, NULL, '2026-09-29 03:59:42', '2026-09-27 10:14:51', '2026-09-29 10:14:51', '2026-09-29 03:59:42'),
(7, 3, 2, 'Pojok Konseling Sebaya', 'Siswa terlatih menjadi konselor sebaya yang siap mendengarkan keluhan teman di pojok konseling setiap jam istirahat.', 'Kesejahteraan Siswa', 'disetujui', 9, 'tidak lampiran tp its oke', '2026-09-29 02:49:40', 'disetujui', 1, 'acc', '2026-09-29 03:57:50', '2026-09-29 04:14:51', '2026-09-29 10:14:51', '2026-09-29 03:57:50');

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
(7, 4, 'foto', 'sesi-terapi-musik.jpg', 'uploads/praktik_baik/sesi-terapi-musik.jpg', 'Sesi terapi musik', '2026-09-29 08:12:56'),
(8, 4, 'video', 'dokumentasi-terapi.mp4', 'uploads/praktik_baik/dokumentasi-terapi.mp4', 'Video dokumentasi', '2026-09-29 08:12:56'),
(9, 5, 'foto', 'bengkel-mini.jpg', 'uploads/praktik_baik/bengkel-mini.jpg', 'Kegiatan servis oleh siswa', '2026-09-29 10:14:50'),
(10, 5, 'data_hasil', 'rekap-layanan-bengkel.pdf', 'uploads/praktik_baik/rekap-layanan-bengkel.pdf', 'Rekap 60 layanan servis', '2026-09-29 10:14:50'),
(11, 6, 'foto', 'jumat-mengaji.jpg', 'uploads/praktik_baik/jumat-mengaji.jpg', 'Kegiatan Jumat pagi', '2026-09-29 10:14:51');

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
(2, 1, NULL, NULL, 'Budi Santoso', 'budi.santoso@gmail.com', 'keluhan', 'Jadwal pengumpulan video kompetisi terlalu singkat untuk sekolah di wilayah kepulauan.', 'belum_ditindak', NULL, NULL, '2026-09-28 16:21:27', NULL),
(3, 2, NULL, 9, 'Yauma Neuvilete', 'smknegeri4kdi@gmail.com', 'keluhan', 'aduhh pak kenapa saya tidak cair sudah 3 bulan lebih. tolonglah kerja samanya. cape loh kerja ga di bayar bayar, mana PPG di jadikan gaji pokok yaAllah... mana sy anak yatim', 'belum_ditindak', NULL, NULL, '2026-09-29 05:11:52', NULL);

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
  MODIFY `id_admin` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `apresiasi`
--
ALTER TABLE `apresiasi`
  MODIFY `id_apresiasi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `bank_inovasi`
--
ALTER TABLE `bank_inovasi`
  MODIFY `id_inovasi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id_guru` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `kompetisi`
--
ALTER TABLE `kompetisi`
  MODIFY `id_kompetisi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kompetisi_kategori`
--
ALTER TABLE `kompetisi_kategori`
  MODIFY `id_kategori` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kompetisi_kriteria`
--
ALTER TABLE `kompetisi_kriteria`
  MODIFY `id_kriteria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT untuk tabel `kompetisi_nilai`
--
ALTER TABLE `kompetisi_nilai`
  MODIFY `id_nilai` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT untuk tabel `kompetisi_peserta`
--
ALTER TABLE `kompetisi_peserta`
  MODIFY `id_peserta` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `monev`
--
ALTER TABLE `monev`
  MODIFY `id_monev` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `praktik_baik`
--
ALTER TABLE `praktik_baik`
  MODIFY `id_praktik_baik` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `praktik_baik_dokumen`
--
ALTER TABLE `praktik_baik_dokumen`
  MODIFY `id_dokumen` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `id_sekolah` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `suara`
--
ALTER TABLE `suara`
  MODIFY `id_suara` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
