-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2025 at 06:03 PM
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
-- Database: `users`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announce_id` int(11) NOT NULL,
  `admin_username` varchar(25) NOT NULL,
  `date` varchar(25) NOT NULL,
  `message` varchar(255) NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_code` varchar(50) NOT NULL,
  `lab` varchar(50) NOT NULL,
  `schedule` varchar(100) NOT NULL,
  `instructor` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_code`, `lab`, `schedule`, `instructor`, `created_at`) VALUES
(6, 'Sysarch', '234124', 'Lab 524', '10:30 AM - 1:00 PM', 'Mr. Salimbangon', '2025-04-21 02:59:06'),
(8, 'Trends', '246784', 'Lab 530', '7-8AM', 'Mr. Caminade', '2025-05-04 09:34:00');

-- --------------------------------------------------------

--
-- Table structure for table `direct_sitin`
--

CREATE TABLE `direct_sitin` (
  `id` int(11) NOT NULL,
  `IDNO` varchar(20) NOT NULL,
  `lab_room` varchar(50) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `status` enum('active','completed','','') NOT NULL DEFAULT 'active',
  `purpose` varchar(50) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `pc_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `direct_sitin`
--

INSERT INTO `direct_sitin` (`id`, `IDNO`, `lab_room`, `time_in`, `time_out`, `status`, `purpose`, `date_updated`, `pc_number`) VALUES
(23, '2323', 'Lab 528', '2025-05-07 23:49:48', '2025-05-07 23:53:25', 'completed', 'ASP.Net', '2025-05-07 15:53:25', 18);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `IDNO` int(11) NOT NULL,
  `lab` int(11) NOT NULL,
  `date` varchar(25) NOT NULL,
  `message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pc_status`
--

CREATE TABLE `pc_status` (
  `id` int(11) NOT NULL,
  `lab_room` varchar(50) NOT NULL,
  `pc_number` int(11) NOT NULL,
  `is_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `disabled_reason` text DEFAULT NULL,
  `disabled_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pc_status`
--

INSERT INTO `pc_status` (`id`, `lab_room`, `pc_number`, `is_disabled`, `disabled_reason`, `disabled_at`) VALUES
(1, 'Lab 528', 1, 0, '', '2025-05-06 09:39:36'),
(2, 'Lab 526', 3, 0, '', '2025-05-06 09:40:07'),
(3, 'Lab 526', 2, 0, '', '2025-05-06 09:40:11'),
(4, 'Lab 528', 2, 0, '', '2025-05-06 09:45:38'),
(10, 'Lab 524', 2, 1, 'asda', '2025-05-06 09:50:35'),
(11, 'Lab 524', 3, 0, '', '2025-05-06 09:56:06'),
(12, 'Lab 524', 4, 0, '', '2025-05-06 10:11:19');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `reservation_id` int(11) NOT NULL,
  `reservation_date` varchar(25) NOT NULL,
  `time_in` int(11) NOT NULL,
  `time_out` int(11) NOT NULL,
  `pc` int(11) NOT NULL,
  `lab` varchar(25) NOT NULL,
  `purpose` varchar(100) NOT NULL,
  `IDNO` int(11) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `is_notified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notification_read` tinyint(1) DEFAULT 0,
  `notification_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sit_in_records`
--

CREATE TABLE `sit_in_records` (
  `id` int(11) NOT NULL,
  `IDNO` varchar(20) NOT NULL,
  `lab_room` varchar(50) NOT NULL,
  `pc_number` int(11) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `status` enum('pending','active','completed','declined') NOT NULL DEFAULT 'pending',
  `purpose` varchar(100) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_notified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit_in_records`
--

INSERT INTO `sit_in_records` (`id`, `IDNO`, `lab_room`, `pc_number`, `time_in`, `time_out`, `status`, `purpose`, `date_updated`, `is_notified`, `created_at`) VALUES
(15, '2323', 'Lab 528', 18, '2025-05-07 23:49:48', '2025-05-07 23:53:25', 'completed', 'ASP.Net', '2025-05-07 15:49:48', 0, '2025-05-07 15:49:48');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `StudID` int(11) NOT NULL,
  `IDNO` int(11) NOT NULL,
  `Last_Name` varchar(100) NOT NULL,
  `First_Name` varchar(100) NOT NULL,
  `Mid_Name` varchar(100) NOT NULL,
  `Course` varchar(100) NOT NULL,
  `Year_lvl` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`StudID`, `IDNO`, `Last_Name`, `First_Name`, `Mid_Name`, `Course`, `Year_lvl`, `Username`, `Password`, `profile_image`, `Email`, `Address`) VALUES
(1, 2323, 'asdas', 'dasdas', 'dasdas', 'College of Engineering', 1, 'asdasd', '$2y$10$E3lrN9Gylvi2a/wdGIuvSepFpR3Yjwx71kZ8q1oeTYVGPuFsBuKMi', 'uploads/67d94937c06be.png', 'asdas@gmail.com', 'asdasd'),
(12, 55555, 'Eight', 'Ninzo', 'Dumandan', 'BSIT', 3, 'zxczxc', '$2y$10$xDPjRvdRE6eHNUlQ9NCtIesT1Z7XEQ3gsvWqYSnbuzJPLRZez85bW', 'uploads/680f75f6c2e2d.png', 'egiht@mgail.com', 'asdreghieht'),
(13, 44444, 'Foure', 'San', 'Men', 'BSHM', 2, 'qweqwe', '$2y$10$FC7qA9Doqso8CqIoV529luKUNcL23Wh0szRx481ml9iRnl6DRJqpK', 'uploads/680f75d175235.jpg', 'asdad@gmail.com', 'asdasdas');

-- --------------------------------------------------------

--
-- Table structure for table `student_points`
--

CREATE TABLE `student_points` (
  `id` int(11) NOT NULL,
  `IDNO` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_points`
--

INSERT INTO `student_points` (`id`, `IDNO`, `points`, `last_updated`) VALUES
(1, 1111, 13, '2025-04-24 14:07:38'),
(2, 2323, 18, '2025-05-05 03:13:27'),
(3, 22683361, 5, '2025-04-24 13:34:06'),
(4, 888, 19, '2025-04-28 12:24:56'),
(5, 2222, 16, '2025-04-24 14:07:44'),
(6, 55555, 19, '2025-05-05 14:44:08'),
(7, 44444, 5, '2025-05-05 03:17:22');

-- --------------------------------------------------------

--
-- Table structure for table `student_session`
--

CREATE TABLE `student_session` (
  `id_number` int(11) NOT NULL,
  `remaining_sessions` int(11) NOT NULL DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_session`
--

INSERT INTO `student_session` (`id_number`, `remaining_sessions`) VALUES
(2323, 18),
(1111, 23),
(2222, 30),
(55555, 30),
(44444, 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `direct_sitin`
--
ALTER TABLE `direct_sitin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pc_status`
--
ALTER TABLE `pc_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_pc` (`lab_room`,`pc_number`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`StudID`);

--
-- Indexes for table `student_points`
--
ALTER TABLE `student_points`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `direct_sitin`
--
ALTER TABLE `direct_sitin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `pc_status`
--
ALTER TABLE `pc_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `StudID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `student_points`
--
ALTER TABLE `student_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
