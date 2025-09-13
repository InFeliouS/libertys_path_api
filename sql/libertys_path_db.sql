-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2025 at 02:30 PM
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
-- Table structure for table `first_qpr_attempts`
--

CREATE TABLE `first_qpr_attempts` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `q1_attempts` int(11) NOT NULL DEFAULT 0,
  `q2_attempts` int(11) NOT NULL DEFAULT 0,
  `q3_attempts` int(11) NOT NULL DEFAULT 0,
  `q4_attempts` int(11) NOT NULL DEFAULT 0,
  `q5_attempts` int(11) NOT NULL DEFAULT 0,
  `q6_attempts` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `first_qpr_attempts`
--

INSERT INTO `first_qpr_attempts` (`id`, `student_id`, `q1_attempts`, `q2_attempts`, `q3_attempts`, `q4_attempts`, `q5_attempts`, `q6_attempts`, `updated_at`) VALUES
(1, 11, 2, 0, 0, 0, 3, 0, '2025-05-27 00:32:17');

-- --------------------------------------------------------

--
-- Table structure for table `first_qpr_progress`
--

CREATE TABLE `first_qpr_progress` (
  `student_id` int(11) NOT NULL,
  `status` enum('unvisited','visited','complete') NOT NULL DEFAULT 'unvisited',
  `retries` int(11) NOT NULL DEFAULT 0,
  `q1_attempts` int(11) NOT NULL DEFAULT 0,
  `q2_attempts` int(11) NOT NULL DEFAULT 0,
  `q3_attempts` int(11) NOT NULL DEFAULT 0,
  `q4_attempts` int(11) NOT NULL DEFAULT 0,
  `q5_attempts` int(11) NOT NULL DEFAULT 0,
  `q6_attempts` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fourth_qpr_attempts`
--

CREATE TABLE `fourth_qpr_attempts` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `question1` int(11) NOT NULL DEFAULT 0,
  `question2` int(11) NOT NULL DEFAULT 0,
  `question3` int(11) NOT NULL DEFAULT 0,
  `question4` int(11) NOT NULL DEFAULT 0,
  `question5` int(11) NOT NULL DEFAULT 0,
  `question6` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fourth_qpr_attempts`
--

INSERT INTO `fourth_qpr_attempts` (`id`, `student_id`, `question1`, `question2`, `question3`, `question4`, `question5`, `question6`, `updated_at`) VALUES
(1, 11, 1, 0, 1, 1, 0, 0, '2025-05-27 01:26:19');

-- --------------------------------------------------------

--
-- Table structure for table `guard_questions`
--

CREATE TABLE `guard_questions` (
  `id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `choice1` varchar(255) NOT NULL,
  `choice2` varchar(255) NOT NULL,
  `choice3` varchar(255) NOT NULL,
  `choice4` varchar(255) NOT NULL,
  `correct_index` tinyint(4) NOT NULL CHECK (`correct_index` between 0 and 3),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guard_questions`
--

INSERT INTO `guard_questions` (`id`, `question_text`, `choice1`, `choice2`, `choice3`, `choice4`, `correct_index`, `created_at`) VALUES
(1, 'this is a test question', 'Answer', 'test', 'test', 'test', 0, '2025-09-09 14:19:58'),
(2, 'this is the 2nd test question', 'test', 'test', 'test', 'answer', 3, '2025-09-09 14:20:18'),
(3, 'sertwert', 'qwerwqefqwef', 'qwefqwef', 'qwefqwef', 'qwefqwef', 1, '2025-09-09 15:58:09'),
(4, 'New Question 4', '123', '651', 'ANswer', '163', 2, '2025-09-09 18:47:42');

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard_team_runs`
--

CREATE TABLE `leaderboard_team_runs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `player1_name` varchar(64) NOT NULL,
  `player2_name` varchar(64) NOT NULL,
  `score` int(11) NOT NULL,
  `time_left` int(11) NOT NULL DEFAULT 0,
  `correct` int(11) NOT NULL DEFAULT 0,
  `mistakes` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `perfect` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `section` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `second_qpr_attempts`
--

CREATE TABLE `second_qpr_attempts` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `bench1_attempts` int(11) NOT NULL DEFAULT 0,
  `bench2_attempts` int(11) NOT NULL DEFAULT 0,
  `bench3_attempts` int(11) NOT NULL DEFAULT 0,
  `bench4_attempts` int(11) NOT NULL DEFAULT 0,
  `bench5_attempts` int(11) NOT NULL DEFAULT 0,
  `bench6_attempts` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `second_qpr_attempts`
--

INSERT INTO `second_qpr_attempts` (`id`, `student_id`, `bench1_attempts`, `bench2_attempts`, `bench3_attempts`, `bench4_attempts`, `bench5_attempts`, `bench6_attempts`, `updated_at`) VALUES
(1, 11, 0, 0, 0, 2, 0, 1, '2025-05-27 00:39:00');

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
(2, 'ST. ALBERTU', '2025', '2026', '2025-05-17 13:37:39'),
(7, 'St. Matthew', '2025', '2026', '2025-05-23 10:46:56'),
(8, 'St. John', '2025', '2026', '2025-05-23 10:50:39'),
(9, 'ST.DIEGO', '2027', '2028', '2025-09-08 12:22:13'),
(10, 'ST. ALBERTU (2025–2026)', '2025', '2026', '2025-09-08 12:49:39');

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
(62, 'Aiden', 'Thomas', 'Adams', 8, 'Male', '2025-05-23 10:50:41'),
(63, 'TESASDF', 'TE23D', '23DSAW', 9, 'Female', '2025-09-08 12:29:50'),
(64, 'TEST', 'WE3TEST', 'TESTETS', 9, 'Male', '2025-09-08 12:31:17'),
(65, 'ASD', 'ASD', 'ASD', 2, 'Male', '2025-09-08 12:49:05'),
(66, 'Juan', 'Cruz', 'Dela', 10, 'Male', '2025-09-08 12:49:39'),
(67, 'Maria', NULL, 'Lopez', 10, 'Female', '2025-09-08 12:49:39'),
(68, 'Pedro', 'Santos', 'Reyes', 10, 'Male', '2025-09-08 12:49:39'),
(69, 'Juan', 'Cruz', 'Dela', 10, 'Male', '2025-09-08 12:50:45'),
(70, 'Maria', NULL, 'Lopez', 10, 'Female', '2025-09-08 12:50:45'),
(71, 'Pedro', 'Santos', 'Reyes', 10, 'Male', '2025-09-08 12:50:45'),
(72, 'AWERWASDFASDFASD', 'DSFSAE', 'SWEFE', 2, 'Female', '2025-09-08 12:53:05'),
(73, '23423', 'DF23D23D', 'ASDFAASDF', 8, 'Female', '2025-09-08 12:55:41'),
(74, 'Juan', 'Cruz', 'Dela', 10, 'Male', '2025-09-08 12:56:00'),
(75, 'Maria', NULL, 'Lopez', 10, 'Female', '2025-09-08 12:56:00'),
(76, 'Pedro', 'Santos', 'Reyes', 10, 'Male', '2025-09-08 12:56:00');

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
(62, 62, 'aadams62', '$2y$10$hw/Ubavu7dOLLCWhzIjv4.trsp3UvxAPpC2u4nzNyEBp6VkhrO5bG', '2025-05-23 10:50:41'),
(63, 63, 't23dsaw63', '$2y$10$NVPCSmtdUnQd0meS6zJA8OH4G/3XmQHcG85siwXsPy7vZ55aJ0Rba', '2025-09-08 12:29:50'),
(64, 64, 'ttestets64', '$2y$10$2ch1rtgZQ4Gdk8XOXv/dB.uowYriAGQT7UJ3v/HUQKUWwGFXQdTYy', '2025-09-08 12:31:17'),
(65, 65, 'aasd65', '$2y$10$3Zi.ZOyQICHHV3/iuOczTO7KpaXpymLufqEt0VBa2zqIaGylOLM2O', '2025-09-08 12:49:05'),
(66, 66, 'jdela66', '$2y$10$DU0ULFFU540GZbgacHp/0ue/lvVSDNUA0Q10DZ.H063s.B2UrqRoO', '2025-09-08 12:49:39'),
(67, 67, 'mlopez67', '$2y$10$A3WqN8QvAL/rGUUErA7Y5OHcrRrj.83F5QzKWEEqKYg5.ictJBeeC', '2025-09-08 12:49:39'),
(68, 68, 'preyes68', '$2y$10$sX6gVIj/BaAO7l32zdyBduZUZuJ.qq5Bd0uNnSmHyEjnN4RPKttKy', '2025-09-08 12:49:40'),
(69, 69, 'jdela69', '$2y$10$xQ/hw6KkHTMocKeWtUoHYufpvHP4o2w41BAPjSQQp0PUA3FsVMMRm', '2025-09-08 12:50:45'),
(70, 70, 'mlopez70', '$2y$10$QvdP2t0yQE7ytGPBkTXc.uP6Yxm6jaxLNgFz4SSUjSR5.zE6F9Pgm', '2025-09-08 12:50:45'),
(71, 71, 'preyes71', '$2y$10$pyONafLgyk2zxn0JWjE3cemkHu2zcm8ZQtuORl/Ed5HGZnUPXnC7S', '2025-09-08 12:50:45'),
(72, 72, 'aswefe72', '$2y$10$oOgvtqUw3DQw3cpmgvauh.YgrC.ij7rBwBBqUwEmWHIWO7mg6N4Oi', '2025-09-08 12:53:05'),
(73, 73, '2asdfaasdf73', '$2y$10$Hpf9aMqecWGTpWhn3WVbA..T23Y7xS0Wxfm2iouX9kLg/ASY4MYe.', '2025-09-08 12:55:41'),
(74, 74, 'jdela74', '$2y$10$uIZK6bSXO3a7KJu4pjXz8.94puDZ1oEg.lfN/GhRCl8zya/7m8iKS', '2025-09-08 12:56:00'),
(75, 75, 'mlopez75', '$2y$10$tK25.yyO2//uLJMZHKz.BuXjHDqXrD0hZMfzeSivg5s3V6b0Zi2iy', '2025-09-08 12:56:00'),
(76, 76, 'preyes76', '$2y$10$//Ju1zvF.yDdjW9TIb0PSusEBwFVYUDwCyjeHdJ1bFDVPDd8tVjOa', '2025-09-08 12:56:00');

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
(11, 11, 'complete', 1, 'complete', 0, 'visited', 4, 'unvisited', 0, '2025-05-19 16:42:03'),
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
(62, 62, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-05-23 10:50:41'),
(63, 63, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:29:50'),
(64, 64, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:31:17'),
(65, 65, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:49:05'),
(66, 66, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:49:39'),
(67, 67, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:49:39'),
(68, 68, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:49:40'),
(69, 69, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:50:45'),
(70, 70, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:50:45'),
(71, 71, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:50:45'),
(72, 72, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:53:05'),
(73, 73, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:55:41'),
(74, 74, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:56:00'),
(75, 75, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:56:00'),
(76, 76, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, 'unvisited', 0, '2025-09-08 12:56:00');

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

-- --------------------------------------------------------

--
-- Table structure for table `third_qpr_attempts`
--

CREATE TABLE `third_qpr_attempts` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `question1` int(11) NOT NULL DEFAULT 0,
  `question2` int(11) NOT NULL DEFAULT 0,
  `question3` int(11) NOT NULL DEFAULT 0,
  `question4` int(11) NOT NULL DEFAULT 0,
  `question5` int(11) NOT NULL DEFAULT 0,
  `question6` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `third_qpr_attempts`
--

INSERT INTO `third_qpr_attempts` (`id`, `student_id`, `question1`, `question2`, `question3`, `question4`, `question5`, `question6`, `updated_at`) VALUES
(1, 11, 5, 0, 1, 1, 0, 1, '2025-05-27 01:05:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `first_qpr_attempts`
--
ALTER TABLE `first_qpr_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_first_student` (`student_id`);

--
-- Indexes for table `first_qpr_progress`
--
ALTER TABLE `first_qpr_progress`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `fourth_qpr_attempts`
--
ALTER TABLE `fourth_qpr_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fourth_student` (`student_id`);

--
-- Indexes for table `guard_questions`
--
ALTER TABLE `guard_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leaderboard_team_runs`
--
ALTER TABLE `leaderboard_team_runs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_score_created` (`score`,`created_at`),
  ADD KEY `idx_section_score` (`section`,`score`),
  ADD KEY `idx_p1` (`player1_name`),
  ADD KEY `idx_p2` (`player2_name`);

--
-- Indexes for table `second_qpr_attempts`
--
ALTER TABLE `second_qpr_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_second_student` (`student_id`);

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
-- Indexes for table `third_qpr_attempts`
--
ALTER TABLE `third_qpr_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_third_student` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `first_qpr_attempts`
--
ALTER TABLE `first_qpr_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fourth_qpr_attempts`
--
ALTER TABLE `fourth_qpr_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `guard_questions`
--
ALTER TABLE `guard_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leaderboard_team_runs`
--
ALTER TABLE `leaderboard_team_runs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `second_qpr_attempts`
--
ALTER TABLE `second_qpr_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `student_accounts`
--
ALTER TABLE `student_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `student_progress`
--
ALTER TABLE `student_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `third_qpr_attempts`
--
ALTER TABLE `third_qpr_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `first_qpr_attempts`
--
ALTER TABLE `first_qpr_attempts`
  ADD CONSTRAINT `first_qpr_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `first_qpr_progress`
--
ALTER TABLE `first_qpr_progress`
  ADD CONSTRAINT `fk_first_qpr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fourth_qpr_attempts`
--
ALTER TABLE `fourth_qpr_attempts`
  ADD CONSTRAINT `fourth_qpr_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `second_qpr_attempts`
--
ALTER TABLE `second_qpr_attempts`
  ADD CONSTRAINT `second_qpr_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

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

--
-- Constraints for table `third_qpr_attempts`
--
ALTER TABLE `third_qpr_attempts`
  ADD CONSTRAINT `third_qpr_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
