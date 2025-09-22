-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 29, 2025 at 01:08 AM
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
-- Database: `panahan_turnament_new`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GenerateTournamentBracket` (IN `p_tournament_id` INT, IN `p_category_id` INT)   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_participant_id INT;
    DECLARE v_match_number INT DEFAULT 1;
    DECLARE v_round_name VARCHAR(50) DEFAULT 'Round 1';
    DECLARE v_total_participants INT;
    
    DECLARE participant_cursor CURSOR FOR
        SELECT tp.id
        FROM tournament_participants tp
        WHERE tp.tournament_id = p_tournament_id 
            AND tp.category_id = p_category_id
            AND tp.status = 'confirmed'
        ORDER BY COALESCE(tp.seed_number, 999), tp.registration_date ASC;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    
    SELECT COUNT(*) INTO v_total_participants
    FROM tournament_participants tp
    WHERE tp.tournament_id = p_tournament_id 
        AND tp.category_id = p_category_id
        AND tp.status = 'confirmed';
    
    
    DELETE FROM matches 
    WHERE tournament_id = p_tournament_id 
        AND category_id = p_category_id;
    
    
    OPEN participant_cursor;
    
    read_loop: LOOP
        FETCH participant_cursor INTO v_participant_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        
        INSERT INTO matches (
            tournament_id, category_id, round_name, match_number,
            player1_id, status
        ) VALUES (
            p_tournament_id, p_category_id, v_round_name, v_match_number,
            v_participant_id, 'scheduled'
        );
        
        SET v_match_number = v_match_number + 1;
    END LOOP;
    
    CLOSE participant_cursor;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `min_age` int NOT NULL,
  `max_age` int NOT NULL,
  `gender` enum('Laki-laki','Perempuan','Campuran') DEFAULT 'Campuran',
  `max_participants` int DEFAULT '32',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `min_age`, `max_age`, `gender`, `max_participants`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Official', 0, 1000, 'Laki-laki', 16, 'active', '2025-08-16 04:37:59', '2025-08-27 05:28:28'),
(2, 'Shortbow NON Pelajar Putra jarak 20m', 0, 1000, 'Laki-laki', 16, 'active', '2025-08-16 04:37:59', '2025-08-27 05:28:05'),
(3, 'Shortbow NON Pelajar Putri jarak 20m', 0, 1000, 'Perempuan', 16, 'active', '2025-08-16 04:37:59', '2025-08-27 05:29:50'),
(4, 'Shortbow Pelajar Putra SD 1-3 jarak 5m', 6, 9, 'Laki-laki', 16, 'active', '2025-08-16 04:37:59', '2025-08-27 05:30:02'),
(5, 'Shortbow Pelajar Putra SD 4-6 jarak 7m', 10, 12, 'Laki-laki', 32, 'active', '2025-08-16 04:37:59', '2025-08-18 06:03:09'),
(6, 'Shortbow Pelajar Putra SMA jarak 15m', 15, 18, 'Laki-laki', 32, 'active', '2025-08-16 04:37:59', '2025-08-27 05:30:12'),
(7, 'Shortbow Pelajar Putri SMA jarak 15m', 15, 18, 'Perempuan', 32, 'active', '2025-08-16 04:37:59', '2025-08-27 05:30:21'),
(8, 'Shortbow Pelajar Putra SMP jarak 10m', 12, 15, 'Laki-laki', 32, 'active', '2025-08-16 04:37:59', '2025-08-27 05:30:33'),
(10, 'Shortbow Pelajar Putri SD 1-3 jarak 5m', 6, 9, 'Perempuan', 16, 'active', '2025-08-16 04:48:56', '2025-08-27 05:30:52'),
(11, 'Shortbow Pelajar Putri SD 4-6 jarak 7m', 10, 12, 'Perempuan', 16, 'active', '2025-08-16 04:48:56', '2025-08-18 06:07:43'),
(12, 'Shortbow Pelajar Putri SMA jarak 10m', 15, 18, 'Perempuan', 16, 'active', '2025-08-16 04:48:56', '2025-08-27 05:31:04'),
(13, 'Shortbow Pemula Putra jarak 3m', 4, 6, 'Laki-laki', 16, 'active', '2025-08-16 04:48:56', '2025-08-27 05:31:27'),
(14, 'Shortbow Pemula Putri jarak 3m', 4, 6, 'Perempuan', 32, 'active', '2025-08-16 04:48:56', '2025-08-27 05:31:37'),
(35, 'testt', 1, 11, 'Laki-laki', 32, 'active', '2025-08-26 23:00:03', '2025-08-26 23:00:03'),
(36, 'ppp', 1, 11, 'Campuran', 32, 'active', '2025-08-26 23:05:20', '2025-08-26 23:05:20'),
(37, 'r', 1, 11, 'Campuran', 32, 'active', '2025-08-27 05:29:16', '2025-08-27 05:29:16'),
(38, 'Shortbow Pelajar Putri SMP jarak 10m', 12, 15, 'Campuran', 32, 'active', '2025-08-28 08:31:05', '2025-08-28 08:31:05');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int NOT NULL,
  `nama_kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id`, `nama_kegiatan`) VALUES
(1, 'apasajala'),
(6, 'testing'),
(7, 'pp'),
(8, 'coba'),
(9, 'testi');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan_kategori`
--

