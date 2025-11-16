-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 15, 2025 at 09:20 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u813043619_libertys_path`
--

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
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guard_questions`
--

INSERT INTO `guard_questions` (`id`, `question_text`, `choice1`, `choice2`, `choice3`, `choice4`, `correct_index`, `created_by`, `created_at`) VALUES
(1, 'this is a test question', 'Answer', 'test', 'test', 'test', 0, NULL, '2025-09-09 14:19:58'),
(2, 'this is the 2nd test question', 'test', 'test', 'test', 'answer', 3, NULL, '2025-09-09 14:20:18'),
(3, 'sertwert', 'qwerwqefqwef', 'qwefqwef', 'qwefqwef', 'qwefqwef', 1, NULL, '2025-09-09 15:58:09'),
(4, 'New Question 4', '123', '651', 'ANswer', '163', 2, NULL, '2025-09-09 18:47:42'),
(5, 'teacher_one Question', '1', '2', '3', 'Answer', 3, NULL, '2025-10-12 10:44:02'),
(6, 'test', '1', '2', '3', 'test', 3, NULL, '2025-10-31 10:14:56'),
(7, '1+1', '1', '2', '3', '4', 1, NULL, '2025-10-31 12:18:05'),
(8, 'National Hero', 'Jose Rizal', 'Andres Bonifacio', 'Juan Luna', 'McArthur', 0, NULL, '2025-10-31 12:19:24'),
(9, '2*2', '1', '2', '3', '4', 3, NULL, '2025-10-31 12:19:41'),
(10, 'Hit and ______', 'Search', 'Collect', 'Run', 'Walk', 2, NULL, '2025-10-31 12:20:32'),
(11, '100-10', '90', '80', '70', '60', 0, NULL, '2025-10-31 12:21:20'),
(12, 'hello', 'Doggy', 'Cat', 'Dog', 'Catty', 0, NULL, '2025-11-08 01:28:57'),
(16, 'What is the capital city of France?', 'Berlin', 'Madrid', 'Paris', 'Rome', 2, 20, '2025-11-10 02:59:51'),
(17, 'How many days are there in one year?', '360', '364', '365', '366', 2, 20, '2025-11-10 03:00:17'),
(18, 'Which planet is known as the “Red Planet”?', 'Venus', 'Mars', 'Jupiter', 'Saturn', 1, 20, '2025-11-10 03:00:45'),
(19, 'Sino ang mayamang pinuno ng Mali Empire?', 'Sundiata Keita', 'Mansa Musa', 'Askia Muhammad', 'Ibn Battuta', 1, 21, '2025-11-11 03:51:55'),
(20, 'Ano ang tawag sa sistemang lupa kapalit ng serbisyo?', 'Feudalismo', 'Monarkiya', 'Imperyalismo', 'Kapitalismo', 0, 21, '2025-11-11 03:52:33'),
(21, 'Sino ang gumawa ng printing press?', 'Johannes Gutenberg', 'Leonardo da Vinci', 'Martin Luther', 'Galileo Galilei', 0, 21, '2025-11-11 04:15:01'),
(22, 'Bakit nagsimula ang Renaissance sa Italya?', 'Pagbagsak ng Constantinople', 'Paglakas ng mga lungsod at mangangalakal', 'Pagkatuklas ng Amerika', 'Pag-aalsa ng mga magsasaka', 1, 21, '2025-11-11 04:15:42'),
(24, 'test question', '1', '2', '3', '4', 0, NULL, '2025-11-12 06:59:45');

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
  `life_used` tinyint(1) NOT NULL DEFAULT 0,
  `run_status` varchar(32) DEFAULT NULL,
  `section` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaderboard_team_runs`
--

INSERT INTO `leaderboard_team_runs` (`id`, `player1_name`, `player2_name`, `score`, `time_left`, `life_used`, `run_status`, `section`, `created_at`) VALUES
(1, 'Player1', 'Player2', 2336, 82, 0, 'PERFECT RUN', 'NF TEST', '2025-09-14 10:50:19'),
(2, 'Player1', 'Player2', 2336, 82, 0, 'PERFECT RUN', 'ST. ALBERTU', '2025-09-14 10:52:46'),
(3, 'Player1', 'Player2', 2336, 82, 0, 'PERFECT RUN', 'ST. ALBERTU', '2025-09-14 15:46:08'),
(4, 'PLAYER1', 'PLAYER2', 200, 0, 1, 'ONE LIFE USED', 'NF TEST', '2025-09-14 15:57:19'),
(5, 'jogarcia', 'kperalta', 865, 74, 0, 'PERFECT RUN', 'NF TEST', '2025-09-17 04:26:18'),
(6, 'PLAYER1', 'PLAYER2', 200, 0, 1, 'ONE LIFE USED', 'NF TEST', '2025-09-19 16:39:41'),
(7, 'PLAYER1', 'PLAYER2', 2004, 390, 0, 'PERFECT RUN', 'NF TEST', '2025-09-20 03:36:34'),
(8, 'PLAYER1', 'PLAYER2', 200, 0, 1, 'ONE LIFE USED', 'NF TEST', '2025-09-20 08:21:47'),
(9, 'PLAYER1', 'PLAYER2', 2104, 418, 0, 'PERFECT RUN', 'NF TEST', '2025-09-20 18:22:09'),
(10, 'PLAYER1', 'PLAYER2', 2103, 418, 0, 'PERFECT RUN', 'NF TEST', '2025-09-20 18:22:17'),
(11, 'PLAYER1', 'PLAYER2', 2103, 418, 0, 'PERFECT RUN', 'NF TEST', '2025-09-20 18:23:01'),
(12, 'PLAYER1', 'PLAYER2', 2103, 418, 0, 'PERFECT RUN', 'NF TEST', '2025-09-20 18:25:19'),
(13, 'PLAYER1', 'PLAYER2', 2104, 418, 0, 'PERFECT RUN', 'NF TEST', '2025-09-20 18:44:55'),
(14, 'PLAYER1', 'PLAYER2', 2104, 418, 0, 'PERFECT RUN', 'NF TEST', '2025-09-21 11:13:30'),
(15, 'PLAYER1', 'PLAYER2', 2476, 321, 0, 'PERFECT RUN', 'NF TEST', '2025-09-21 11:44:33'),
(16, 'PLAYER1', 'PLAYER2', 2103, 418, 0, 'PERFECT RUN', 'NF TEST', '2025-09-25 11:37:45'),
(17, 'Player1', 'Player2', 1000, 0, 0, 'PERFECT RUN', 'A', '2025-09-28 08:46:47'),
(18, 'PLAYER1', 'PLAYER2', 1500, 0, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 14:25:18'),
(19, 'PLAYER1', 'PLAYER2', 3105, 321, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 14:25:18'),
(20, 'tester1', 'tester2', 3105, 321, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 14:25:43'),
(21, 'tester1', 'tester2', 1500, 0, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 14:25:43'),
(22, 'tester1', 'tester2', 1500, 0, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 14:26:08'),
(23, 'tester1', 'tester2', 3105, 321, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 14:26:08'),
(24, 'tester1', 'tester2', 2105, 321, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:12:51'),
(25, 'tester1', 'tester2', 2105, 321, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:12:51'),
(26, 'tester1', 'tester2', 2105, 321, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:13:22'),
(27, 'tester1', 'tester2', 2105, 321, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:13:22'),
(28, 'tester12', 'tester21', 2105, 321, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:13:47'),
(29, 'tester12', 'tester21', 2105, 321, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:13:47'),
(30, 'tester12', 'tester21', 550, 10, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:14:23'),
(31, 'tester12', 'tester21', 550, 10, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:14:24'),
(32, 'tester12', 'tester21', 650, 30, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:15:43'),
(33, 'tester12', 'tester21', 650, 30, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:15:43'),
(34, 'tester12', 'tester21', 2340, 368, 0, 'PERFECT RUN', 'NF TEST', '2025-10-16 09:28:32'),
(35, 'tester12', 'tester21', 650, 30, 0, 'PERFECT RUN', 'NF TEST', '2025-10-16 09:28:32'),
(36, 'tester12', 'tester21', 500, 0, 0, 'PERFECT RUN', 'NF TEST', '2025-10-16 09:29:58'),
(37, 'Player1', 'Player2', 2345, 369, 0, 'PERFECT RUN', 'A', '2025-10-16 10:47:29'),
(38, 'Player1', 'Player2', 935, 87, 0, 'PERFECT RUN', 'A', '2025-10-16 16:23:08'),
(39, 'Player1', 'Player2', 930, 86, 0, 'PERFECT RUN', 'A', '2025-10-16 16:38:22'),
(40, 'jogarcia', 'kperalta', 930, 86, 0, 'PERFECT RUN', 'NF TEST', '2025-10-16 17:01:08'),
(41, 'Player1', 'Player2', 4420, 784, 0, 'PERFECT RUN', 'A', '2025-10-16 17:09:13'),
(42, 'Player1', 'Player2', 4940, 888, 0, 'PERFECT RUN', 'A', '2025-10-17 04:53:26'),
(43, 'Player1', 'Player2', 500, 0, 0, 'PERFECT RUN', 'A', '2025-10-17 04:57:55'),
(44, 'Player1', 'Player2', 4565, 813, 0, 'PERFECT RUN', 'A', '2025-10-19 05:55:22'),
(45, 'Player1', 'Player2', 3670, 634, 0, 'PERFECT RUN', 'A', '2025-10-19 06:38:45'),
(46, 'Player1', 'Player2', 680, 36, 0, 'PERFECT RUN', 'A', '2025-10-21 14:50:02'),
(47, 'Player1', 'Player2', 595, 19, 0, 'PERFECT RUN', 'A', '2025-10-21 14:52:25'),
(48, 'Player1', 'Player2', 625, 25, 0, 'PERFECT RUN', 'A', '2025-10-21 14:53:38'),
(49, 'jogarcia', 'kperalta', 7185, 1337, 0, 'PERFECT RUN', 'NF TEST', '2025-10-23 13:13:22'),
(50, 'jogarcia', 'kperalta', 7850, 1470, 0, 'PERFECT RUN', 'NF TEST', '2025-11-01 01:20:16'),
(51, 'jogarcia', 'kperalta', 500, 0, 0, 'PERFECT RUN', 'NF TEST', '2025-11-10 02:55:56'),
(52, 'jogarcia', 'kperalta', 7555, 1411, 0, 'PERFECT RUN', 'NF TEST', '2025-11-10 03:02:49'),
(53, 'keperalta', 'lpanelo', 5400, 1080, 1, 'ONE LIFE USED', 'ST. JOHN MICHAEL', '2025-11-11 07:56:26'),
(54, 'keperalta', 'lpanelo', 5355, 1071, 1, 'ONE LIFE USED', 'ST. JOHN MICHAEL', '2025-11-11 08:08:04'),
(55, 'jogarcia', 'kperalta', 7045, 1309, 0, 'PERFECT RUN', 'NF TEST', '2025-11-11 09:11:05'),
(56, 'jevangelista', 'keperalta', 6855, 1371, 1, 'ONE LIFE USED', 'ST. JOHN MICHAEL', '2025-11-11 11:31:08'),
(57, 'jchua', 'jdchua', 3330, 566, 0, 'PERFECT RUN', 'ST. FRANCIS', '2025-11-11 13:56:36'),
(58, 'tstest', 'tsurnametest', 6330, 1266, 1, 'ONE LIFE USED', '11-12-25 TEST', '2025-11-12 06:51:02');

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
(11, 'NF TEST', '2025', '2026', '2025-09-14 13:27:10'),
(19, 'ST. FRANCIS', '2025', '2026', '2025-11-08 06:13:25'),
(20, 'ST. IGNATIUS', '2025', '2026', '2025-11-08 06:13:48'),
(21, 'ST. ANTHONY', '2025', '2026', '2025-11-08 06:14:11'),
(22, 'ST. MARIA GORETTI', '2025', '2026', '2025-11-08 06:14:27'),
(24, 'TEST SECTION NI CLARK', '2029', '2030', '2025-11-08 14:02:37'),
(25, 'ST. JOHN MICHAEL', '2029', '2030', '2025-11-10 04:07:43'),
(26, '11-12-25 TEST', '2025', '2026', '2025-11-12 06:15:16');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `given_name`, `middle_name`, `last_name`, `section_id`, `created_at`) VALUES
(1, 'JHON BENEDICT', 'ORBITA', 'GARCIA', 11, '2025-09-14 15:12:11'),
(2, 'KIM', '', 'PERALTA', 11, '2025-09-14 15:12:36'),
(16, 'NEW WEBSITE', 'TESTER', 'TEST', 11, '2025-11-10 02:51:37'),
(17, 'KIM', 'EVANGELISTA', 'PERALTA', 25, '2025-11-11 03:43:40'),
(18, 'JOHN MICHAEL', '', 'EVANGELISTA', 25, '2025-11-11 03:44:29'),
(19, 'LESTER', '', 'PANELO', 25, '2025-11-11 03:47:16'),
(20, 'JC', 'DIMACALE', 'CHUA', 19, '2025-11-11 13:13:32'),
(21, 'JHON BENEDICT', 'ORBITA', 'GARCIA', 19, '2025-11-11 13:15:45'),
(22, 'JC', '', 'CHUA', 19, '2025-11-11 13:35:14'),
(23, 'TEST', '', 'SURNAMETEST', 26, '2025-11-12 06:23:09'),
(24, 'TEST2', '', 'STEST', 26, '2025-11-12 06:31:35'),
(25, 'LEE', 'P', 'MERCADO', 19, '2025-11-12 12:02:30'),
(26, 'SUCCESS', 'POPUP', 'TEST', 11, '2025-11-14 14:49:21');

-- --------------------------------------------------------

--
-- Table structure for table `student_accounts`
--

CREATE TABLE `student_accounts` (
  `account_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_accounts`
--

INSERT INTO `student_accounts` (`account_id`, `student_id`, `username`, `created_at`) VALUES
(1, 1, 'jogarcia', '2025-09-14 15:12:11'),
(2, 2, 'kperalta', '2025-09-14 15:12:36'),
(18, 16, 'nttest', '2025-11-10 02:51:37'),
(19, 17, 'keperalta', '2025-11-11 03:43:40'),
(20, 18, 'jevangelista', '2025-11-11 03:44:29'),
(21, 19, 'lpanelo', '2025-11-11 03:47:16'),
(22, 20, 'jdchua', '2025-11-11 13:13:32'),
(23, 21, 'jogarcia2', '2025-11-11 13:15:45'),
(24, 22, 'jchua', '2025-11-11 13:35:14'),
(25, 23, 'tsurnametest', '2025-11-12 06:23:09'),
(26, 24, 'tstest', '2025-11-12 06:31:35'),
(27, 25, 'lpmercado', '2025-11-12 12:02:30'),
(28, 26, 'sptest', '2025-11-14 14:49:21');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('ADMIN','TEACHER') NOT NULL DEFAULT 'TEACHER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `username`, `first_name`, `last_name`, `password`, `role`) VALUES
(1, 'admin', '', '', '$2y$10$6pno/1twL7Q6n4qQW0UWL./GyWI6AXK5Pi3Vu3ts61u/jo1aOmHqW', 'ADMIN'),
(16, 'raqueld@2025', 'Racquel', 'Dichoso', '$2y$10$AB05Ol.PlmfbZr.uM27XHuzr1EPvgcCZShoEMAMkA8MY8JjdBhwR2', 'TEACHER'),
(20, 'jogarcia', 'Jhon Benedict', 'Garcia', '$2y$10$hlZ6jXfAI0Bb.g/bo5AM0.QKWCWl202g6766yV8TwH3Ma6BWqMOiG', 'TEACHER'),
(21, 'kep@2025', 'Kimberly Mae', 'Peralta', '$2y$10$ESAxHlyXLLNSTbToYFuXO.JKISspOL.tNff7WADt6nRAzq2jWtceu', 'TEACHER');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_configs`
--

