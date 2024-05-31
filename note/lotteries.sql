-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2024 at 03:44 AM
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
-- Database: `77_sport`
--

-- --------------------------------------------------------

--
-- Table structure for table `lotteries`
--

CREATE TABLE `lotteries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pay_amount` int(11) NOT NULL DEFAULT 0,
  `total_amount` int(11) NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `lottery_match_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `session` enum('morning','evening') NOT NULL,
  `slip_no` varchar(191) NOT NULL DEFAULT '77sport-2d-2024-05-30-session',
  `comission` decimal(8,2) NOT NULL DEFAULT 0.00,
  `commission_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lotteries`
--

INSERT INTO `lotteries` (`id`, `pay_amount`, `total_amount`, `user_id`, `lottery_match_id`, `session`, `slip_no`, `comission`, `commission_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 600, 600, 5, 1, 'morning', '3648-77-sport-2d-2024-05-31-08:12:59', 0.00, 0.00, 'pending', '2024-05-31 01:42:59', '2024-05-31 01:42:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lotteries`
--
ALTER TABLE `lotteries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lotteries_user_id_foreign` (`user_id`),
  ADD KEY `lotteries_lottery_match_id_foreign` (`lottery_match_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lotteries`
--
ALTER TABLE `lotteries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lotteries`
--
ALTER TABLE `lotteries`
  ADD CONSTRAINT `lotteries_lottery_match_id_foreign` FOREIGN KEY (`lottery_match_id`) REFERENCES `lottery_matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lotteries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
