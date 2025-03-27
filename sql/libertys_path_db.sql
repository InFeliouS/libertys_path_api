-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 03:35 AM
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
-- Table structure for table `dialogues`
--

CREATE TABLE `dialogues` (
  `id` int(11) NOT NULL,
  `scene` varchar(255) NOT NULL,
  `level` varchar(255) NOT NULL,
  `character_name` varchar(255) NOT NULL,
  `dialogue` text NOT NULL,
  `dialogue_condition` varchar(255) DEFAULT NULL,
  `task_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dialogues`
--

INSERT INTO `dialogues` (`id`, `scene`, `level`, `character_name`, `dialogue`, `dialogue_condition`, `task_name`) VALUES
(1, 'Room', 'Tutorial', 'Guide', 'Welcome to Liberty\'s Path! I\'ll be your guide through this tutorial.', 'tutorial_start', 'move_player'),
(2, 'Room', 'Tutorial', 'Guide', 'First, let\'s learn how to move around in this world.', 'tutorial_start', NULL),
(3, 'Room', 'Tutorial', 'Guide', 'Great job moving around! You can use the WASD keys to move in any direction.', 'tutorial_move_complete', 'move_camera'),
(4, 'Room', 'Tutorial', 'Guide', 'Now, let\'s learn how to look around.', 'tutorial_move_complete', NULL),
(5, 'Room', 'Tutorial', 'Guide', 'Perfect! You can use your mouse to look in any direction.', 'tutorial_camera_complete', 'interact_folder'),
(6, 'Room', 'Tutorial', 'Guide', 'Now, try interacting with objects. See that folder on the desk? Click on it to examine it.', 'tutorial_camera_complete', NULL),
(7, 'Room', 'Tutorial', 'Guide', 'Well done! You can interact with many objects in this world by clicking on them.', 'tutorial_interact_complete', 'view_controls'),
(8, 'Room', 'Tutorial', 'Guide', 'Finally, you can press C at any time to view the full list of controls.', 'tutorial_interact_complete', NULL),
(9, 'Room', 'Tutorial', 'Guide', 'Excellent! Remember you can access this menu anytime you need a reminder.', 'tutorial_controls_complete', 'tutorial_complete'),
(10, 'Room', 'Tutorial', 'Guide', 'Congratulations! You\'ve completed the basic tutorial.', 'tutorial_finished', NULL),
(11, 'Room', 'Tutorial', 'Guide', 'In Liberty\'s Path, you\'ll explore historical locations, solve puzzles, and uncover the story of Philippine independence.', 'tutorial_finished', NULL),
(12, 'Room', 'Tutorial', 'Guide', 'Your journey begins in the year 1896, as tensions rise against Spanish colonial rule.', 'tutorial_finished', NULL),
(13, 'Room', 'Tutorial', 'Guide', 'Remember what you\'ve learned here as you navigate through history.', 'tutorial_finished', NULL),
(14, 'Room', 'Tutorial', 'Guide', 'Good luck on your journey! The door to the next room is now unlocked.', 'tutorial_finished', 'next_level');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `given_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `birth_sex` enum('Male','Female') NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `given_name`, `middle_name`, `last_name`, `section_name`, `birth_sex`, `username`, `password`, `created_at`) VALUES
(1, 'Jhon Benedict', '', 'Garcia', 'St. Paul', 'Male', 'garciaJB', '$2y$10$ai/a/cIKFghTu.75GiG3auGz1PjoDRm8ZOwlNoh7fU5v8Drnw98te', '2025-03-14 10:38:55'),
(2, 'Kim', '', 'Peralta', 'St.Albert', 'Female', 'peraltaKIM', '$2y$10$FoV1okNJsBkM32l2pg95/O6/VhIIyD1auatVYHZvMgnAZ4Kphi1Ui', '2025-03-14 11:33:00');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `scene` varchar(255) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `task_content` text NOT NULL,
  `dialogue_condition` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `scene`, `task_name`, `task_content`, `dialogue_condition`) VALUES
(1, 'Room', 'move_player', 'Use WASD keys to move your character.', 'tutorial_move'),
(2, 'Room', 'move_camera', 'Move your mouse to look around.', 'tutorial_camera'),
(3, 'Room', 'interact_folder', 'Click on the folder to examine it.', 'tutorial_interact'),
(4, 'Room', 'view_controls', 'Press C to view the full controls.', 'tutorial_controls'),
(5, 'Room', 'tutorial_complete', 'Tutorial complete! Proceed to the next room.', 'tutorial_finished');

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
-- Indexes for table `dialogues`
--
ALTER TABLE `dialogues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `dialogues`
--
ALTER TABLE `dialogues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
