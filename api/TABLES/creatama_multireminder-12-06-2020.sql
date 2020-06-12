-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2020 at 06:04 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `creatama_multireminder`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `Id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `DOB` varchar(64) DEFAULT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `firebase_token` text,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`Id`, `name`, `phone`, `email`, `DOB`, `username`, `password`, `firebase_token`, `created_date`) VALUES
(1, 'Komal Nikhare', '9028221663', 'nikharekomal@gmail.com', '1990-4-30', 'komalnikhare', '827ccb0eea8a706c4c34a16891f84e7b', 'clpoJzv5vEU:APA91bEz3BNBj5HOiQQlIFf59gK4b8K1IJtS9eJlIiB8FoxkmZ2-m77iAOveSqgkNSeVFrpSa-2zEvn-OFPE34eADuLJE_1euVos3wt34ciZ5RTYwXzfu4UqYrm1OMMDVm8WziAf0SvP', '2018-08-23 09:32:50'),
(2, 'Gagan', '7620123788', 'gaganstack@gmail.com', '1984-10-20', 'gaganstack', '21020860e80ce809dfc15c739785568c', 'd8DJZfB3imc:APA91bGGsv-M3Fgd1IXUwlXP4DoALxQF1f5mCNygDEA_F-aud_411tUkSuUh9Wlxh8lvd2RkcYLPJjm-i8G65n8GXveJ10Xq-i5D0ArLAIhI5zQjOoquMoRP7FtYbHIP7bP7-fsChjvl', '2018-08-23 09:33:08'),
(3, 'Pushkar', '9975036220', 'pushkarrainchwar11@gmail.com', '1990-2-11', 'pushkarrainchwar11@gmail.com', '25d55ad283aa400af464c76d713c07ad', 'd8DJZfB3imc:APA91bGGsv-M3Fgd1IXUwlXP4DoALxQF1f5mCNygDEA_F-aud_411tUkSuUh9Wlxh8lvd2RkcYLPJjm-i8G65n8GXveJ10Xq-i5D0ArLAIhI5zQjOoquMoRP7FtYbHIP7bP7-fsChjvl', '2018-08-23 10:46:05');

-- --------------------------------------------------------

--
-- Table structure for table `persons`
--

CREATE TABLE `persons` (
  `Id` int(11) NOT NULL,
  `reminder_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `person_name` varchar(20) DEFAULT NULL,
  `person_mobile` varchar(20) DEFAULT NULL,
  `complete` varchar(2) NOT NULL DEFAULT '0',
  `complete_updated_date` datetime NOT NULL,
  `complete_remark` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `persons`
--

INSERT INTO `persons` (`Id`, `reminder_id`, `customer_id`, `person_name`, `person_mobile`, `complete`, `complete_updated_date`, `complete_remark`) VALUES
(1, 2, 1, 'Gagan', '7620123788', '1', '2018-08-24 02:45:01', 'testing by gagan via postman app'),
(2, 1, 1, 'Pushkar', '9975036220', '1', '2018-08-24 02:41:20', 'done'),
(3, 1, 1, 'Gagan', '7620123788', '1', '2018-08-24 02:41:20', 'done');

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `Id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `title` tinytext,
  `description` text,
  `remind_time` time DEFAULT NULL,
  `remind_date` date DEFAULT NULL,
  `priority` varchar(12) DEFAULT NULL,
  `repeat_type` tinytext,
  `repeat_duration` varchar(12) DEFAULT NULL,
  `repeat_type_dismiss` enum('0','1') NOT NULL DEFAULT '0',
  `complete` varchar(12) DEFAULT NULL,
  `complete_updated_date` datetime NOT NULL,
  `complete_remark` text,
  `status` varchar(12) DEFAULT NULL,
  `status_reason` tinytext,
  `status_updated_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`Id`, `customer_id`, `title`, `description`, `remind_time`, `remind_date`, `priority`, `repeat_type`, `repeat_duration`, `repeat_type_dismiss`, `complete`, `complete_updated_date`, `complete_remark`, `status`, `status_reason`, `status_updated_date`, `updated_date`, `created_date`) VALUES
(1, 1, 'My First Reminder', 'First', '20:55:00', '2018-08-23', '2', '', '00:00', '0', '', '0000-00-00 00:00:00', NULL, '', '', '0000-00-00 00:00:00', '2018-09-20 18:25:53', '2018-08-23 09:25:33'),
(2, 1, 'Fist Reminder', 'Testing', '19:55:00', '2018-08-23', '2', '', '00:00', '1', '1', '2018-08-24 01:38:56', 'all task completed', '', '', '0000-00-00 00:00:00', '2018-08-23 09:46:56', '2018-08-23 09:34:16'),
(3, 2, 'Reminder', 'my', '21:00:00', '2018-08-23', '2', '', '00:00', '0', '', '0000-00-00 00:00:00', NULL, '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2018-08-23 09:39:54'),
(4, 1, 'Second', 'My', '12:00:00', '2018-08-24', '2', '', '00:00', '0', '1', '2018-08-24 01:32:37', 'done', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2018-08-23 09:52:01');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_otp`
--

CREATE TABLE `tbl_otp` (
  `id` int(11) NOT NULL,
  `otp` int(6) DEFAULT NULL,
  `mobile` varchar(10) DEFAULT NULL,
  `time_from` varchar(20) DEFAULT NULL,
  `status` enum('OTP_SENT','OTP_SUCCESS','OTP_EXPIRED') DEFAULT NULL,
  `updated_date` datetime NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_otp`
--

INSERT INTO `tbl_otp` (`id`, `otp`, `mobile`, `time_from`, `status`, `updated_date`, `created_date`) VALUES
(1, 820497, '9028221663', '1534987929', 'OTP_SENT', '0000-00-00 00:00:00', '2018-08-23 09:32:09'),
(2, 783192, '9028221663', '1534987952', 'OTP_SUCCESS', '2018-08-23 09:32:49', '2018-08-23 09:32:32'),
(3, 385469, '7620123788', '1534987974', 'OTP_SUCCESS', '2018-08-23 09:33:06', '2018-08-23 09:32:54'),
(4, 109256, '9975036220', '1534992355', 'OTP_SUCCESS', '2018-08-23 10:46:04', '2018-08-23 10:45:55'),
(5, 724965, '7620123788', '1539916715', 'OTP_SENT', '0000-00-00 00:00:00', '2018-10-19 20:10:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `username` (`username`,`password`);

--
-- Indexes for table `persons`
--
ALTER TABLE `persons`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `reminder_id` (`reminder_id`,`person_name`,`person_mobile`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_date` (`created_date`);

--
-- Indexes for table `tbl_otp`
--
ALTER TABLE `tbl_otp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `otp` (`otp`,`mobile`,`time_from`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `persons`
--
ALTER TABLE `persons`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_otp`
--
ALTER TABLE `tbl_otp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
