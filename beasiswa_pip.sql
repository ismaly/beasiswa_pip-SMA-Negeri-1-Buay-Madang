-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 25, 2024 at 12:17 PM
-- Server version: 8.0.30
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `beasiswa_pip`
--

-- --------------------------------------------------------

--
-- Table structure for table `beasiswa`
--

CREATE TABLE `beasiswa` (
  `kd_beasiswa` int NOT NULL,
  `nama_beasiswa` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `beasiswa`
--

INSERT INTO `beasiswa` (`kd_beasiswa`, `nama_beasiswa`) VALUES
(1, 'Beasiswa PIP');

-- --------------------------------------------------------

--
-- Table structure for table `hasil`
--

CREATE TABLE `hasil` (
  `kd_hasil` int NOT NULL,
  `kd_beasiswa` int NOT NULL,
  `nisn` char(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nilai` float DEFAULT NULL,
  `tahun_mengajukan` char(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id` int NOT NULL,
  `kd_kriteria` varchar(55) NOT NULL,
  `kd_beasiswa` int NOT NULL,
  `nama` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `sifat` enum('cost','benefit') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `bobot` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id`, `kd_kriteria`, `kd_beasiswa`, `nama`, `sifat`, `bobot`) VALUES
(1, 'C1', 1, 'Penghasilan Orang Tua', 'cost', '30'),
(2, 'C2', 1, 'Tanggungan Orang Tua', 'benefit', '15'),
(3, 'C3', 1, 'Jumlah Saudara Kandung', 'benefit', '10'),
(4, 'C4', 1, 'Status Orang Tua (hidup/mati)', 'cost', '25'),
(5, 'C5', 1, 'Kepemilikan Rumah', 'benefit', '20');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `kd_nilai` int NOT NULL,
  `kd_beasiswa` varchar(55) DEFAULT NULL,
  `kd_kriteria` varchar(55) DEFAULT NULL,
  `nisn` varchar(55) DEFAULT NULL,
  `nilai` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`kd_nilai`, `kd_beasiswa`, `kd_kriteria`, `nisn`, `nilai`) VALUES
(1, '1', 'C1', '12345', 4),
(2, '1', 'C2', '12345', 3),
(3, '1', 'C3', '12345', 3),
(4, '1', 'C4', '12345', 3),
(5, '1', 'C5', '12345', 2),
(6, '1', 'C1', '678910', 4),
(7, '1', 'C2', '678910', 4),
(8, '1', 'C3', '678910', 3),
(9, '1', 'C4', '678910', 3),
(10, '1', 'C5', '678910', 4),
(11, '1', 'C1', '109876', 4),
(12, '1', 'C2', '109876', 2),
(13, '1', 'C3', '109876', 2),
(14, '1', 'C4', '109876', 3),
(15, '1', 'C5', '109876', 2),
(16, '1', 'C1', '54321', 4),
(17, '1', 'C2', '54321', 2),
(18, '1', 'C3', '54321', 2),
(19, '1', 'C4', '54321', 3),
(20, '1', 'C5', '54321', 2),
(21, '1', 'C1', '112345', 3),
(22, '1', 'C2', '112345', 3),
(23, '1', 'C3', '112345', 3),
(24, '1', 'C4', '112345', 3),
(25, '1', 'C5', '112345', 2);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `kd_pengguna` int NOT NULL,
  `username` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `role` enum('petugas','siswa') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`kd_pengguna`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$GJpYGJKXt1Fh2ZsEphyTIuMY.Vqxx3b32CFTZTwnqRjLxqMQLpo4C', 'petugas');

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `kd_penilaian` int NOT NULL,
  `kd_beasiswa` int DEFAULT NULL,
  `kd_kriteria` int NOT NULL,
  `keterangan` varchar(20) NOT NULL,
  `bobot` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perangkingan`
--

CREATE TABLE `perangkingan` (
  `id` int NOT NULL,
  `kode_siswa` varchar(50) DEFAULT NULL,
  `nisn` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `perangkingan` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `kode_siswa` varchar(50) NOT NULL,
  `nisn` char(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `alamat` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `tahun_mengajukan` enum('2023','2024','2025','2026','2027') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `periode` enum('Tahap1','Tahap2') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`kode_siswa`, `nisn`, `nama`, `alamat`, `jenis_kelamin`, `tahun_mengajukan`, `periode`) VALUES
('A1', '12345', 'Novi Kharesa', 'Oku Timur', 'Perempuan', '2023', 'Tahap1'),
('A2', '678910', 'Fahmi Hasril', 'Oku Timur', 'Laki-laki', '2023', 'Tahap1'),
('A3', '109876', 'Ani Zakiyah', 'Oku Timur', 'Perempuan', '2023', 'Tahap1'),
('A4', '54321', 'Okta', 'Oku Timur', 'Perempuan', '2023', 'Tahap1'),
('A5', '112345', 'Subin', 'Oku Timur', 'Perempuan', '2023', 'Tahap1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beasiswa`
--
ALTER TABLE `beasiswa`
  ADD PRIMARY KEY (`kd_beasiswa`);

--
-- Indexes for table `hasil`
--
ALTER TABLE `hasil`
  ADD PRIMARY KEY (`kd_hasil`),
  ADD KEY `fk_siswa` (`nisn`),
  ADD KEY `fk_beasiswa` (`kd_beasiswa`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kd_beasiswa` (`kd_beasiswa`) USING BTREE,
  ADD KEY `kd_beasiswa_2` (`kd_beasiswa`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`kd_nilai`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`kd_pengguna`);

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`kd_penilaian`),
  ADD KEY `fk_kriteria` (`kd_kriteria`),
  ADD KEY `fk_beasiswa` (`kd_beasiswa`);

--
-- Indexes for table `perangkingan`
--
ALTER TABLE `perangkingan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`kode_siswa`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beasiswa`
--
ALTER TABLE `beasiswa`
  MODIFY `kd_beasiswa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hasil`
--
ALTER TABLE `hasil`
  MODIFY `kd_hasil` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `kd_nilai` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `kd_pengguna` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `kd_penilaian` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perangkingan`
--
ALTER TABLE `perangkingan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
