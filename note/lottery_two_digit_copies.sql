-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2024 at 03:43 AM
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
-- Table structure for table `lottery_two_digit_copies`
--

CREATE TABLE `lottery_two_digit_copies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lottery_id` bigint(20) UNSIGNED NOT NULL,
  `twod_setting_id` bigint(20) UNSIGNED NOT NULL,
  `two_digit_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `bet_digit` varchar(191) NOT NULL,
  `sub_amount` int(11) NOT NULL DEFAULT 0,
  `prize_sent` tinyint(1) NOT NULL DEFAULT 0,
  `match_status` varchar(191) NOT NULL,
  `res_date` date NOT NULL,
  `res_time` time NOT NULL,
  `session` enum('morning','evening') NOT NULL,
  `admin_log` enum('open','closed') NOT NULL DEFAULT 'closed',
  `user_log` enum('open','closed') NOT NULL DEFAULT 'closed',
  `play_date` date NOT NULL DEFAULT '2024-05-09',
  `play_time` time NOT NULL DEFAULT '12:01:00',
  `win_lose` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lottery_two_digit_copies`
--

INSERT INTO `lottery_two_digit_copies` (`id`, `lottery_id`, `twod_setting_id`, `two_digit_id`, `user_id`, `bet_digit`, `sub_amount`, `prize_sent`, `match_status`, `res_date`, `res_time`, `session`, `admin_log`, `user_log`, `play_date`, `play_time`, `win_lose`, `created_at`, `updated_at`) VALUES
(1, 1, 303, 86, 5, '85', 100, 0, 'open', '2024-05-31', '12:01:00', 'morning', 'closed', 'closed', '2024-05-31', '08:12:59', 0, '2024-05-31 01:42:59', '2024-05-31 01:42:59'),
(2, 1, 303, 76, 5, '75', 100, 0, 'open', '2024-05-31', '12:01:00', 'morning', 'closed', 'closed', '2024-05-31', '08:12:59', 0, '2024-05-31 01:42:59', '2024-05-31 01:42:59'),
(3, 1, 303, 73, 5, '72', 100, 0, 'open', '2024-05-31', '12:01:00', 'morning', 'closed', 'closed', '2024-05-31', '08:12:59', 0, '2024-05-31 01:42:59', '2024-05-31 01:42:59'),
(4, 1, 303, 74, 5, '73', 100, 0, 'open', '2024-05-31', '12:01:00', 'morning', 'closed', 'closed', '2024-05-31', '08:12:59', 0, '2024-05-31 01:42:59', '2024-05-31 01:42:59'),
(5, 1, 303, 75, 5, '74', 100, 0, 'open', '2024-05-31', '12:01:00', 'morning', 'closed', 'closed', '2024-05-31', '08:12:59', 0, '2024-05-31 01:42:59', '2024-05-31 01:42:59'),
(6, 1, 303, 80, 5, '79', 100, 0, 'open', '2024-05-31', '12:01:00', 'morning', 'closed', 'closed', '2024-05-31', '08:12:59', 0, '2024-05-31 01:42:59', '2024-05-31 01:42:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lottery_two_digit_copies`
--
ALTER TABLE `lottery_two_digit_copies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lottery_two_digit_copies_twod_setting_id_foreign` (`twod_setting_id`),
  ADD KEY `lottery_two_digit_copies_lottery_id_foreign` (`lottery_id`),
  ADD KEY `lottery_two_digit_copies_two_digit_id_foreign` (`two_digit_id`),
  ADD KEY `lottery_two_digit_copies_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lottery_two_digit_copies`
--
ALTER TABLE `lottery_two_digit_copies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lottery_two_digit_copies`
--
ALTER TABLE `lottery_two_digit_copies`
  ADD CONSTRAINT `lottery_two_digit_copies_lottery_id_foreign` FOREIGN KEY (`lottery_id`) REFERENCES `lotteries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lottery_two_digit_copies_two_digit_id_foreign` FOREIGN KEY (`two_digit_id`) REFERENCES `two_digits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lottery_two_digit_copies_twod_setting_id_foreign` FOREIGN KEY (`twod_setting_id`) REFERENCES `twod_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lottery_two_digit_copies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
