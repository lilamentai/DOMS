-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2025 at 05:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `donation_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_data`
--

CREATE TABLE `admin_data` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rekening_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(50) NOT NULL,
  `rekening_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `donor_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `status` enum('pending','confirmed') NOT NULL DEFAULT 'pending',
  `deleted_admin_temp` enum('yes','no') NOT NULL DEFAULT 'no',
  `tujuan_donasi` varchar(100) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `nama_bank` varchar(50) DEFAULT NULL,
  `is_anonim` tinyint(100) NOT NULL DEFAULT 0,
  `tanggal` datetime DEFAULT current_timestamp(),
  `rekening_number` varchar(50) DEFAULT NULL,
  `target_amount` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `username`, `donor_name`, `phone`, `amount`, `created_at`, `status`, `deleted_admin_temp`, `tujuan_donasi`, `bukti_transfer`, `nama_bank`, `is_anonim`, `tanggal`, `rekening_number`, `target_amount`) VALUES
(67, 'amel', 'amel', NULL, 0.00, '2025-10-15 17:18:39', 'pending', 'no', 'PANTI ASUHAN', NULL, NULL, 0, '2025-10-15 22:18:39', NULL, 2000000),
(70, 'amel', 'amel', NULL, 0.00, '2025-10-15 17:18:39', 'confirmed', 'no', 'ANAK YATIM', 'bukti_1760754224_9475.jpg', NULL, 0, '2025-10-15 22:18:39', NULL, 270000),
(76, NULL, 'Yanto', '0886757899', 90000.00, '2025-10-15 23:38:58', 'pending', 'no', 'BANTUAN BENCANA', NULL, 'BCA', 0, '2025-10-15 23:38:58', '456749854448', 0),
(77, 'yan', 'yan', NULL, 0.00, '2025-10-15 18:41:47', 'confirmed', 'no', 'BANTUAN BENCANA', 'Screenshot 2025-10-13 132007_1760573768_7418.png', NULL, 0, '2025-10-15 23:41:47', NULL, 900000),
(78, 'yan', 'yan', NULL, 0.00, '2025-10-15 18:41:47', 'confirmed', 'no', 'BANTUAN PENDIDIKAN', '5_1760573518_5614.jpg', NULL, 0, '2025-10-15 23:41:47', NULL, 70000),
(79, 'yan', 'yan', NULL, 0.00, '2025-10-15 18:41:47', 'confirmed', 'no', 'BANTUAN KESEHATAN', '5_1760573157_7765.jpg', NULL, 0, '2025-10-15 23:41:47', NULL, 750000),
(80, 'yan', 'yan', NULL, 0.00, '2025-10-15 18:41:47', 'confirmed', 'no', 'BANTUAN SOSIAL', '5_1760572497_8510.jpg', NULL, 0, '2025-10-15 23:41:47', NULL, 750000),
(81, 'yan', 'yan', NULL, 0.00, '2025-10-15 18:41:47', 'confirmed', 'no', 'ANAK YATIM', '5_1760549352_5642.jpg', NULL, 0, '2025-10-15 23:41:47', NULL, 250000),
(82, 'yan', 'yan', NULL, 0.00, '2025-10-15 18:41:47', 'confirmed', 'no', 'PANTI ASUHAN', 'bukti_1760546903_5584.jpg', NULL, 0, '2025-10-15 23:41:47', NULL, 700000),
(83, 'yan', 'yan', NULL, 0.00, '2025-10-15 18:41:47', 'confirmed', 'no', 'PEMBANGUNAN MASJID', '5_1760546549_6318.jpg', NULL, 0, '2025-10-15 23:41:47', NULL, 1000000),
(84, NULL, 'Yanto', '0886757899', 90000.00, '2025-10-15 23:42:22', 'pending', 'no', 'BANTUAN BENCANA', NULL, 'BCA', 0, '2025-10-15 23:42:22', '456749854448', 0),
(85, NULL, 'Yanto', '0886757899', 9000000.00, '2025-10-15 23:48:15', 'pending', 'no', 'BANTUAN BENCANA', NULL, 'Mandiri', 0, '2025-10-15 23:48:15', '123456789012', 0),
(88, NULL, '', NULL, NULL, '2025-10-16 00:28:16', 'pending', 'no', 'BANTUAN BENCANA', NULL, 'BRI', 0, '2025-10-16 00:28:16', '000000000000', 0),
(89, NULL, '', NULL, NULL, '2025-10-16 00:28:16', 'pending', 'no', 'BANTUAN BENCANA', NULL, 'BNI', 0, '2025-10-16 00:28:16', '333333333333', 0),
(90, NULL, 'Yanto', '0886757899', 900000.00, '2025-10-16 00:29:08', 'pending', 'no', 'PEMBANGUNAN MASJID', NULL, 'BNI', 0, '2025-10-16 00:29:08', '333333333333', 0),
(91, NULL, 'Yanto', '0886757899', 70000.00, '2025-10-16 06:54:49', 'pending', 'no', 'BANTUAN BENCANA', NULL, 'Mandiri', 0, '2025-10-16 06:54:49', '123456789012', 0),
(92, NULL, 'Yanto', '0886757899', 80000.00, '2025-10-16 07:05:48', 'pending', 'no', 'BANTUAN PENDIDIKAN', NULL, 'BNI', 0, '2025-10-16 07:05:48', '333333333333', 0),
(93, NULL, 'Anonim', '0877777776', 90000.00, '2025-10-16 07:11:50', 'pending', 'no', 'BANTUAN KESEHATAN', NULL, 'Mandiri', 1, '2025-10-16 07:11:50', '123456789012', 0),
(94, NULL, 'Yanto', '0886757899', 80000.00, '2025-10-16 07:15:59', 'pending', 'no', 'ANAK YATIM', NULL, 'BCA', 0, '2025-10-16 07:15:59', '456749854448', 0),
(95, NULL, 'Yanto', '0886757899', 10000.00, '2025-10-16 07:30:47', 'pending', 'no', 'BANTUAN KESEHATAN', NULL, 'BNI', 0, '2025-10-16 07:30:47', '333333333333', 0),
(96, NULL, 'Yanto', '0886757899', 70000.00, '2025-10-16 07:47:35', 'pending', 'no', 'BANTUAN BENCANA', NULL, 'BCA', 0, '2025-10-16 07:47:35', '456749854448', 0),
(97, 'yan', 'Yanto', '099999999998', 100000.00, '2025-10-16 08:13:16', 'confirmed', 'no', 'BANTUAN BENCANA', 'nonAnonim_1760577211_3496.png', 'BCA', 0, '2025-10-16 08:13:16', '456749854448', 0),
(98, 'yan', 'Yanto', '07767889', 1000000.00, '2025-10-16 08:14:26', 'confirmed', 'no', 'BANTUAN BENCANA', 'anonim_1760577275_6478.png', 'BRI', 0, '2025-10-16 08:14:26', '000000000000', 0),
(99, 'yan', 'Yanto', '099999999998', 90000.00, '2025-10-16 08:35:06', 'confirmed', 'no', 'BANTUAN BENCANA', 'anonim_1760578521_7731.png', 'BNI', 0, '2025-10-16 08:35:06', '333333333333', 0),
(100, 'yan', 'Yanto', '07767889', 90000.00, '2025-10-16 08:36:51', 'confirmed', 'no', 'BANTUAN BENCANA', '5_1760762823_1990.jpg', 'BCA', 0, '2025-10-16 08:36:51', '456749854448', 0),
(101, 'yan', 'Yanto', '12466467', 900000.00, '2025-10-16 08:39:02', 'confirmed', 'no', 'BANTUAN BENCANA', 'nonAnonim_1760578753_9451.png', 'Mandiri', 0, '2025-10-16 08:39:02', '123456789012', 0),
(102, 'yan', 'Yanto', '12466467', 100000.00, '2025-10-16 08:41:15', 'confirmed', 'no', 'PAPUA', 'anonim_1760578887_4214.png', 'BCA', 0, '2025-10-16 08:41:15', '456749854448', 0),
(103, 'yan', 'Yanto', '099999999998', 100000.00, '2025-10-16 08:45:44', 'confirmed', 'no', '0', 'anonim_1760579154_4770.png', 'BCA', 0, '2025-10-16 08:45:44', '456749854448', 0),
(104, 'yan', 'Yanto', '07767889', 900000.00, '2025-10-16 09:01:04', 'confirmed', 'no', 'ANAK YATIM', 'anonim_1760580076_4498.png', 'BCA', 0, '2025-10-16 09:01:04', '456749854448', 0),
(105, 'yan', 'Anonim', '0886757899', 80000.00, '2025-10-16 09:37:20', 'confirmed', 'no', 'ANAK YATIM', 'Screenshot 2025-10-13 132153_1760582249_8446.png', 'BNI', 1, '2025-10-16 09:37:20', '333333333333', 0),
(106, 'PRU', 'PRUJEN', '0886757899', 70000.00, '2025-10-16 09:59:10', 'confirmed', 'no', 'PANTI ASUHAN', 'Screenshot 2025-10-13 132153_1760583558_9340.png', 'BCA', 0, '2025-10-16 09:59:10', '456749854448', 0),
(107, 'amel', 'Anonim', '0886757899', 50000.00, '2025-10-16 10:11:04', 'confirmed', 'no', 'BANTUAN SOSIAL', 'Screenshot 2025-10-13 132153_1760584277_6101.png', 'BNI', 1, '2025-10-16 10:11:04', '333333333333', 0),
(108, 'amel', 'Ameliya P', '0886757899', 70000.00, '2025-10-16 10:17:13', 'confirmed', 'no', 'BANTUAN BENCANA', 'Screenshot 2025-10-13 132153_1760584641_4406.png', 'BCA', 0, '2025-10-16 10:17:13', '456749854448', 0),
(109, 'ma', 'Mi ayam', '0886757899', 70000.00, '2025-10-16 10:18:53', 'confirmed', 'no', 'BANTUAN KESEHATAN', 'bukti_1760584762_6273.jpg', 'BCA', 0, '2025-10-16 10:18:53', '456749854448', 0),
(110, 'ma', 'Mi ayam', '086859787', 70000.00, '2025-10-16 10:38:38', 'confirmed', 'no', 'BANTUAN BENCANA', '5_1760585927_7036.jpg', 'BCA', 0, '2025-10-16 10:38:38', '456749854448', 0),
(111, NULL, 'Yanto', '086543234567', 30000.00, '2025-10-18 09:14:42', 'pending', 'no', 'BANTUAN BENCANA', NULL, 'BCA', 0, '2025-10-18 09:14:42', '456749854448', 0),
(112, 'yan', 'Yanto', '086543234567', 30000.00, '2025-10-18 09:14:43', 'confirmed', 'no', 'BANTUAN BENCANA', 'bukti_1760753691_1430.jpg', 'BCA', 0, '2025-10-18 09:14:43', '456749854448', 0),
(113, NULL, 'Yanto', '0876543234567', 900000.00, '2025-10-18 09:18:33', 'pending', 'no', 'BANTUAN BENCANA', NULL, 'BCA', 0, '2025-10-18 09:18:33', '456749854448', 0),
(114, 'yan', 'Yanto', '0876543234567', 900000.00, '2025-10-18 09:18:34', 'confirmed', 'no', 'BANTUAN BENCANA', '5_1760753920_9197.jpg', 'BCA', 0, '2025-10-18 09:18:34', '456749854448', 0),
(115, NULL, 'Yanto', '0876434567', 120000.00, '2025-10-18 09:22:16', 'pending', 'no', 'PEMBANGUNAN MASJID', NULL, 'Mandiri', 1, '2025-10-18 09:22:16', '123456789012', 0),
(116, NULL, 'Ameliya P', '0876543234567', 1500000.00, '2025-10-18 09:23:29', 'pending', 'no', 'BANTUAN PENDIDIKAN', NULL, 'BRI', 0, '2025-10-18 09:23:29', '000000000000', 0),
(117, NULL, 'Ameliya P', '0886757899', 80000.00, '2025-10-18 09:30:49', 'pending', 'no', 'ANAK YATIM', NULL, 'BCA', 0, '2025-10-18 09:30:49', '456749854448', 0),
(118, 'amel', 'Ameliya P', '0886757899', 80000.00, '2025-10-18 09:30:53', 'confirmed', 'no', 'ANAK YATIM', 'bukti_1760754660_4421.jpg', 'BCA', 0, '2025-10-18 09:30:53', '456749854448', 0),
(119, NULL, 'Ameliya P', '0874887655', 1500000.00, '2025-10-18 09:34:07', 'pending', 'no', 'BANTUAN SOSIAL', NULL, 'Mandiri', 1, '2025-10-18 09:34:07', '123456789012', 0),
(120, 'amel', 'Anonim', '0874887655', 1500000.00, '2025-10-18 09:34:35', 'pending', 'no', 'BANTUAN SOSIAL', NULL, 'Mandiri', 1, '2025-10-18 09:34:35', '123456789012', 0),
(121, NULL, 'Ameliya P', '087736463737', 750000.00, '2025-10-18 09:35:13', 'pending', 'no', 'BANTUAN KESEHATAN', NULL, 'Mandiri', 1, '2025-10-18 09:35:13', '123456789012', 0),
(122, 'amel', 'Anonim', '087736463737', 750000.00, '2025-10-18 09:35:14', 'confirmed', 'no', 'BANTUAN KESEHATAN', 'bukti_1760754921_8126.jpg', 'Mandiri', 1, '2025-10-18 09:35:14', '123456789012', 0),
(123, 'amel', 'Ameliya P', '0765434567', 120000.00, '2025-10-18 09:39:17', 'confirmed', 'no', 'PANTI ASUHAN', 'bukti_1760755164_7769.jpg', 'BNI', 0, '2025-10-18 09:39:17', '333333333333', 0),
(124, 'amel', 'Ameliya P', '08756788655', 90000.00, '2025-10-18 10:05:14', 'pending', 'no', 'BANTUAN PENDIDIKAN', 'bukti_1760756723_6481.jpg', 'BNI', 0, '2025-10-18 10:05:14', '333333333333', 0),
(125, NULL, '', NULL, NULL, '2025-10-18 10:06:35', 'pending', 'no', '', NULL, 'BRI', 0, '2025-10-18 10:06:35', '000000000000', 0),
(126, 'boom', 'MR BOOM', '08765432456', 8500000.00, '2025-10-18 10:08:26', 'pending', 'no', 'BANTUAN BENCANA', 'bukti_1760756915_2404.jpg', 'BCA', 0, '2025-10-18 10:08:26', '456749854448', 0),
(127, 'boom', 'MR BOOM', '087665544668', 800000.00, '2025-10-18 10:14:22', 'pending', 'no', 'BANTUAN BENCANA', 'bukti_1760757271_2499.jpg', 'Mandiri', 0, '2025-10-18 10:14:22', '123456789012', 0),
(128, 'pr', 'Pur', '0877654321345', 750000.00, '2025-10-18 10:25:20', 'pending', 'no', 'BANTUAN PENDIDIKAN', 'bukti_1760757928_1729.jpg', 'Mandiri', 0, '2025-10-18 10:25:20', '123456789012', 0),
(129, 'pr', 'Anonim', '07654321456', 870000.00, '2025-10-18 10:26:01', 'confirmed', 'no', 'BANTUAN KESEHATAN', 'bukti_1760757970_2246.jpg', 'BCA', 1, '2025-10-18 10:26:01', '456749854448', 0),
(130, 'pr', 'pr', NULL, 0.00, '2025-10-18 05:26:50', 'pending', 'no', '0', NULL, NULL, 0, '2025-10-18 10:26:50', NULL, 0),
(131, 'pr', 'pr', NULL, 0.00, '2025-10-18 05:26:50', 'confirmed', 'no', 'BANTUAN BENCANA', NULL, NULL, 0, '2025-10-18 10:26:50', NULL, 1500000),
(132, 'pr', 'pr', NULL, 0.00, '2025-10-18 05:26:50', 'confirmed', 'no', 'BANTUAN SOSIAL', NULL, NULL, 0, '2025-10-18 10:26:50', NULL, 0),
(133, 'pr', 'pr', NULL, 0.00, '2025-10-18 05:26:50', 'confirmed', 'no', 'ANAK YATIM', NULL, NULL, 0, '2025-10-18 10:26:50', NULL, 0),
(134, 'pr', 'pr', NULL, 0.00, '2025-10-18 05:26:50', 'confirmed', 'no', 'PANTI ASUHAN', NULL, NULL, 0, '2025-10-18 10:26:50', NULL, 0),
(135, 'pr', 'pr', NULL, 0.00, '2025-10-18 05:26:50', 'confirmed', 'yes', 'PEMBANGUNAN MASJID', NULL, NULL, 0, '2025-10-18 10:26:50', NULL, 0),
(136, 'yan', 'Anonim', '087554546756', 980000.00, '2025-10-18 11:25:32', 'confirmed', 'yes', 'BANTUAN PENDIDIKAN', 'bukti_1760761549_7179.jpg', 'BRI', 1, '2025-10-18 11:25:32', '000000000000', 0),
(137, 'abdul', 'Abdul Khomar', '0876543456', 3000000.00, '2025-10-18 12:04:06', 'confirmed', 'yes', 'BANTUAN SOSIAL', 'bukti_1760763854_2394.jpg', 'BCA', 0, '2025-10-18 12:04:06', '456749854448', 0),
(138, 'boom', 'MR BOOM', '0847656778764', 750000.00, '2025-10-18 21:05:54', 'confirmed', 'yes', 'BANTUAN SOSIAL', 'bukti_1760796447_9944.jpg', 'Mandiri', 0, '2025-10-18 21:05:54', '123456789012', 0),
(139, 'abdul', 'Anonim', '0876546758798', 450000.00, '2025-10-18 21:11:49', 'confirmed', 'yes', 'BANTUAN BENCANA', 'cd_l_1_1760797063_2339.jpg', 'BCA', 1, '2025-10-18 21:11:49', '456749854448', 0),
(140, 'laras', 'Laras', '08954355465', 1000000.00, '2025-10-21 13:53:39', 'confirmed', 'yes', 'PANTI ASUHAN', 'bukti_1761029642_2566.jpg', 'BRI', 0, '2025-10-21 13:53:39', '000000000000', 0),
(141, 'laras', 'Anonim', '087654657899', 80000.00, '2025-10-21 13:58:02', 'pending', 'no', 'BANTUAN BENCANA', 'bukti_1761029894_9006.jpg', 'BNI', 1, '2025-10-21 13:58:02', '333333333333', 0),
(142, 'yan', 'Yanto', '088675789909', 70000.00, '2025-10-21 14:13:16', 'pending', 'no', 'BANTUAN BENCANA', 'bukti_1761030807_8567.jpg', 'BNI', 0, '2025-10-21 14:13:16', '333333333333', 0),
(143, 'bibi', 'habibi', '087654323489', 1000000.00, '2025-10-22 11:47:00', 'confirmed', 'yes', 'BANTUAN PENDIDIKAN', 'bukti_1761108435_7422.jpg', 'Mandiri', 0, '2025-10-22 11:47:00', '123456789012', 0);

