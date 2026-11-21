-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2026 at 08:44 AM
-- Server version: 10.11.19-MariaDB
-- PHP Version: 8.5.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bobo_klinik`
--

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id`, `nama`) VALUES
(1, 'Dr. Anthony');

-- --------------------------------------------------------

--
-- Table structure for table `hewan`
--

CREATE TABLE `hewan` (
  `id` int(11) NOT NULL,
  `pemilik_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_hewan` enum('Anjing','Kucing','Kera','Unggas','Lainnya') NOT NULL,
  `ras` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hewan`
--

INSERT INTO `hewan` (`id`, `pemilik_id`, `nama`, `jenis_hewan`, `ras`) VALUES
(1, 1, 'Golden', 'Anjing', 'Golden Retriever');

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id`, `nama`) VALUES
(6, 'Collar'),
(5, 'Foto X Ray'),
(7, 'House Call'),
(1, 'Konsultasi'),
(8, 'Lain-lain'),
(4, 'Obat-obatan'),
(3, 'Operasi'),
(2, 'Vaksinasi');

-- --------------------------------------------------------

--
-- Table structure for table `nota`
--

CREATE TABLE `nota` (
  `id` int(11) NOT NULL,
  `no_nota` varchar(30) NOT NULL,
  `tanggal` date NOT NULL,
  `pemilik_id` int(11) NOT NULL,
  `hewan_id` int(11) NOT NULL,
  `dokter_id` int(11) NOT NULL,
  `total` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nota`
--

INSERT INTO `nota` (`id`, `no_nota`, `tanggal`, `pemilik_id`, `hewan_id`, `dokter_id`, `total`, `user_id`) VALUES
(1, 'BK-2026-00001', '2026-11-21', 1, 1, 1, 150000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `nota_detail`
--

CREATE TABLE `nota_detail` (
  `id` int(11) NOT NULL,
  `nota_id` int(11) NOT NULL,
  `layanan_id` int(11) NOT NULL,
  `keterangan` varchar(200) DEFAULT NULL,
  `harga` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nota_detail`
--

INSERT INTO `nota_detail` (`id`, `nota_id`, `layanan_id`, `keterangan`, `harga`) VALUES
(1, 1, 1, 'Sakit', 100000),
(2, 1, 4, 'Obat Flu', 50000);

-- --------------------------------------------------------

--
-- Table structure for table `pemilik`
--

CREATE TABLE `pemilik` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `telepon` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pemilik`
--

INSERT INTO `pemilik` (`id`, `nama`, `alamat`, `telepon`) VALUES
(1, 'Tristan', 'Tangerang', '081234567890');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`) VALUES
(1, 'admin', '$2y$12$tGJlXzX1qzabwSP2Uyaph.tT9v23vpxGH7hQQGaXR5D0iRErqBjyq', 'Administrator');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hewan`
--
ALTER TABLE `hewan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pemilik_id` (`pemilik_id`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indexes for table `nota`
--
ALTER TABLE `nota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_nota` (`no_nota`),
  ADD KEY `pemilik_id` (`pemilik_id`),
  ADD KEY `hewan_id` (`hewan_id`),
  ADD KEY `dokter_id` (`dokter_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `nota_detail`
--
ALTER TABLE `nota_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nota_id` (`nota_id`),
  ADD KEY `layanan_id` (`layanan_id`);

--
-- Indexes for table `pemilik`
--
ALTER TABLE `pemilik`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hewan`
--
ALTER TABLE `hewan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `nota`
--
ALTER TABLE `nota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `nota_detail`
--
ALTER TABLE `nota_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pemilik`
--
ALTER TABLE `pemilik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hewan`
--
ALTER TABLE `hewan`
  ADD CONSTRAINT `hewan_ibfk_1` FOREIGN KEY (`pemilik_id`) REFERENCES `pemilik` (`id`);

--
-- Constraints for table `nota`
--
ALTER TABLE `nota`
  ADD CONSTRAINT `nota_ibfk_1` FOREIGN KEY (`pemilik_id`) REFERENCES `pemilik` (`id`),
  ADD CONSTRAINT `nota_ibfk_2` FOREIGN KEY (`hewan_id`) REFERENCES `hewan` (`id`),
  ADD CONSTRAINT `nota_ibfk_3` FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`id`),
  ADD CONSTRAINT `nota_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `nota_detail`
--
ALTER TABLE `nota_detail`
  ADD CONSTRAINT `nota_detail_ibfk_1` FOREIGN KEY (`nota_id`) REFERENCES `nota` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nota_detail_ibfk_2` FOREIGN KEY (`layanan_id`) REFERENCES `layanan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