CREATE TABLE `teacher_configs` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `room_count` tinyint(4) NOT NULL,
  `enemy_count` tinyint(4) NOT NULL,
  `difficulty` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_configs`
--

INSERT INTO `teacher_configs` (`id`, `teacher_id`, `room_count`, `enemy_count`, `difficulty`, `created_at`, `updated_at`) VALUES
(3, 1, 1, 1, 0, '2025-11-08 01:32:45', '2025-11-08 14:11:29'),
(5, 20, 2, 1, 0, '2025-11-10 02:57:52', '2025-11-10 02:57:52'),
(6, 21, 3, 2, 2, '2025-11-11 03:52:37', '2025-11-12 02:54:49');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_sections`
--

CREATE TABLE `teacher_sections` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_sections`
--

INSERT INTO `teacher_sections` (`id`, `teacher_id`, `section_id`) VALUES
(21, 16, 19),
(22, 16, 20),
(20, 16, 21),
(23, 16, 22),
(27, 16, 24),
(28, 20, 11),
(29, 21, 25);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `guard_questions`
--
ALTER TABLE `guard_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gq_created_by` (`created_by`);

--
-- Indexes for table `leaderboard_team_runs`
--
ALTER TABLE `leaderboard_team_runs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_score_created` (`score`,`created_at`),
  ADD KEY `idx_section_score` (`section`,`score`),
  ADD KEY `idx_p1` (`player1_name`),
  ADD KEY `idx_p2` (`player2_name`),
  ADD KEY `idx_section_life_score` (`section`,`life_used`,`score`);

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
  ADD UNIQUE KEY `uniq_student` (`student_id`),
  ADD UNIQUE KEY `uniq_username` (`username`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `teacher_configs`
--
ALTER TABLE `teacher_configs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tc_teacher` (`teacher_id`);

--
-- Indexes for table `teacher_sections`
--
ALTER TABLE `teacher_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_teacher_section` (`teacher_id`,`section_id`),
  ADD KEY `idx_teacher` (`teacher_id`),
  ADD KEY `idx_section` (`section_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `guard_questions`
--
ALTER TABLE `guard_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `leaderboard_team_runs`
--
ALTER TABLE `leaderboard_team_runs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `student_accounts`
--
ALTER TABLE `student_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `teacher_configs`
--
ALTER TABLE `teacher_configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `teacher_sections`
--
ALTER TABLE `teacher_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `guard_questions`
--
ALTER TABLE `guard_questions`
  ADD CONSTRAINT `fk_gq_creator` FOREIGN KEY (`created_by`) REFERENCES `teachers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Constraints for table `teacher_configs`
--
ALTER TABLE `teacher_configs`
  ADD CONSTRAINT `fk_tc_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_sections`
--
ALTER TABLE `teacher_sections`
  ADD CONSTRAINT `fk_ts_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ts_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
