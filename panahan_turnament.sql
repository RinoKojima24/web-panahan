-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 15, 2025 at 10:42 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `panahan_turnament`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `min_age` int NOT NULL,
  `max_age` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `min_age`, `max_age`) VALUES
(1, 'U-12', 0, 12),
(2, 'U-15', 13, 15),
(3, 'U-18', 16, 18),
(4, 'Dewasa', 19, 100),
(5, 'U-12', 0, 12),
(6, 'U-15', 13, 15),
(7, 'U-18', 16, 18),
(8, 'Dewasa', 19, 100);

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `category_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`id`, `name`, `birthdate`, `email`, `phone`, `category_id`, `created_at`) VALUES
(1, 'Awan', '2025-08-07', 'wanwanwan@gmail.com', '08881223131', 1, '2025-08-05 02:03:47'),
(2, 'Ferdi', '2021-07-07', 'dididifer@gmail.com', '012012832', 5, '2025-08-05 02:13:18'),
(3, 'silsilah', '2008-07-29', 'silsilsil@gmail.com', '08167263711', 3, '2025-08-05 02:47:37'),
(4, 'elelelsa', '2008-08-13', 'elelelsa@gmail.com', '086716276317', 7, '2025-08-05 02:55:43');

-- --------------------------------------------------------

--
-- Table structure for table `peserta`
--

CREATE TABLE `peserta` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `kategori` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peserta`
--

INSERT INTO `peserta` (`id`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `kategori`, `email`, `phone`, `created_at`) VALUES
(1, 'elelelsa', '2025-08-07', 'Laki-laki', 'U-12', 'elelelsa@gmail.com', '086716276317', '2025-08-08 03:52:51'),
(2, 'elelelsa', '2025-08-07', 'Laki-laki', 'U-12', 'elelelsa@gmail.com', '086716276317', '2025-08-08 03:53:21'),
(3, 'elelelsa', '2025-08-07', 'Laki-laki', 'U-12', 'elelelsa@gmail.com', '086716276317', '2025-08-08 03:53:26'),
(4, 'tuntung', '2025-08-19', 'Laki-laki', 'U-12', 'nananaa@nanana', '09871827138', '2025-08-08 06:21:52'),
(5, 'tungggg', '2025-07-30', 'Laki-laki', 'U-12', 'adkak@kajssda', '197278178231', '2025-08-08 06:39:36'),
(6, 'ahdalda', '2025-07-29', 'Laki-laki', 'U-12', 'alknsdan@lakdnas', '12719072313', '2025-08-08 06:41:46'),
(7, 'yayay', '2025-07-29', 'Laki-laki', '5', 'aoadj@ajjah', '08761625', '2025-08-08 06:56:55'),
(8, 'asdananda', '2028-10-18', 'Laki-laki', '5', 'aaa2@aa', '0879176311', '2025-08-08 07:08:29'),
(9, 'aa', '2006-02-09', 'Perempuan', '4', 'AODOASODAO@AODSAO', '0979782632', '2025-08-08 07:09:45'),
(10, 'aaaaa', '2025-08-13', 'Laki-laki', '5', 'aaa@aaa', '1234314124', '2025-08-10 13:28:53'),
(11, 'ropiq', '2025-08-06', 'Laki-laki', 'U-12', 'ropi123@q', '087812121', '2025-08-10 13:43:44'),
(12, 'blabla', '2003-10-22', 'Laki-laki', 'Dewasa', 'aa@aa', '08672112', '2025-08-10 14:11:44'),
(13, 'Yoga', '2008-07-11', 'Laki-laki', 'U-18', 'yoga@yogayoga', '08921531131', '2025-08-11 01:50:33'),
(14, 'jamal', '2010-02-02', 'Laki-laki', 'U-15', 'aiaiaia@ausjada', '08665126162', '2025-08-11 03:39:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(2, 'mimin27', 'admin@gmail.com', 'mimin27'),
(3, 'Admin Turnamen', 'admin@example.com', '$2y$10$IQ.xekN3n1bQgANzOh3UsOCzoK3KfyQYdlzZ.c/wEBooSCh3e4uFG'),
(4, 'mimin27', 'mimin27@gmail.com', '12345'),
(5, '1122', 'abc@gmail.com', '1122'),
(6, 'admin', 'adminmin@gmail.com', '11'),
(7, 'mimin27', 'mimin27@gmail.com', '00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `peserta`
--
ALTER TABLE `peserta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `peserta`
--
ALTER TABLE `peserta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
