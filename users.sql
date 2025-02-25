-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2025 at 04:10 PM
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
(1, 2323, 'asdas', 'dasdas', 'dasdas', 'College of Engineering', 1, 'asdasd', '$2y$10$E3lrN9Gylvi2a/wdGIuvSepFpR3Yjwx71kZ8q1oeTYVGPuFsBuKMi', 'uploads/67af416ac9b40.jpg', '', ''),
(2, 888, 'Ocliasa', 'Nino Rollane', 'Dumandan', 'BSIT', 3, 'qweqwe', '$2y$10$qzH5V3co3NfiiQ15B32MZe4/hVG6Sisb9I29Xagx7KcM.JoDSPuky', 'uploads/67ae1195ef198.gif', 'ninorollaneocliasa684@gmail.com', 'Cebu City');

-- --------------------------------------------------------

--
-- Table structure for table `student_session`
--

CREATE TABLE `student_session` (
  `id_number` int(11) NOT NULL,
  `remaining_sessions` int(11) NOT NULL DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`StudID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `StudID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

-- Insert default sessions for existing students who don't have an entry
INSERT INTO student_session (id_number, remaining_sessions)
SELECT IDNO, 30
FROM students s
WHERE NOT EXISTS (
    SELECT 1 FROM student_session ss 
    WHERE ss.id_number = s.IDNO
);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