CREATE TABLE `kegiatan_kategori` (
  `id` int NOT NULL,
  `kegiatan_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kegiatan_kategori`
--

INSERT INTO `kegiatan_kategori` (`id`, `kegiatan_id`, `category_id`) VALUES
(21, 6, 2),
(22, 6, 3),
(27, 8, 6),
(31, 7, 1),
(32, 7, 2),
(33, 7, 3),
(40, 1, 1),
(41, 1, 2),
(42, 1, 3),
(43, 1, 35),
(44, 1, 36),
(63, 9, 1),
(64, 9, 2),
(65, 9, 3),
(66, 9, 4),
(67, 9, 10),
(68, 9, 8),
(69, 9, 38);

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int NOT NULL,
  `tournament_id` int NOT NULL,
  `category_id` int NOT NULL,
  `round_name` varchar(50) NOT NULL,
  `match_number` int NOT NULL,
  `player1_id` int DEFAULT NULL,
  `player2_id` int DEFAULT NULL,
  `winner_id` int DEFAULT NULL,
  `scheduled_time` datetime DEFAULT NULL,
  `actual_start_time` datetime DEFAULT NULL,
  `actual_end_time` datetime DEFAULT NULL,
  `court_venue` varchar(100) DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled','bye') DEFAULT 'scheduled',
  `notes` text,
  `referee_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `tournament_id`, `category_id`, `round_name`, `match_number`, `player1_id`, `player2_id`, `winner_id`, `scheduled_time`, `actual_start_time`, `actual_end_time`, `court_venue`, `status`, `notes`, `referee_name`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 'Round 1', 1, 2, NULL, NULL, '2025-09-01 09:30:00', NULL, NULL, 'Court 2', 'scheduled', NULL, 'Wasit B', '2025-08-16 04:53:16', '2025-08-16 04:53:16'),
(2, 1, 11, 'Round 1', 1, 3, NULL, NULL, '2025-09-01 10:00:00', NULL, NULL, 'Court 1', 'scheduled', NULL, 'Wasit A', '2025-08-16 04:53:16', '2025-08-16 04:53:16'),
(3, 1, 12, 'Round 1', 1, 4, NULL, NULL, '2025-09-01 10:30:00', NULL, NULL, 'Court 2', 'scheduled', NULL, 'Wasit B', '2025-08-16 04:53:16', '2025-08-16 04:53:16'),
(4, 1, 13, 'Round 1', 1, 5, NULL, NULL, '2025-09-01 11:00:00', NULL, NULL, 'Court 1', 'scheduled', NULL, 'Wasit C', '2025-08-16 04:53:16', '2025-08-16 04:53:16'),
(5, 1, 14, 'Round 1', 1, NULL, NULL, NULL, '2025-09-01 11:30:00', NULL, NULL, 'Court 2', 'scheduled', NULL, 'Wasit C', '2025-08-16 04:53:16', '2025-08-16 04:53:16');

-- --------------------------------------------------------

--
-- Table structure for table `match_results`
--

CREATE TABLE `match_results` (
  `id` int NOT NULL,
  `match_id` int NOT NULL,
  `set_number` int NOT NULL,
  `player1_score` int DEFAULT '0',
  `player2_score` int DEFAULT '0',
  `duration_minutes` int DEFAULT NULL,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Laki-laki','Perempuan') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`id`, `name`, `birthdate`, `gender`, `email`, `phone`, `address`, `emergency_contact`, `emergency_phone`, `photo`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ahmad Fauzi', '2010-05-15', 'Laki-laki', 'ahmad.fauzi@email.com', '08123456789', NULL, NULL, NULL, NULL, 'active', '2025-08-16 04:53:15', '2025-08-16 04:53:15'),
(2, 'Siti Nurhaliza', '2011-08-20', 'Perempuan', 'siti.nurhaliza@email.com', '08234567890', NULL, NULL, NULL, NULL, 'active', '2025-08-16 04:53:15', '2025-08-16 04:53:15'),
(3, 'Budi Santoso', '2008-03-10', 'Laki-laki', 'budi.santoso@email.com', '08345678901', NULL, NULL, NULL, NULL, 'active', '2025-08-16 04:53:15', '2025-08-16 04:53:15'),
(4, 'Dewi Sartika', '2009-12-05', 'Perempuan', 'dewi.sartika@email.com', '08456789012', NULL, NULL, NULL, NULL, 'active', '2025-08-16 04:53:15', '2025-08-16 04:53:15'),
(5, 'Rafi Ahmad', '2006-07-18', 'Laki-laki', 'rafi.ahmad@email.com', '08567890123', NULL, NULL, NULL, NULL, 'active', '2025-08-16 04:53:15', '2025-08-16 04:53:15'),
(6, 'Maya Putri', '2007-11-25', 'Perempuan', 'maya.putri@email.com', '08678901234', NULL, NULL, NULL, NULL, 'active', '2025-08-16 04:53:15', '2025-08-16 04:53:15'),
(7, 'Doni Pratama', '1995-04-12', 'Laki-laki', 'doni.pratama@email.com', '08789012345', NULL, NULL, NULL, NULL, 'active', '2025-08-16 04:53:15', '2025-08-16 04:53:15'),
(9, 'test', '2025-08-08', 'Laki-laki', 'test@test.com', '0812345', '-', '-', '-', NULL, 'active', '2025-08-18 05:38:10', '2025-08-18 05:38:10');

-- --------------------------------------------------------

--
-- Stand-in structure for view `participant_eligible_categories`
-- (See below for the actual view)
--
CREATE TABLE `participant_eligible_categories` (
`age` int
,`category_id` int
,`category_name` varchar(50)
,`gender` enum('Laki-laki','Perempuan')
,`max_age` int
,`min_age` int
,`participant_id` int
,`participant_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `peserta`
--

CREATE TABLE `peserta` (
  `id` int NOT NULL,
  `nama_peserta` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `asal_kota` varchar(100) DEFAULT NULL,
  `nama_club` varchar(100) DEFAULT NULL,
  `sekolah` varchar(100) DEFAULT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `nomor_hp` varchar(20) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `kegiatan_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peserta`
--

INSERT INTO `peserta` (`id`, `nama_peserta`, `tanggal_lahir`, `jenis_kelamin`, `asal_kota`, `nama_club`, `sekolah`, `kelas`, `nomor_hp`, `category_id`, `kegiatan_id`) VALUES
(1, 'test', '2025-08-01', 'Laki-laki', 'Balikpapan', 'Pova Archery', 'STM', 'XII Alat Berat', '0821128311', NULL, NULL),
(2, 'test', '2025-08-01', 'Laki-laki', 'Balikpapan', 'Pova Archery', 'STM', 'XII Alat Berat', '0821128311', 1, NULL),
(3, 'testt', '2025-07-31', 'Perempuan', 'Balikpapan', 'Pova Archery', 'STM', 'XII Alat Berat', '0821128311', 2, 1),
(4, 'ziya', '2016-01-28', 'Laki-laki', 'Samarinda', 'Airlangga Club', 'Airlangga', '3 sd', '0812345161', 1, 9),
(5, 'yaya', '2017-01-01', 'Laki-laki', 'Samarinda', 'Airlangga Club', 'Airlangga', '3 sd', '08123456789', 2, 9),
(6, 'qiyam', '2017-06-06', 'Laki-laki', 'Samarinda', 'Airlangga Club', 'Airlangga', '3 sd', '0812334411', 4, 9),
(7, 'lia', '2000-01-05', 'Perempuan', 'Samarinda', 'Airlangga Club', 'Airlangga', '12', '08771219211', 3, 9),
(8, 'wijaya', '2011-01-04', 'Laki-laki', 'Samarinda', 'Airlangga Club', 'Airlangga', '13', '08991273161', 8, 9),
(9, 'jihan', '2013-02-05', 'Perempuan', 'Samarinda', 'Airlangga Club', 'Airlangga', '9', '0874445778686', 38, 9);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `value` text,
  `description` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key_name`, `value`, `description`, `updated_at`) VALUES
(1, 'site_name', 'Tournament Management System', 'Nama aplikasi', '2025-08-16 04:53:15'),
(2, 'site_logo', '/assets/images/logo.png', 'Path logo aplikasi', '2025-08-16 04:53:15'),
(3, 'default_match_duration', '60', 'Durasi default pertandingan dalam menit', '2025-08-16 04:53:15'),
(4, 'timezone', 'Asia/Jakarta', 'Timezone aplikasi', '2025-08-16 04:53:15'),
(5, 'currency', 'IDR', 'Mata uang yang digunakan', '2025-08-16 04:53:15'),
(6, 'max_upload_size', '5MB', 'Ukuran maksimal upload file', '2025-08-16 04:53:15'),
(7, 'contact_email', 'info@tournament.com', 'Email kontak', '2025-08-16 04:53:15'),
(8, 'contact_phone', '+62-21-1234567', 'Nomor telepon kontak', '2025-08-16 04:53:15');

-- --------------------------------------------------------

--
-- Table structure for table `tournaments`
--

CREATE TABLE `tournaments` (
  `id` int NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `registration_start` datetime NOT NULL,
  `registration_end` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `entry_fee` decimal(10,2) DEFAULT '0.00',
  `prize_pool` decimal(10,2) DEFAULT '0.00',
  `max_participants` int DEFAULT '32',
  `tournament_type` enum('single_elimination','double_elimination','round_robin','swiss') DEFAULT 'single_elimination',
  `status` enum('draft','registration','ongoing','completed','cancelled') DEFAULT 'draft',
  `rules` text,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tournaments`
--

INSERT INTO `tournaments` (`id`, `name`, `description`, `start_date`, `end_date`, `registration_start`, `registration_end`, `location`, `entry_fee`, `prize_pool`, `max_participants`, `tournament_type`, `status`, `rules`, `contact_person`, `contact_phone`, `poster`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Turnamen Panahan 2025', '-', '2025-09-01 08:00:00', '2025-09-03 18:00:00', '2025-08-01 00:00:00', '2025-08-25 23:59:59', '-', '150000.00', '50000000.00', 128, 'single_elimination', 'registration', NULL, NULL, NULL, NULL, 1, '2025-08-16 04:37:59', '2025-08-18 05:14:23'),
(2, 'Turnamen Badminton Nusantara 2025', 'Turnamen badminton tingkat nasional dengan berbagai kategori usia', '2025-09-01 08:00:00', '2025-09-03 18:00:00', '2025-08-01 00:00:00', '2025-08-25 23:59:59', 'GOR Senayan, Jakarta', '150000.00', '50000000.00', 128, 'single_elimination', 'registration', NULL, NULL, NULL, NULL, 1, '2025-08-16 04:48:56', '2025-08-16 04:48:56'),
(4, 'Testing', '-', '2025-08-01 11:44:38', '2025-08-02 11:44:38', '2025-08-01 11:44:38', '2025-08-05 11:44:38', '-', '1.10', '1.00', 32, 'single_elimination', 'registration', '-', '-', '-', '-', 1, '2025-08-01 03:44:38', '2025-08-01 03:44:38');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_categories`
--

CREATE TABLE `tournament_categories` (
  `id` int NOT NULL,
  `tournament_id` int NOT NULL,
  `category_id` int NOT NULL,
  `max_participants` int DEFAULT '32',
  `entry_fee` decimal(10,2) DEFAULT '0.00',
  `prize_1st` decimal(10,2) DEFAULT '0.00',
  `prize_2nd` decimal(10,2) DEFAULT '0.00',
  `prize_3rd` decimal(10,2) DEFAULT '0.00',
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tournament_categories`
--

INSERT INTO `tournament_categories` (`id`, `tournament_id`, `category_id`, `max_participants`, `entry_fee`, `prize_1st`, `prize_2nd`, `prize_3rd`, `status`) VALUES
(1, 1, 1, 32, '0.00', '0.00', '0.00', '0.00', 'active'),
(2, 1, 2, 32, '0.00', '0.00', '0.00', '0.00', 'active'),
(3, 1, 3, 32, '0.00', '0.00', '0.00', '0.00', 'active'),
(4, 1, 4, 32, '0.00', '0.00', '0.00', '0.00', 'active'),
(5, 1, 5, 32, '0.00', '0.00', '0.00', '0.00', 'active'),
(6, 1, 6, 32, '0.00', '0.00', '0.00', '0.00', 'active'),
(7, 1, 7, 32, '0.00', '0.00', '0.00', '0.00', 'active'),
(8, 1, 8, 32, '0.00', '0.00', '0.00', '0.00', 'active'),
(16, 1, 10, 16, '100000.00', '2000000.00', '1000000.00', '500000.00', 'active'),
(17, 1, 11, 16, '150000.00', '3000000.00', '1500000.00', '750000.00', 'active'),
(18, 1, 12, 16, '150000.00', '3000000.00', '1500000.00', '750000.00', 'active'),
(19, 1, 13, 32, '200000.00', '5000000.00', '2500000.00', '1250000.00', 'active'),
(20, 1, 14, 32, '200000.00', '5000000.00', '2500000.00', '1250000.00', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_participants`
--

CREATE TABLE `tournament_participants` (
  `id` int NOT NULL,
  `tournament_id` int NOT NULL,
  `participant_id` int NOT NULL,
  `category_id` int NOT NULL,
  `registration_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `seed_number` int DEFAULT NULL,
  `status` enum('registered','confirmed','withdrew','disqualified') DEFAULT 'registered',
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tournament_participants`
--

INSERT INTO `tournament_participants` (`id`, `tournament_id`, `participant_id`, `category_id`, `registration_date`, `payment_status`, `payment_date`, `seed_number`, `status`, `notes`) VALUES
(1, 1, 2, 10, '2025-08-16 04:53:16', 'pending', NULL, NULL, 'confirmed', NULL),
(2, 1, 3, 11, '2025-08-16 04:53:16', 'pending', NULL, NULL, 'confirmed', NULL),
(3, 1, 4, 12, '2025-08-16 04:53:16', 'pending', NULL, NULL, 'confirmed', NULL),
(4, 1, 5, 13, '2025-08-16 04:53:16', 'pending', NULL, NULL, 'confirmed', NULL),
(5, 1, 6, 14, '2025-08-16 04:53:16', 'pending', NULL, NULL, 'confirmed', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `tournament_summary`
-- (See below for the actual view)
--
CREATE TABLE `tournament_summary` (
`end_date` datetime
,`id` int
,`location` varchar(255)
,`name` varchar(200)
,`start_date` datetime
,`status` enum('draft','registration','ongoing','completed','cancelled')
,`total_categories` bigint
,`total_participants` bigint
,`total_revenue` decimal(52,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator','viewer') DEFAULT 'operator',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@tournament.com', '11', 'admin', 'active', '2025-08-16 04:37:59', '2025-08-16 06:24:20'),
(2, 'operator', 'operator1@tournament.com', '$2y$10$Phj2F0oy0pO5ChWDKDMe.O8w/u4nPXyTt8zD7kO3tY/dfVsvmID26', 'operator', 'active', '2025-08-16 04:37:59', '2025-08-18 06:15:22'),
(3, 'Viewer', 'viewer@tournament.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'viewer', 'active', '2025-08-16 04:37:59', '2025-08-16 04:37:59'),
(10, 'testing', '123@gmail.com', '$2y$10$2YBZaY5miPUmxtVSK4X1t.1kQ6EwNf7ZRIes.k4icTZNVhdzMug6K', 'operator', 'active', '2025-08-18 06:19:04', '2025-08-18 06:19:04');

-- --------------------------------------------------------

--
-- Structure for view `participant_eligible_categories`
--
DROP TABLE IF EXISTS `participant_eligible_categories`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `participant_eligible_categories`  AS SELECT `p`.`id` AS `participant_id`, `p`.`name` AS `participant_name`, (year(curdate()) - year(`p`.`birthdate`)) AS `age`, `p`.`gender` AS `gender`, `c`.`id` AS `category_id`, `c`.`name` AS `category_name`, `c`.`min_age` AS `min_age`, `c`.`max_age` AS `max_age` FROM (`participants` `p` join `categories` `c`) WHERE (((year(curdate()) - year(`p`.`birthdate`)) between `c`.`min_age` and `c`.`max_age`) AND ((`c`.`gender` = `p`.`gender`) OR (`c`.`gender` = 'Campuran')) AND (`p`.`status` = 'active') AND (`c`.`status` = 'active'))  ;

-- --------------------------------------------------------

--
-- Structure for view `tournament_summary`
--
DROP TABLE IF EXISTS `tournament_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tournament_summary`  AS SELECT `t`.`id` AS `id`, `t`.`name` AS `name`, `t`.`start_date` AS `start_date`, `t`.`end_date` AS `end_date`, `t`.`status` AS `status`, `t`.`location` AS `location`, count(distinct `tp`.`participant_id`) AS `total_participants`, count(distinct `tc`.`category_id`) AS `total_categories`, sum((`tc`.`entry_fee` * (select count(0) from `tournament_participants` `tp2` where ((`tp2`.`tournament_id` = `t`.`id`) and (`tp2`.`category_id` = `tc`.`category_id`))))) AS `total_revenue` FROM ((`tournaments` `t` left join `tournament_categories` `tc` on((`t`.`id` = `tc`.`tournament_id`))) left join `tournament_participants` `tp` on((`t`.`id` = `tp`.`tournament_id`))) GROUP BY `t`.`id`, `t`.`name`, `t`.`start_date`, `t`.`end_date`, `t`.`status`, `t`.`location``location`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kegiatan_kategori`
--
ALTER TABLE `kegiatan_kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`),
  ADD KEY `winner_id` (`winner_id`);

--
-- Indexes for table `match_results`
--
ALTER TABLE `match_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- Indexes for table `peserta`
--
ALTER TABLE `peserta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_peserta_category` (`category_id`),
  ADD KEY `fk_peserta_kegiatan` (`kegiatan_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name` (`key_name`);

--
-- Indexes for table `tournaments`
--
ALTER TABLE `tournaments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_tournaments_dates` (`start_date`,`end_date`),
  ADD KEY `idx_tournaments_status` (`status`);

--
-- Indexes for table `tournament_categories`
--
ALTER TABLE `tournament_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tournament_category_unique` (`tournament_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tournament_participants`
--
ALTER TABLE `tournament_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tournament_participant_unique` (`tournament_id`,`participant_id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `kegiatan_kategori`
--
ALTER TABLE `kegiatan_kategori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `match_results`
--
ALTER TABLE `match_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `peserta`
--
ALTER TABLE `peserta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tournaments`
--
ALTER TABLE `tournaments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tournament_categories`
--
ALTER TABLE `tournament_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tournament_participants`
--
ALTER TABLE `tournament_participants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`player1_id`) REFERENCES `tournament_participants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matches_ibfk_4` FOREIGN KEY (`player2_id`) REFERENCES `tournament_participants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matches_ibfk_5` FOREIGN KEY (`winner_id`) REFERENCES `tournament_participants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `match_results`
--
ALTER TABLE `match_results`
  ADD CONSTRAINT `match_results_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peserta`
--
ALTER TABLE `peserta`
  ADD CONSTRAINT `fk_peserta_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_peserta_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tournaments`
--
ALTER TABLE `tournaments`
  ADD CONSTRAINT `tournaments_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tournament_categories`
--
ALTER TABLE `tournament_categories`
  ADD CONSTRAINT `tournament_categories_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tournament_participants`
--
ALTER TABLE `tournament_participants`
  ADD CONSTRAINT `tournament_participants_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_participants_ibfk_2` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_participants_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
