-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2024 at 12:24 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gba_sample`
--

-- --------------------------------------------------------

--
-- Table structure for table `admission`
--

CREATE TABLE `admission` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `LRN` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `PSA` varchar(255) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `phonenumber` varchar(255) NOT NULL,
  `emailaddress` varchar(255) NOT NULL,
  `homeaddress` varchar(255) NOT NULL,
  `specialED` varchar(255) NOT NULL,
  `grade` varchar(255) NOT NULL,
  `withLRN` varchar(255) NOT NULL,
  `lastGradelevel` varchar(255) NOT NULL,
  `lastSY` varchar(255) NOT NULL,
  `lastSchool` varchar(255) NOT NULL,
  `schoolAddress` varchar(255) NOT NULL,
  `schoolType` varchar(255) NOT NULL,
  `fatherName` varchar(255) NOT NULL,
  `fatheremail` varchar(255) NOT NULL,
  `fatherSchool` varchar(255) NOT NULL,
  `fatherJob` varchar(255) NOT NULL,
  `fatherNumber` varchar(255) NOT NULL,
  `motherName` varchar(255) NOT NULL,
  `motheremail` varchar(255) NOT NULL,
  `motherSchool` varchar(255) NOT NULL,
  `motherJob` varchar(255) NOT NULL,
  `motherNumber` varchar(255) NOT NULL,
  `currentdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admission`
--

INSERT INTO `admission` (`id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `specialED`, `grade`, `withLRN`, `lastGradelevel`, `lastSY`, `lastSchool`, `schoolAddress`, `schoolType`, `fatherName`, `fatheremail`, `fatherSchool`, `fatherJob`, `fatherNumber`, `motherName`, `motheremail`, `motherSchool`, `motherJob`, `motherNumber`, `currentdate`) VALUES
(17, 'Justine Kyle', 'Gupo', 'S', 'N/A', '2002-09-01', 'N/A', 'Born Again', 'Male', '09991331245', 'jako@gmail.com', 'Dasma City', 'No', 'Grade 4', 'No', 'Grade 3', '2016-2017', 'Sta. Cristina Elementary School', 'Dasma', 'Public', 'Jay Gupo', 'jay@gmail.com', 'College Graduate', 'Full time employee', '09986541778', 'Gupo', 'weqwe@gmail.com', 'College Graduate', 'Full time employee', '09985547886', '2024-10-04');

-- --------------------------------------------------------

--
-- Table structure for table `archive`
--

CREATE TABLE `archive` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `LRN` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `PSA` varchar(255) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `phonenumber` varchar(255) NOT NULL,
  `emailaddress` varchar(255) NOT NULL,
  `homeaddress` varchar(255) NOT NULL,
  `grade` varchar(255) NOT NULL,
  `fatherName` varchar(255) NOT NULL,
  `fatherNumber` varchar(255) NOT NULL,
  `motherName` varchar(255) NOT NULL,
  `motherNumber` varchar(255) NOT NULL,
  `currentdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `archive`
--

INSERT INTO `archive` (`id`, `student_id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `grade`, `fatherName`, `fatherNumber`, `motherName`, `motherNumber`, `currentdate`) VALUES
(1, 'GBA-2024-001', 'Earl Joshua', 'Espiritu', 'Tanghal', '107018080295', '2002-09-15', 'N/A', 'Born Again', 'Male', '09088004563', 'ej@gmail.com', 'Dasma City', 'Grade 9', 'Randy Espiritu', '09958874233', 'Mayumi Espiritu', '09986645231', '2024-10-03');

-- --------------------------------------------------------

--
-- Table structure for table `archive_admission`
--

CREATE TABLE `archive_admission` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `LRN` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `PSA` varchar(255) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `phonenumber` varchar(255) NOT NULL,
  `emailaddress` varchar(255) NOT NULL,
  `homeaddress` varchar(255) NOT NULL,
  `specialED` varchar(255) NOT NULL,
  `grade` varchar(255) NOT NULL,
  `withLRN` varchar(255) NOT NULL,
  `lastGradelevel` varchar(255) NOT NULL,
  `lastSY` varchar(255) NOT NULL,
  `lastSchool` varchar(255) NOT NULL,
  `schoolAddress` varchar(255) NOT NULL,
  `schoolType` varchar(255) NOT NULL,
  `fatherName` varchar(255) NOT NULL,
  `fatherSchool` varchar(255) NOT NULL,
  `fatherJob` varchar(255) NOT NULL,
  `fatherNumber` varchar(255) NOT NULL,
  `motherName` varchar(255) NOT NULL,
  `motherSchool` varchar(255) NOT NULL,
  `motherJob` varchar(255) NOT NULL,
  `motherNumber` varchar(255) NOT NULL,
  `currentdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `enroll`
--

CREATE TABLE `enroll` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `LRN` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `PSA` varchar(255) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `phonenumber` varchar(255) NOT NULL,
  `emailaddress` varchar(255) NOT NULL,
  `homeaddress` varchar(255) NOT NULL,
  `grade` varchar(255) NOT NULL,
  `fatherName` varchar(255) NOT NULL,
  `fatherNumber` varchar(255) NOT NULL,
  `motherName` varchar(255) NOT NULL,
  `motherNumber` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `enroll`
--

INSERT INTO `enroll` (`id`, `student_id`, `firstname`, `lastname`, `middlename`, `LRN`, `birthday`, `PSA`, `religion`, `gender`, `phonenumber`, `emailaddress`, `homeaddress`, `grade`, `fatherName`, `fatherNumber`, `motherName`, `motherNumber`) VALUES
(2, 'GBA-2024-002', 'Ivan', 'Formento', 'Tayab', 'N/A', '2001-03-13', 'N/A', 'Roman Catholic ', 'Male', '09986534785', 'ivan@gmail.com', 'Dasma City', 'Grade 10', 'Jeck Formento', '09965887411', 'Janneth Formento', '09987456322'),
(3, 'GBA-2024-003', 'Harbey', 'Songalia', 'Roadiel', 'N/A', '2003-03-23', 'N/A', 'Catholic', 'Male', '09955632447', 'harbey@gmail.com', 'Dasma City', 'Grade 9', 'Ferdinand Songalia', '09985634745', 'Rosalinda Songalia', '09985547886'),
(16, 'GBA-2024-016', 'Earl Joshua', 'Espiritu', 'Tanghal', 'N/A', '2024-09-15', 'N/A', 'Born Again', 'Male', '09991331245', 'ej@gmail.com', 'Dasma City', 'Grade 9', 'Randy Espiritu', '09986541778', 'Mayumi Espiritu', '09986574563');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `type`, `firstname`, `lastname`) VALUES
(1, 'phoebe', 'phoebe123', 2, 'Phoebe Rose', 'Montes'),
(2, 'lester', 'lester123', 1, 'Lester Mark', 'Pornel'),
(16, 'GBA-2024-001', '2002-09-15', 0, 'Earl Joshua', 'Espiritu'),
(47, 'GBA-2024-002', '2001-03-13', 0, 'Ivan', 'Formento'),
(49, 'GBA-2024-003', '2003-03-23', 0, 'Harbey', 'Songalia');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admission`
--
ALTER TABLE `admission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `archive`
--
ALTER TABLE `archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `archive_admission`
--
ALTER TABLE `archive_admission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enroll`
--
ALTER TABLE `enroll`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admission`
--
ALTER TABLE `admission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `archive`
--
ALTER TABLE `archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `archive_admission`
--
ALTER TABLE `archive_admission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `enroll`
--
ALTER TABLE `enroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
