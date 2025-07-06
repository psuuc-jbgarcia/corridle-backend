-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2025 at 04:54 AM
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
-- Database: `corridle`
--

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(17, 'garciajerico217@gmail.com', '46b5c24a0ef7c7dc2692e217e5472a59ae246b136d4cc66898d272e55f6e6bdb', '2025-06-28 14:33:34', '2025-06-28 11:33:34');

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` int(11) NOT NULL,
  `store_id` varchar(100) NOT NULL,
  `user_uid` varchar(100) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ownership_proof` varchar(255) DEFAULT NULL,
  `postal_code` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_boosted` tinyint(1) DEFAULT 0,
  `business_logo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `store_id`, `user_uid`, `business_name`, `phone_number`, `email`, `category`, `description`, `ownership_proof`, `postal_code`, `created_at`, `is_boosted`, `business_logo`) VALUES
(10, '728685_1750948422911', '728685', '1212', '1111', 'gjeric54321@gmail.com', '121', '1212', 'uploads/685d5a46e410b_RobloxScreenShot20250607_184107429.png', '1212', '2025-06-26 14:33:42', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `userType` enum('Customer','Shop Owner') DEFAULT 'Customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `has_store_info` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `email`, `password`, `userType`, `created_at`, `is_verified`, `verification_token`, `has_store_info`) VALUES
(40, '899390', 'garciajerico217@gmail.com', '$2y$10$VQcR0FIRm8.n9vO1WIDOYeaSVAvm7Gpkl1j9oDFig9SKg00CWdfF2', 'Customer', '2025-06-26 02:21:24', 1, NULL, 1),
(52, '728685', 'gjeric54321@gmail.com', '$2y$10$ycdDT8kFJ66SnFCA6G1MzeCL0JuT.uZ68CcMCLb/fuojDrfYzPopO', 'Shop Owner', '2025-06-26 14:33:14', 1, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `id` int(11) NOT NULL,
  `user_uid` varchar(100) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`id`, `user_uid`, `first_name`, `middle_name`, `last_name`, `phone_number`, `date_of_birth`, `email`, `created_at`) VALUES
(37, '899390', 'Jerico', 'b', 'GARCA', '121212121', '2000-01-11', 'garciajerico217@gmail.com', '2025-06-26 02:21:54'),
(38, '430600', 'asdsd', 'asdas', '1212', '12121', '2000-01-14', 'gjeric54321@gmail.com', '2025-06-26 02:26:01'),
(39, '470335', '12', '12', '1212', '12', '2000-01-18', 'j@gmail.com', '2025-06-26 02:44:10'),
(40, '626563', '11', '1', '11', '11', '2000-01-01', 's@gmail.com', '2025-06-26 13:45:18'),
(41, '822109', '1', '1', '11', '11', '2000-01-19', 'k@gmail.com', '2025-06-26 13:46:54'),
(42, '595958', '111', '11', '111', '111', '2000-01-26', 'k@gmail.com', '2025-06-26 14:08:13'),
(43, '633727', '111', '11', '11', '11', '2000-01-20', 'gjeric54321@gmail.com', '2025-06-26 14:19:40'),
(44, '728685', 'Jerico', 'b', 'garca', '1111', '2000-01-11', 'gjeric54321@gmail.com', '2025-06-26 14:33:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_id` (`store_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userUid` (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
