-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2025 at 01:04 PM
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
(5, 'teacher_one Question', '1', '2', '3', 'Answer', 3, 2, '2025-10-12 10:44:02');

-- --------------------------------------------------------

--
-- Table structure for table `guard_questions_backup`
--

CREATE TABLE `guard_questions_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `question_text` text NOT NULL,
  `choice1` varchar(255) NOT NULL,
  `choice2` varchar(255) NOT NULL,
  `choice3` varchar(255) NOT NULL,
  `choice4` varchar(255) NOT NULL,
  `correct_index` tinyint(4) NOT NULL CHECK (`correct_index` between 0 and 3),
  `section_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guard_questions_backup`
--

INSERT INTO `guard_questions_backup` (`id`, `question_text`, `choice1`, `choice2`, `choice3`, `choice4`, `correct_index`, `section_id`, `created_by`, `created_at`) VALUES
(1, 'this is a test question', 'Answer', 'test', 'test', 'test', 0, NULL, NULL, '2025-09-09 14:19:58'),
(2, 'this is the 2nd test question', 'test', 'test', 'test', 'answer', 3, NULL, NULL, '2025-09-09 14:20:18'),
(3, 'sertwert', 'qwerwqefqwef', 'qwefqwef', 'qwefqwef', 'qwefqwef', 1, NULL, NULL, '2025-09-09 15:58:09'),
(4, 'New Question 4', '123', '651', 'ANswer', '163', 2, NULL, NULL, '2025-09-09 18:47:42');

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
(33, 'tester12', 'tester21', 650, 30, 0, 'PERFECT RUN', 'NF TEST', '2025-10-07 15:15:43');

-- --------------------------------------------------------

--
-- Stand-in structure for view `leaderboard_team_runs_v2`
-- (See below for the actual view)
--
CREATE TABLE `leaderboard_team_runs_v2` (
`id` bigint(20) unsigned
,`player1_name` varchar(64)
,`player2_name` varchar(64)
,`score` int(11)
,`time_left` int(11)
,`life_used` tinyint(1)
,`run_status` varchar(32)
,`section` varchar(64)
,`created_at` timestamp
);

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
(10, 'ST. ALBERTU (2025–2026)', '2025', '2026', '2025-09-08 12:49:39'),
(11, 'NF TEST', '2025', '2026', '2025-09-14 13:27:10');

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
(2, 'KIM', '', 'PERALTA', 11, '2025-09-14 15:12:36');

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
(2, 2, 'kperalta', '2025-09-14 15:12:36');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('ADMIN','TEACHER') NOT NULL DEFAULT 'TEACHER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$6pno/1twL7Q6n4qQW0UWL./GyWI6AXK5Pi3Vu3ts61u/jo1aOmHqW', 'ADMIN'),
(2, 'teacher_one', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TEACHER'),
(3, 'teacher_two', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TEACHER');

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
(1, 2, 9),
(2, 3, 7);

-- --------------------------------------------------------

--
-- Structure for view `leaderboard_team_runs_v2`
--
DROP TABLE IF EXISTS `leaderboard_team_runs_v2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `leaderboard_team_runs_v2`  AS SELECT `leaderboard_team_runs`.`id` AS `id`, `leaderboard_team_runs`.`player1_name` AS `player1_name`, `leaderboard_team_runs`.`player2_name` AS `player2_name`, `leaderboard_team_runs`.`score` AS `score`, `leaderboard_team_runs`.`time_left` AS `time_left`, `leaderboard_team_runs`.`life_used` AS `life_used`, coalesce(`leaderboard_team_runs`.`run_status`,case when `leaderboard_team_runs`.`life_used` = 0 then 'PERFECT RUN' else 'ONE LIFE USED' end) AS `run_status`, `leaderboard_team_runs`.`section` AS `section`, `leaderboard_team_runs`.`created_at` AS `created_at` FROM `leaderboard_team_runs` ;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `leaderboard_team_runs`
--
ALTER TABLE `leaderboard_team_runs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_accounts`
--
ALTER TABLE `student_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher_sections`
--
ALTER TABLE `teacher_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Constraints for table `teacher_sections`
--
ALTER TABLE `teacher_sections`
  ADD CONSTRAINT `fk_ts_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ts_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

  ADD CONSTRAINT `uq_teacher_sections_section` UNIQUE (`section_id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
