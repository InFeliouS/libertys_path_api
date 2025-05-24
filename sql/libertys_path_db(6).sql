-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2025 at 05:39 AM
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
-- Database: `libertys_path_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `start_school_year` year(4) NOT NULL,
  `end_school_year` year(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_name`, `start_school_year`, `end_school_year`, `created_at`) VALUES
(2, 'ST. ALBERT', '2025', '2026', '2025-05-17 13:37:39'),
(5, 'ST.PAUL', '2025', '2026', '2025-05-20 19:46:36'),
(7, 'St. Matthew', '2025', '2026', '2025-05-23 10:46:56'),
(8, 'St. John', '2025', '2026', '2025-05-23 10:50:39');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `given_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `section_id` int(11) NOT NULL,
  `birth_sex` enum('Male','Female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `given_name`, `middle_name`, `last_name`, `section_id`, `birth_sex`, `created_at`) VALUES
(9, 'TEEEST', 'TE', 'BING', 2, 'Male', '2025-05-19 13:44:04'),
(11, 'JHON BENEDICT', 'ORBITA', 'GARCIA', 2, 'Male', '2025-05-19 16:42:03'),
(12, 'KIM', NULL, 'PERALTA', 2, 'Female', '2025-05-20 06:40:34'),
(23, 'John', 'Michael', 'Doe', 7, 'Male', '2025-05-23 10:46:56'),
(24, 'Jane', 'Elizabeth', 'Smith', 7, 'Female', '2025-05-23 10:46:56'),
(25, 'Sam', 'Robert', 'Wilson', 7, 'Male', '2025-05-23 10:46:56'),
(26, 'Emily', 'Grace', 'Brown', 7, 'Female', '2025-05-23 10:46:56'),
(27, 'William', 'Henry', 'Johnson', 7, 'Male', '2025-05-23 10:46:56'),
(28, 'Olivia', 'Rose', 'Taylor', 7, 'Female', '2025-05-23 10:46:56'),
(29, 'James', 'Paul', 'Clark', 7, 'Male', '2025-05-23 10:46:56'),
(30, 'Sophia', 'Marie', 'Davis', 7, 'Female', '2025-05-23 10:46:56'),
(31, 'Matthew', 'James', 'Miller', 7, 'Male', '2025-05-23 10:46:56'),
(32, 'Chloe', 'Isabelle', 'Anderson', 7, 'Female', '2025-05-23 10:46:56'),
(33, 'John', 'Michael', 'Doe', 8, 'Male', '2025-05-23 10:50:39'),
(34, 'Jane', 'Elizabeth', 'Smith', 8, 'Female', '2025-05-23 10:50:39'),
(35, 'Sam', 'Robert', 'Wilson', 8, 'Male', '2025-05-23 10:50:39'),
(36, 'Emily', 'Grace', 'Brown', 8, 'Female', '2025-05-23 10:50:39'),
(37, 'William', 'Henry', 'Johnson', 8, 'Male', '2025-05-23 10:50:39'),
(38, 'Olivia', 'Rose', 'Taylor', 8, 'Female', '2025-05-23 10:50:39'),
(39, 'James', 'Paul', 'Clark', 8, 'Male', '2025-05-23 10:50:39'),
(40, 'Sophia', 'Marie', 'Davis', 8, 'Female', '2025-05-23 10:50:39'),
(41, 'Matthew', 'James', 'Miller', 8, 'Male', '2025-05-23 10:50:39'),
(42, 'Chloe', 'Isabelle', 'Anderson', 8, 'Female', '2025-05-23 10:50:39'),
(43, 'Noah', 'James', 'Carter', 8, 'Male', '2025-05-23 10:50:39'),
(44, 'Ava', 'Louise', 'Bennett', 8, 'Female', '2025-05-23 10:50:39'),
(45, 'Liam', 'Alexander', 'Harris', 8, 'Male', '2025-05-23 10:50:40'),
(46, 'Isabella', 'Claire', 'Murphy', 8, 'Female', '2025-05-23 10:50:40'),
(47, 'Mason', 'Oliver', 'Garcia', 8, 'Male', '2025-05-23 10:50:40'),
(48, 'Mia', 'Sophia', 'Martinez', 8, 'Female', '2025-05-23 10:50:40'),
(49, 'Ethan', 'Michael', 'Robinson', 8, 'Male', '2025-05-23 10:50:40'),
(50, 'Charlotte', 'Anne', 'Clark', 8, 'Female', '2025-05-23 10:50:40'),
(51, 'Harper', 'Elizabeth', 'Rodriguez', 8, 'Female', '2025-05-23 10:50:40'),
(52, 'Benjamin', 'Joseph', 'Lewis', 8, 'Male', '2025-05-23 10:50:40'),
(53, 'Ella', 'Grace', 'Lee', 8, 'Female', '2025-05-23 10:50:40'),
(54, 'Jacob', 'David', 'Walker', 8, 'Male', '2025-05-23 10:50:40'),
(55, 'Amelia', 'Rose', 'Hall', 8, 'Female', '2025-05-23 10:50:40'),
(56, 'Logan', 'Matthew', 'Allen', 8, 'Male', '2025-05-23 10:50:40'),
(57, 'Evelyn', 'Jane', 'Young', 8, 'Female', '2025-05-23 10:50:40'),
(58, 'Lucas', 'William', 'Hernandez', 8, 'Male', '2025-05-23 10:50:41'),
(59, 'Abigail', 'Marie', 'King', 8, 'Female', '2025-05-23 10:50:41'),
(60, 'Jackson', 'Ryan', 'Wright', 8, 'Male', '2025-05-23 10:50:41'),
(61, 'Lily', 'Isabelle', 'Scott', 8, 'Female', '2025-05-23 10:50:41'),
(62, 'Aiden', 'Thomas', 'Adams', 8, 'Male', '2025-05-23 10:50:41');

-- --------------------------------------------------------

--
-- Table structure for table `student_accounts`
--

CREATE TABLE `student_accounts` (
  `account_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_accounts`
--

INSERT INTO `student_accounts` (`account_id`, `student_id`, `username`, `password`, `created_at`) VALUES
(9, 9, 'tteeest9', '$2y$10$PQf7EeLMv6RsAN0wh7r8cu4Hy99va0/37R1htexPbsURqnylVUqu.', '2025-05-19 13:44:04'),
(11, 11, 'jgarcia11', '$2y$10$7V5aHAdj2dN2szvF3xPGwe5OHYHV7K/Thgcfrx7YVRePklVPxwqIW', '2025-05-19 16:42:03'),
(12, 12, 'kperalta12', '$2y$10$bwyNNWO1IVAHFBGNIWB6IOdW0T./Y8ilUPcf7hrU2twARokh46clm', '2025-05-20 06:40:34'),
(23, 23, 'jdoe23', '$2y$10$5z.pKpawtqmj5PrRFLgL0.4/9hevqhuiqLcYIS0dbGvEhsJbTbQ9O', '2025-05-23 10:46:56'),
(24, 24, 'jsmith24', '$2y$10$zKFJkANroc.69MnlKzyVf.EurEqLRISdDtLnevT/JxYMlAXi1Il0W', '2025-05-23 10:46:56'),
(25, 25, 'swilson25', '$2y$10$uGfdQM7iJJcBKJ0Jo9ss8ufX86DBcyRaPHOL5VNrPvY1bWFpvbbla', '2025-05-23 10:46:56'),
(26, 26, 'ebrown26', '$2y$10$Gd2mPsuPeIBqec9VsH/O4uwWk7X3tk6pzCu9NmgkfZNBR.6YhOnj2', '2025-05-23 10:46:56'),
(27, 27, 'wjohnson27', '$2y$10$aZsyw2hw/iDk/8faj6igt.4Cs6RHLRMhEIyOQedIGo46zbwrX6HNG', '2025-05-23 10:46:56'),
(28, 28, 'otaylor28', '$2y$10$LzPU7bjZLw7Q2aMXubNebeWu7pgda24HzzRHgrfpcIZ.DrF83xJjG', '2025-05-23 10:46:56'),
(29, 29, 'jclark29', '$2y$10$5TzbUgKoFUJzHNfT601tXeRVxBrFTrJwn35GNMu53TQ6e53yGAMqO', '2025-05-23 10:46:56'),
(30, 30, 'sdavis30', '$2y$10$nW3TIZjtZRrTXW5kW6A7Den5Xe8GWDLBCh94kjIgSxDReIDfVq0xy', '2025-05-23 10:46:56'),
(31, 31, 'mmiller31', '$2y$10$1Kl9800wmR/pHA0lp.2yhuicXP.oxA2mRp6lKtKAjNJ4igW1TAQIi', '2025-05-23 10:46:56'),
(32, 32, 'canderson32', '$2y$10$bake4slDury4lb.XM6ACYeUUEK0OUo8tNoGPcnWe7DHfpzzkd8EzO', '2025-05-23 10:46:56'),
(33, 33, 'jdoe33', '$2y$10$J27hnxlJAVBp6KNviy5tTur5xKCkNiCOAkITxy.Auu51UWm6BzJKK', '2025-05-23 10:50:39'),
(34, 34, 'jsmith34', '$2y$10$rhLFOGRX5UXDaCzl1z5UD.2txjBNlDnA8N7fJ4k4ptzUVGuhW1N.O', '2025-05-23 10:50:39'),
(35, 35, 'swilson35', '$2y$10$0CU/R8p4n9w8L8tUGkoPR.Fkt51XTj/hSpNoB1Ez3aQswKDfiM3EC', '2025-05-23 10:50:39'),
(36, 36, 'ebrown36', '$2y$10$9kJguT/W2WNaD99EHlv4VOvFJxhR.sDUXaFkISUgIO0.EhRXdHt/W', '2025-05-23 10:50:39'),
(37, 37, 'wjohnson37', '$2y$10$CnfCCUij76Sfs.MWhq2ip.U1VqzcIVmrcsxL.JRXiBgelgZDfnpeS', '2025-05-23 10:50:39'),
(38, 38, 'otaylor38', '$2y$10$NCZFL5n3ZTrjECh6W05db.Lh.or8a7ABno7TPiJg8MpAa8HGCEK6u', '2025-05-23 10:50:39'),
(39, 39, 'jclark39', '$2y$10$z66GJmCCZhMqe8lALy.lre/gbGYJLoJP846gl6TBAcInrwDo4Gmx2', '2025-05-23 10:50:39'),
(40, 40, 'sdavis40', '$2y$10$HS9jzQL6Xb1LdF6hSnztLuDGIgy5hpO2Drw3Y1wZH3MMK1fsVJ5CG', '2025-05-23 10:50:39'),
(41, 41, 'mmiller41', '$2y$10$N1ofZSuYzp1YtVWtnflEbeXcOjIjWGgUKbKj9xBpL/ykh/MS4Ar4S', '2025-05-23 10:50:39'),
(42, 42, 'canderson42', '$2y$10$eECtQ/UpoICXLt72IDxgpuuVn4BPXl1SDRItAgsl2I9.KtJQ/xEGS', '2025-05-23 10:50:39'),
(43, 43, 'ncarter43', '$2y$10$BEy6PneGlw3PQBck02RYV.ZatmwymkhFnoveBJftsu4YHtN6MKVy2', '2025-05-23 10:50:39'),
(44, 44, 'abennett44', '$2y$10$aKozPvBRPthAhlPxQNmf.eg0KfCS6KmGbtOT4EynLBLg9uIw17U3e', '2025-05-23 10:50:39'),
(45, 45, 'lharris45', '$2y$10$DWiFjR7xzIjie.9W/fy1r.ir377kV2vEoPHnGdzZ.fhRN.SZnhPZ6', '2025-05-23 10:50:40'),
(46, 46, 'imurphy46', '$2y$10$A8hRlCfJzcVExMyALYNbZOV.Bcrb15zlcY6v6.OeZJDHBJBXRxqkG', '2025-05-23 10:50:40'),
(47, 47, 'mgarcia47', '$2y$10$b3jMz/gZ6ox7zIzU592dk.P2inqKgKvll6IiOfb8irOzP4m5EL8X2', '2025-05-23 10:50:40'),
(48, 48, 'mmartinez48', '$2y$10$gSNOTCMxbQN036OmAydBxOPGMvEJ6dPH6VF3W2T8ffuobcGrF7riu', '2025-05-23 10:50:40'),
(49, 49, 'erobinson49', '$2y$10$i9mIk7ya7uJ9OuFbbeYoh.yKo9WXUCcUyoGM0rNGVUhMkSpKLurcu', '2025-05-23 10:50:40'),
(50, 50, 'cclark50', '$2y$10$kNiameM92l9J.dJ6GOwCdeqN5row8RFpjri3QYOJjyYwAlysDjAoW', '2025-05-23 10:50:40'),
(51, 51, 'hrodriguez51', '$2y$10$UgbjiTqDGOL7MC99LWFhiOw4FVbzGDQqPAOrF2SYk/lTn9XnWv3re', '2025-05-23 10:50:40'),
(52, 52, 'blewis52', '$2y$10$2swgGadFzQ4MZ/gpFCpzkeZH7Sbvv.Z94DcU6UZ9Ub1w1QAeCCdhS', '2025-05-23 10:50:40'),
(53, 53, 'elee53', '$2y$10$ySa2qVqAUOVr3pdTC0v9K.DIQR2uw.MSiVIqy1fQfuBvYjZIfWcHq', '2025-05-23 10:50:40'),
(54, 54, 'jwalker54', '$2y$10$iG28w0ua.cgFj3xIui0sE.5WTD8S3QfVEdVa5bu39YWo5fdzKuzke', '2025-05-23 10:50:40'),
(55, 55, 'ahall55', '$2y$10$tZdmG4OzMREVU1TKKt6xmuN0bMmRkrtgbVzsD9y9hst/vYFKyTREC', '2025-05-23 10:50:40'),
(56, 56, 'lallen56', '$2y$10$W3FI4sQ0EBTKRx2XhA5tYu7UmqqjXqsDghtDNZoFc20tVBNIKdq1G', '2025-05-23 10:50:40'),
(57, 57, 'eyoung57', '$2y$10$wwPukUcfEhvMArKemeAV5.7fkvru6mBgp3IC/Qhux/aGRqXsauv7u', '2025-05-23 10:50:41'),
(58, 58, 'lhernandez58', '$2y$10$6jph7iTpZVuTLOhMdehKXeU8mAFnegrD03nthRgVu6BMI0rnsd/QS', '2025-05-23 10:50:41'),
(59, 59, 'aking59', '$2y$10$JzgQxCsOIXXkx8zOPMxCpepoNrwBwoLLWrtLMfrjdb58tdv9RQZr2', '2025-05-23 10:50:41'),
(60, 60, 'jwright60', '$2y$10$xzCuCS3J8nT7qx9nLXq/seDUcQNMinT0WnovRqJnAUnm57RR4zpn6', '2025-05-23 10:50:41'),
(61, 61, 'lscott61', '$2y$10$KjhxF2cPIy0SyR9tlXTbY.88GFbCdx2plDdwBz0EAwd0AGIGWK/ie', '2025-05-23 10:50:41'),
(62, 62, 'aadams62', '$2y$10$hw/Ubavu7dOLLCWhzIjv4.trsp3UvxAPpC2u4nzNyEBp6VkhrO5bG', '2025-05-23 10:50:41');

-- --------------------------------------------------------

--
-- Table structure for table `student_progress`
--

CREATE TABLE `student_progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `first_qpr_status` enum('unvisited','visited','complete') NOT NULL DEFAULT 'unvisited',
  `first_qpr_retries` int(11) NOT NULL DEFAULT 0,
  `second_qpr_status` enum('unvisited','visited','complete') NOT NULL DEFAULT 'unvisited',
  `second_qpr_retries` int(11) NOT NULL DEFAULT 0,
  `third_qpr_status` enum('unvisited','visited','complete') NOT NULL DEFAULT 'unvisited',
  `third_qpr_retries` int(11) NOT NULL DEFAULT 0,
  `fourth_qpr_status` enum('unvisited','visited','complete') NOT NULL DEFAULT 'unvisited',
  `fourth_qpr_retries` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_progress`
--

INSERT INTO `student_progress` (`id`, `student_id`, `first_qpr_status`, `first_qpr_retries`, `second_qpr_status`, `second_qpr_retries`, `third_qpr_status`, `third_qpr_retries`, `fourth_qpr_status`, `fourth_qpr_retries`, `created_at`) VALUES
(9, 9, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-19 13:44:04'),
(11, 11, 'complete', 1, 'complete', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-19 16:42:03'),
(12, 12, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-20 06:40:34'),
(23, 23, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:46:56'),
(24, 24, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:46:56'),
(25, 25, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:46:56'),
(26, 26, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:46:56'),
(27, 27, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:46:56'),
(28, 28, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:46:56'),
(29, 29, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:46:56'),
(30, 30, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:46:56'),
(31, 31, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:46:56'),
(32, 32, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:46:56'),
(33, 33, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(34, 34, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(35, 35, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(36, 36, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(37, 37, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(38, 38, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(39, 39, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(40, 40, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(41, 41, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(42, 42, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(43, 43, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(44, 44, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:39'),
(45, 45, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(46, 46, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(47, 47, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(48, 48, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(49, 49, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(50, 50, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(51, 51, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(52, 52, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(53, 53, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(54, 54, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(55, 55, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(56, 56, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:40'),
(57, 57, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:41'),
(58, 58, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:41'),
(59, 59, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:41'),
(60, 60, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:41'),
(61, 61, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:41'),
(62, 62, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:41');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$6pno/1twL7Q6n4qQW0UWL./GyWI6AXK5Pi3Vu3ts61u/jo1aOmHqW');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `student_accounts`
--
ALTER TABLE `student_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_student` (`student_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `student_accounts`
--
ALTER TABLE `student_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `student_progress`
--
ALTER TABLE `student_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `student_accounts`
--
ALTER TABLE `student_accounts`
  ADD CONSTRAINT `student_accounts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD CONSTRAINT `fk_progress_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