-- --------------------------------------------------------

--
-- Table structure for table `targets`
--

CREATE TABLE `targets` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `tujuan_donasi` varchar(255) NOT NULL,
  `target_amount` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `targets`
--

INSERT INTO `targets` (`id`, `username`, `tujuan_donasi`, `target_amount`, `created_at`) VALUES
(1, 'abdul', 'BANTUAN BENCANA', 900000, '2025-10-18 12:18:50'),
(2, 'abdul', 'BANTUAN PENDIDIKAN', 1500000, '2025-10-18 12:18:50'),
(3, 'abdul', 'BANTUAN KESEHATAN', 0, '2025-10-18 12:18:50'),
(4, 'abdul', 'BANTUAN SOSIAL', 10000, '2025-10-18 12:18:50'),
(5, 'abdul', 'ANAK YATIM', 0, '2025-10-18 12:18:50'),
(6, 'abdul', 'PANTI ASUHAN', 0, '2025-10-18 12:18:50'),
(7, 'abdul', 'PEMBANGUNAN MASJID', 0, '2025-10-18 12:18:50'),
(8, 'boom', 'BANTUAN BENCANA', 23000000, '2025-10-18 21:07:59'),
(9, 'boom', 'BANTUAN PENDIDIKAN', 0, '2025-10-18 21:07:59'),
(10, 'boom', 'BANTUAN KESEHATAN', 0, '2025-10-18 21:07:59'),
(11, 'boom', 'BANTUAN SOSIAL', 0, '2025-10-18 21:07:59'),
(12, 'boom', 'ANAK YATIM', 0, '2025-10-18 21:07:59'),
(13, 'boom', 'PANTI ASUHAN', 0, '2025-10-18 21:07:59'),
(14, 'boom', 'PEMBANGUNAN MASJID', 0, '2025-10-18 21:07:59'),
(15, 'laras', 'BANTUAN BENCANA', 2000000, '2025-10-21 13:54:49'),
(16, 'laras', 'BANTUAN PENDIDIKAN', 0, '2025-10-21 13:54:49'),
(17, 'laras', 'BANTUAN KESEHATAN', 0, '2025-10-21 13:54:49'),
(18, 'laras', 'BANTUAN SOSIAL', 0, '2025-10-21 13:54:50'),
(19, 'laras', 'ANAK YATIM', 0, '2025-10-21 13:54:50'),
(20, 'laras', 'PANTI ASUHAN', 1000000, '2025-10-21 13:54:50'),
(21, 'laras', 'PEMBANGUNAN MASJID', 0, '2025-10-21 13:54:50'),
(22, 'bibi', 'BANTUAN BENCANA', 0, '2025-10-22 11:48:21'),
(23, 'bibi', 'BANTUAN PENDIDIKAN', 2000000, '2025-10-22 11:48:21'),
(24, 'bibi', 'BANTUAN KESEHATAN', 0, '2025-10-22 11:48:21'),
(25, 'bibi', 'BANTUAN SOSIAL', 0, '2025-10-22 11:48:21'),
(26, 'bibi', 'ANAK YATIM', 0, '2025-10-22 11:48:21'),
(27, 'bibi', 'PANTI ASUHAN', 0, '2025-10-22 11:48:21'),
(28, 'bibi', 'PEMBANGUNAN MASJID', 0, '2025-10-22 11:48:21');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_donation` int(11) DEFAULT NULL,
  `donor_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `nama_bank` varchar(50) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `tujuan_donasi` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('pending','confirmed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_donation`, `donor_name`, `phone`, `amount`, `nama_bank`, `bukti_transfer`, `tujuan_donasi`, `created_at`, `status`) VALUES
(44, 76, 'Yanto', '0886757899', 90000.00, 'BCA', '5_1760546349_3793.jpg', 'BANTUAN BENCANA', '2025-10-15 23:38:58', 'confirmed'),
(45, 84, 'Yanto', '0886757899', 90000.00, 'BCA', '5_1760546549_6318.jpg', 'BANTUAN BENCANA', '2025-10-15 23:42:22', 'confirmed'),
(46, 85, 'Yanto', '0886757899', 9000000.00, 'Mandiri', 'bukti_1760546903_5584.jpg', 'BANTUAN BENCANA', '2025-10-15 23:48:15', 'confirmed'),
(47, 90, 'Yanto', '0886757899', 900000.00, 'BNI', '5_1760549352_5642.jpg', 'PEMBANGUNAN MASJID', '2025-10-16 00:29:08', 'confirmed'),
(48, 91, 'Yanto', '0886757899', 70000.00, 'Mandiri', '5_1760572497_8510.jpg', 'BANTUAN BENCANA', '2025-10-16 06:54:49', 'confirmed'),
(49, 92, 'Yanto', '0886757899', 80000.00, 'BNI', '5_1760573157_7765.jpg', 'BANTUAN PENDIDIKAN', '2025-10-16 07:05:48', 'confirmed'),
(50, 93, 'Anonim', '0877777776', 90000.00, 'Mandiri', '5_1760573518_5614.jpg', 'BANTUAN KESEHATAN', '2025-10-16 07:11:50', 'confirmed'),
(51, 94, 'Yanto', '0886757899', 80000.00, 'BCA', 'Screenshot 2025-10-13 132007_1760573768_7418.png', 'ANAK YATIM', '2025-10-16 07:15:59', 'confirmed'),
(52, 95, 'Yanto', '0886757899', 10000.00, 'BNI', 'Screenshot 2025-10-14 214105_1760574655_1124.png', 'BANTUAN KESEHATAN', '2025-10-16 07:30:47', 'confirmed'),
(53, 96, 'Yanto', '0886757899', 70000.00, 'BCA', 'Screenshot 2025-10-14 213735_1760575665_9749.png', 'BANTUAN BENCANA', '2025-10-16 07:47:35', 'confirmed'),
(54, 97, 'Yanto', '099999999998', 100000.00, 'BCA', 'nonAnonim_1760577211_3496.png', 'BANTUAN BENCANA', '2025-10-16 08:13:16', 'confirmed'),
(55, 98, 'Yanto', '07767889', 1000000.00, 'BRI', 'anonim_1760577275_6478.png', 'BANTUAN BENCANA', '2025-10-16 08:14:26', 'confirmed'),
(56, 99, 'Yanto', '099999999998', 90000.00, 'BNI', 'anonim_1760578521_7731.png', 'BANTUAN BENCANA', '2025-10-16 08:35:06', 'confirmed'),
(57, 100, 'Yanto', '07767889', 90000.00, 'BCA', '5_1760762823_1990.jpg', 'PANTI ASUHAN', '2025-10-16 08:36:51', 'confirmed'),
(58, 101, 'Yanto', '12466467', 900000.00, 'Mandiri', 'nonAnonim_1760578753_9451.png', 'BANTUAN BENCANA', '2025-10-16 08:39:02', 'confirmed'),
(59, 102, 'Yanto', '12466467', 100000.00, 'BCA', 'anonim_1760578887_4214.png', 'PANTI ASUHAN', '2025-10-16 08:41:15', 'confirmed'),
(60, 103, 'Yanto', '099999999998', 100000.00, 'BCA', 'anonim_1760579154_4770.png', 'PANTI ASUHAN', '2025-10-16 08:45:44', 'confirmed'),
(61, 104, 'Yanto', '07767889', 900000.00, 'BCA', 'anonim_1760580076_4498.png', 'ANAK YATIM', '2025-10-16 09:01:04', 'confirmed'),
(62, 105, 'Anonim', '0886757899', 80000.00, 'BNI', 'Screenshot 2025-10-13 132153_1760582249_8446.png', 'ANAK YATIM', '2025-10-16 09:37:20', 'confirmed'),
(63, 106, 'PRUJEN', '0886757899', 70000.00, 'BCA', 'Screenshot 2025-10-13 132153_1760583558_9340.png', 'PANTI ASUHAN', '2025-10-16 09:59:10', 'confirmed'),
(64, 107, 'Anonim', '0886757899', 50000.00, 'BNI', 'Screenshot 2025-10-13 132153_1760584277_6101.png', 'BANTUAN SOSIAL', '2025-10-16 10:11:04', 'confirmed'),
(65, 108, 'Ameliya P', '0886757899', 70000.00, 'BCA', 'Screenshot 2025-10-13 132153_1760584641_4406.png', 'BANTUAN BENCANA', '2025-10-16 10:17:13', 'confirmed'),
(66, 109, 'Mi ayam', '0886757899', 70000.00, 'BCA', 'bukti_1760584762_6273.jpg', 'BANTUAN KESEHATAN', '2025-10-16 10:18:53', 'confirmed'),
(67, 110, 'Mi ayam', '086859787', 70000.00, 'BCA', '5_1760585927_7036.jpg', 'BANTUAN BENCANA', '2025-10-16 10:38:38', 'confirmed'),
(68, 112, 'Yanto', '086543234567', 30000.00, 'BCA', 'bukti_1760753691_1430.jpg', 'BANTUAN BENCANA', '2025-10-18 09:14:43', 'confirmed'),
(69, 114, 'Yanto', '0876543234567', 900000.00, 'BCA', '5_1760753920_9197.jpg', 'BANTUAN BENCANA', '2025-10-18 09:18:34', 'confirmed'),
(70, 118, 'Ameliya P', '0886757899', 80000.00, 'BCA', NULL, 'ANAK YATIM', '2025-10-18 09:30:53', 'pending'),
(71, 120, 'Anonim', '0874887655', 1500000.00, 'Mandiri', NULL, 'BANTUAN SOSIAL', '2025-10-18 09:34:35', 'pending'),
(72, 122, 'Anonim', '087736463737', 750000.00, 'Mandiri', 'bukti_1760754921_8126.jpg', 'BANTUAN KESEHATAN', '2025-10-18 09:35:14', 'confirmed'),
(73, 123, 'Ameliya P', '0765434567', 120000.00, 'BNI', 'bukti_1760755164_7769.jpg', 'PANTI ASUHAN', '2025-10-18 09:39:17', 'confirmed'),
(74, 124, 'Ameliya P', '08756788655', 90000.00, 'BNI', 'bukti_1760756723_6481.jpg', 'BANTUAN PENDIDIKAN', '2025-10-18 10:05:14', 'pending'),
(75, 126, 'MR BOOM', '08765432456', 8500000.00, 'BCA', 'bukti_1760756915_2404.jpg', 'BANTUAN BENCANA', '2025-10-18 10:08:26', 'pending'),
(76, 127, 'MR BOOM', '087665544668', 800000.00, 'Mandiri', 'bukti_1760757271_2499.jpg', 'BANTUAN BENCANA', '2025-10-18 10:14:22', 'pending'),
(77, 128, 'Pur', '0877654321345', 750000.00, 'Mandiri', 'bukti_1760757928_1729.jpg', 'BANTUAN PENDIDIKAN', '2025-10-18 10:25:20', 'pending'),
(78, 129, 'Anonim', '07654321456', 870000.00, 'BCA', 'bukti_1760757970_2246.jpg', 'BANTUAN KESEHATAN', '2025-10-18 10:26:01', 'confirmed'),
(79, 136, 'Anonim', '087554546756', 980000.00, 'BRI', 'bukti_1760761549_7179.jpg', 'BANTUAN PENDIDIKAN', '2025-10-18 11:25:32', 'confirmed'),
(80, 137, 'Abdul Khomar', '0876543456', 3000000.00, 'BCA', 'bukti_1760763854_2394.jpg', 'BANTUAN SOSIAL', '2025-10-18 12:04:06', 'confirmed'),
(81, 138, 'MR BOOM', '0847656778764', 750000.00, 'Mandiri', 'bukti_1760796447_9944.jpg', 'BANTUAN SOSIAL', '2025-10-18 21:05:54', 'confirmed'),
(82, 139, 'Anonim', '0876546758798', 450000.00, 'BCA', 'cd_l_1_1760797063_2339.jpg', 'BANTUAN BENCANA', '2025-10-18 21:11:49', 'confirmed'),
(83, 135, 'pr', '', 0.00, '', '', 'PEMBANGUNAN MASJID', '2025-10-21 12:00:16', 'confirmed'),
(84, 134, 'pr', '', 0.00, '', '', 'PANTI ASUHAN', '2025-10-21 13:09:54', 'confirmed'),
(85, 133, 'pr', '', 0.00, '', '', 'ANAK YATIM', '2025-10-21 13:09:55', 'confirmed'),
(86, 132, 'pr', '', 0.00, '', '', 'BANTUAN SOSIAL', '2025-10-21 13:09:56', 'confirmed'),
(87, 131, 'pr', '', 0.00, '', '', 'BANTUAN BENCANA', '2025-10-21 13:09:57', 'confirmed'),
(88, 140, 'Laras', '08954355465', 1000000.00, 'BRI', 'bukti_1761029642_2566.jpg', 'PANTI ASUHAN', '2025-10-21 13:53:39', 'confirmed'),
(89, 141, 'Anonim', '087654657899', 80000.00, 'BNI', 'bukti_1761029894_9006.jpg', 'BANTUAN BENCANA', '2025-10-21 13:58:02', 'pending'),
(90, 142, 'Yanto', '088675789909', 70000.00, 'BNI', 'bukti_1761030807_8567.jpg', 'BANTUAN BENCANA', '2025-10-21 14:13:16', 'pending'),
(91, 143, 'habibi', '087654323489', 1000000.00, 'Mandiri', 'bukti_1761108435_7422.jpg', 'BANTUAN PENDIDIKAN', '2025-10-22 11:47:00', 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Hamida Noor Kalila', 'yey', '202cb962ac59075b964b07152d234b70', 'user'),
(2, 'Admin ', 'kokwet', '21232f297a57a5a743894a0e4a801fc3', 'user'),
(3, 'Imam', '123', '202cb962ac59075b964b07152d234b70', 'user'),
(4, 'Udin', 'ya', '202cb962ac59075b964b07152d234b70', 'user'),
(5, 'kharisma', 'cireng', '81dc9bdb52d04dc20036dbd8313ed055', 'user'),
(6, 'Anggun Putri Kumalasari', 'aanggun', '202cb962ac59075b964b07152d234b70', 'user'),
(7, 'Rowi', 'risol mayo', '202cb962ac59075b964b07152d234b70', 'user'),
(8, 'Yaya', 'o', 'd95679752134a2d9eb61dbd7b91c4bcc', 'user'),
(9, 'cici', 'tegar', '202cb962ac59075b964b07152d234b70', 'user'),
(10, 'lee jeno', 'jinoow', 'b18ea44550b68d0d012bd9017c4a864a', 'user'),
(11, 'mamamia', 'nana', '202cb962ac59075b964b07152d234b70', 'user'),
(12, 'Wawan', 'w', '202cb962ac59075b964b07152d234b70', 'user'),
(13, 'Habibi', 'bib', '202cb962ac59075b964b07152d234b70', 'user'),
(14, 'Santi', 'sa', '202cb962ac59075b964b07152d234b70', 'user'),
(15, 'Chiwawa', '!', '202cb962ac59075b964b07152d234b70', 'user'),
(16, 'Maki', 'p', '202cb962ac59075b964b07152d234b70', 'user'),
(17, 'Fajar', '1', '202cb962ac59075b964b07152d234b70', 'user'),
(18, 'Markonah', 'hm', '202cb962ac59075b964b07152d234b70', 'user'),
(19, 'Miya', 'miya', '202cb962ac59075b964b07152d234b70', 'user'),
(20, 'Gatau', 'g', '698d51a19d8a121ce581499d7b701668', 'user'),
(21, 'Ima', 'ima', '92bfd5bc53a4bdbd7b8c9f8bc660cc14', 'user'),
(22, 'Ameliya P', 'amel', '202cb962ac59075b964b07152d234b70', 'user'),
(23, 'Wawa', 'n', '202cb962ac59075b964b07152d234b70', 'user'),
(24, 'opa', 'hehe', '529ca8050a00180790cf88b63468826a', 'user'),
(25, 'Mamat', 'mm', '202cb962ac59075b964b07152d234b70', 'user'),
(26, 'Bejo', 'h', '2510c39011c5be704182423e3a695e91', 'user'),
(27, 'khaleed bin walid', 'khaleed', '827ccb0eea8a706c4c34a16891f84e7b', 'user'),
(28, 'Yanto', 'yan', '202cb962ac59075b964b07152d234b70', 'user'),
(29, 'Amel', 'amell', '202cb962ac59075b964b07152d234b70', 'user'),
(30, 'Lily', 'lalapou', '5795b4547fa2d9bd3c3d5963bcd74976', 'user'),
(31, 'Abdul Khomar', 'abdul', '428a78b4fee47253898d7918c0a09160', 'user'),
(32, 'aanggun', 'gun', '202cb962ac59075b964b07152d234b70', 'user'),
(33, 'MR BOOM', 'boom', '25f9e794323b453885f5181f1b624d0b', 'user'),
(34, 'Kharisma oi', 'Oi', '202cb962ac59075b964b07152d234b70', 'user'),
(35, 'Dhika', 'dika', '18af8ed15c3355d051f9496b0b029bc8', 'user'),
(36, 'PRUJEN', 'PRU', '25f9e794323b453885f5181f1b624d0b', 'user'),
(37, 'Mi ayam', 'ma', '25f9e794323b453885f5181f1b624d0b', 'user'),
(38, 'Pur', 'pr', '25f9e794323b453885f5181f1b624d0b', 'user'),
(39, 'Laras', 'laras', '81dc9bdb52d04dc20036dbd8313ed055', 'user'),
(40, 'habibi', 'bibi', '25f9e794323b453885f5181f1b624d0b', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_data`
--
ALTER TABLE `admin_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bank_name` (`bank_name`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `targets`
--
ALTER TABLE `targets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `donasi_id` (`id_donation`);

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
-- AUTO_INCREMENT for table `admin_data`
--
ALTER TABLE `admin_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `targets`
--
ALTER TABLE `targets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_donation`) REFERENCES `donations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
