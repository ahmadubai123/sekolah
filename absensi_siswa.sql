-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Des 2025 pada 15.48
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_siswa`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam` time DEFAULT NULL,
  `keterangan` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id`, `user_id`, `tanggal`, `jam`, `keterangan`) VALUES
(1, 3, '2025-12-17', '13:56:39', 'Hadir');

-- --------------------------------------------------------

--
-- Struktur dari tabel `qr_absen`
--

CREATE TABLE `qr_absen` (
  `id` int(11) NOT NULL,
  `token` varchar(100) DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `qr_absen`
--

INSERT INTO `qr_absen` (`id`, `token`, `tanggal`) VALUES
(1, '66484b5214650b92cdbf933f17f9930e', '2025-12-17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(50) NOT NULL,
  `role` enum('admin','siswa') NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `email_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `register_time` time DEFAULT curtime()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `email`, `password`, `role`, `email_verified`, `email_token`, `created_at`, `register_time`) VALUES
(1, 'Ahmad Ubaidillah', 'ahmad ubaidillah', NULL, 'admin123', 'admin', 0, NULL, '2025-12-17 07:32:19', '20:54:45'),
(5, 'AHMAD HAFIZ', 'siswa1', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(6, 'AHMAD HERDIANSYAH', 'siswa2', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(7, 'ALIVEA AL CAHYA', 'siswa3', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(8, 'ALYSSA AZZAHRA', 'siswa4', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(9, 'ARYA GENTA ILHAM', 'siswa5', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(10, 'DEBY MAULIDA', 'siswa6', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(11, 'FAJAR ARIFIKRY', 'siswa7', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(12, 'FATHIYA RAFIFAH', 'siswa8', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(13, 'FIRDA AWALIYAH FITRIANI FITRI NUR', 'siswa9', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(14, 'AWALIYAH IKMAL NURRAMADHAN', 'siswa10', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(15, 'IRMA AMALIA', 'siswa11', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(16, 'KARIMAH KHANZA HUMAERA', 'siswa12', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(17, 'KHOERUN NISA MUHAMAD RADITIYA', 'siswa13', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(18, 'MUHAMAD ZAKARIA', 'siswa14', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(19, 'MUHAMMAD ALIEGA SAPUTRA', 'siswa15', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(20, 'MUHAMMAD IKBAL IBRAHIM', 'siswa16', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(21, 'NABILA AMZA KHAIRANI', 'siswa17', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(22, 'NADA ISLAH JAHAN', 'siswa18', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(23, 'NESYA AZMI UTAMI', 'siswa19', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(24, 'NUR AFIFA SAFITRI', 'siswa20', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(25, 'OPSA OCTAR HARI', 'siswa21', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(26, 'RAFLI JULIANSYAH', 'siswa22', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(27, 'RAMA AL-VALIN', 'siswa23', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(28, 'SARIP MAULANA', 'siswa24', NULL, '123', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(29, 'SITI NIHLAH SITI NUR MAULIDA', 'siswa25', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(30, 'SITI NURAINI SITI SOFWATUN NISA', 'siswa26', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(31, 'ZULMI IKHWAN AL-FATIH', 'siswa27', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(32, 'HAURA AWALIA', 'siswa28', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:14:09', '21:14:09'),
(39, 'HARI RAFLI', 'siswa37', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:21:36', '21:21:36'),
(40, 'JULIANSYAH', 'siswa38', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:21:36', '21:21:36'),
(41, 'RAMA AL-VALIN', 'siswa39', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:21:36', '21:21:36'),
(42, 'SARIP MAULANA', 'siswa40', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:21:36', '21:21:36'),
(43, 'SITI NIHLAH SITI NUR MAULIDA', 'siswa41', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:21:36', '21:21:36'),
(44, 'SITI NURAINI', 'siswa42', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:21:36', '21:21:36'),
(63, 'HARI RAFLI', 'siswa31', NULL, '$2y$10$2b2Bz0d9T3Qy8F4aY0QkU.7BvQzD1B0P6y6cF0b8o9Y', 'siswa', 0, NULL, '2025-12-17 14:31:05', '21:31:05');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `qr_absen`
--
ALTER TABLE `qr_absen`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `qr_absen`
--
ALTER TABLE `qr_absen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
