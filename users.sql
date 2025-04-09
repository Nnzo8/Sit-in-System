-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2025 at 05:53 AM
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
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `direct_sitin`
--

INSERT INTO `direct_sitin` (`id`, `IDNO`, `lab_room`, `time_in`, `time_out`, `status`, `purpose`, `date_updated`) VALUES
(6, '2323', 'Lab 530', '2025-03-20 08:00:03', '2025-03-20 08:00:16', 'completed', 'Python', '2025-03-20 00:00:03'),
(7, '2323', 'Lab 530', '2025-03-31 13:27:10', '2025-03-31 13:27:31', 'completed', 'Java', '2025-03-31 05:27:10');

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

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `IDNO`, `lab`, `date`, `message`) VALUES
(0, 2323, 0, '2025-03-20', 'Good'),
(0, 2323, 0, '2025-04-09', 'haha bati');

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
  `status` varchar(100) NOT NULL
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
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 888, 'Ocliasa', 'Ninzo', 'Dumandan', 'BSIT', 3, 'qweqwe', '$2y$10$qzH5V3co3NfiiQ15B32MZe4/hVG6Sisb9I29Xagx7KcM.JoDSPuky', 'uploads/67bfd4fcdaca2.jpg', 'ninorollaneocliasa@gmail.com', 'Lahug Cebu City'),
(4, 22683361, 'Ocliasa', 'Nino Rollane ', 'Dumandan', 'BSIT', 3, 'qwerty', '$2y$10$5M/txXWM9CXnZLDrChSJX.1DXIq06vrBIJZltcyGG/CcIwNm.TZQW', 'uploads/67ca5ae740e75.png', 'ninorollaneocliasa@gmail.com', 'Cebu City');

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
(888, 30),
(2323, 30);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `direct_sitin`
--
ALTER TABLE `direct_sitin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `StudID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
