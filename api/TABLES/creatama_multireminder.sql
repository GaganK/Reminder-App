-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 18, 2018 at 08:14 AM
-- Server version: 5.5.59-cll
-- PHP Version: 5.6.30

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
(1, 'Satish Jampalwar ', '9404105677', 'androidsatish02@gmail.com', '1990-7-29', 'satish', '202cb962ac59075b964b07152d234b70', 'fFZUgwpfkLo:APA91bELR0974FKx2jBZCdZBkeyxB1G6V86yNX4kgRKSg5N6Y9zolMViaxxCLpCf22F4-NI0usCPFyPUbSx35EbBIknhXcRzhXuOIUNB0Zvmn_1mUuLnlQx4WtxE7Z-9wHwL14yTX7_F', '2017-10-28 10:34:01'),
(2, 'satish test device', '9673703757', 'sat@sat.con', '2017-10-28', 'sat', '202cb962ac59075b964b07152d234b70', 'c-CLslHmcas:APA91bFS1z6nfXHXwYdPo7h1_To59DVDFwRCyddAguM_YEyd353kQ15GST7uVens7cZrWzLPOAXrytvRPIOu8TrfkBH8PyK159E3l8kCJlQJzZqnH_z30vUr6u_rDT_Iy90E6s-bQ7g9', '2017-10-28 11:42:16'),
(3, 'Pushkar', '9975036220', 'pushkarrainchwar11@gmail.com', '1990-2-11', 'pushkar3422', '827ccb0eea8a706c4c34a16891f84e7b', 'eWMML9XMlKg:APA91bGY4Oi79Y_pd7v90OS_CLWI-eWMeRBO4UXZm3293gIq460A66V8wUKGU8bulnW0BYWPC74Iqm9tMKXu_0-2jyXby1OIjmfYX4KrvWCejXTceX40x2_ewA2YCC_1rs6ewMMiIXgu', '2017-11-01 05:10:38'),
(4, 'Gagan ', '7620123788', 'gaganstack@gmail.com', '1984-10-20', 'gaganstack', '81dc9bdb52d04dc20036dbd8313ed055', 'dFOK-XKgLP4:APA91bEVJixZMUPse-sN-9O1vX5x5e9a6noa5YOqcxzpKVcV6HFOvV6UkP0VRLVCj7zRLRxGk7-b44f0k0a-vRPNnJrkxXSUzT8vEwrKQDIxob9KTPGByg5TYz_u_T8Kps56FFj61fCG', '2017-11-02 23:38:23'),
(5, 'Devyani', '8888699994', 'pune@creatagroup.com', '1993-12-1', 'devyani143', '81dc9bdb52d04dc20036dbd8313ed055', 'dYgo85qkv3k:APA91bGGqXhx5EgZzBzZnWlsZHa1PsJe726NRPQNNOPtKXX9xhkeDSOxAHzlSpWz2B02vQuRaBuQjq4gdtJvnZykXkZZ0tZgCBumOP5t77u-Ck6jzjnoeKPDrsflchBje8fvLyAlPazc', '2017-11-06 09:58:22'),
(6, 'piyush', '7506735494', 'adettiwarpiyush@gmail.com', '1988-11-11', 'piyush', '47d4240c258b6b7e52932240219d77e9', 'eEamvTqvaUA:APA91bHlunAelM_4eZzkjTVl_V2ynzTPrPvMVOCRm9ZgEtCbogca-_CZxvNV1GXbBpzJD1hdK97MEt7w_qw68wzTjiJBRd0fjkJduiJSatLXXQwE8IH4fGjJdVbbV-nAxREB3yZz4rVI', '2018-01-16 23:35:32'),
(7, 'Abhijeet B', '8446919163', 'abhijeet.seasons@gmail.com', '1992-5-19', 'me.abhijeet', '72a047f2bcb087fdd40635ad36115d55', NULL, '2018-02-08 04:16:34'),
(8, 'ganesh gaikhe', '8668408306', 'ganeah.gaikhe90@gmail.com', '1989-6-16', 'ganesh007', '4722a5eb5ff46b9a92b74d0b7d45366a', NULL, '2018-02-11 04:41:51'),
(9, 'Priyanka ', '9689180473', 'pk4968@gmail.com', '1993-1-20', 'priyanka', '1fd96777aedeadb325c66f3780054765', 'eHlva_7z1OM:APA91bGchdc8PMGcIqdsWeiObufG-EB6xpIcVWb-yrquTGxdo_KIhoxjxpo-O672eBNw7xbOKFn0soVMIlSkXvbd_rU7Xomp00zG4q3DKsFnnp7G8FhvqOC--aeJldltJ0iLLh4jzSsw', '2018-02-13 02:08:59'),
(10, 'komal', '9028221663', 'nikharekomal@gmail.com', '1990-4-30', 'komal', '0963dc2351f426d077ff39290648a5a0', 'ddHAUma6I4k:APA91bF_r3GxcHhiYfbSkjh7TBonw58x5752qGLtGAcvcoFGZ_VzsgEEs1-l5GL_Hmwj2qVeVeJkikiKv_ACHEsSzM6TM0Tz4quy3bcew9Ee8cnVFoSDdaYg082oAKqW5585Wq2Nv6tI', '2018-02-17 11:20:34'),
(11, 'komal', '9028221663', 'komalnikhare@yahoo.com', '2018-2-27', 'kn', '8c7e6965b4169689a88b313bbe7450f9', 'djWxmSMh2L8:APA91bGc49APc08bmZ48rP8OsXQ5PrI0hPIv4DfDPM-bt-_zFgyL8bE1e8MU6zpdE2QPMXm-238vuUrT18Fpyk-ZWusnRday000Al7stAEDLNrNUKNKX9Oa9MFKNsye15Yzpa2OIRjuG', '2018-02-26 14:15:46');

-- --------------------------------------------------------

--
-- Table structure for table `persons`
--

CREATE TABLE `persons` (
  `Id` int(11) NOT NULL,
  `reminder_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `person_name` varchar(20) DEFAULT NULL,
  `person_mobile` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `persons`
--

INSERT INTO `persons` (`Id`, `reminder_id`, `customer_id`, `person_name`, `person_mobile`) VALUES
(2, 2, 2, 'Sâtîsh Dã', '8380088806'),
(3, 3, 2, '$át!$# D@', '9404105677'),
(7, 5, 2, '$át!$# D@', '9404105677'),
(8, 6, 2, '$át!$# D@', '9404105677'),
(9, 7, 2, '$át!$# D@', '9404105677'),
(24, 15, 1, 'Kajal', '9673703757'),
(25, 15, 1, 'Pushkar Rainchwar', '9975036220'),
(28, 18, 4, 'My Self Vodafone', '8408046479'),
(29, 19, 4, 'My Self Vodafone', '8408046479'),
(30, 21, 5, 'Pushkar', '9975036220'),
(32, 23, 6, 'Pushkar Rainchwar Pr', '9975036220'),
(36, 26, 9, 'Pihu', '9689180473'),
(39, 30, 11, 'komal', '9028221663'),
(49, 33, 3, 'Bayako', '9028627405'),
(50, 33, 3, 'Komal Nikhare Androi', '9028221663'),
(51, 34, 3, 'Devyani', '9028627405'),
(52, 34, 3, 'Komal Nikhare Androi', '9028221663'),
(56, 41, 3, 'Komal Nikhare Androi', '9028221663'),
(57, 41, 3, 'Devyani', '9028627405'),
(58, 42, 3, 'Devyani', '9028627405');

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
  `complete` varchar(12) DEFAULT NULL,
  `complete_updated_date` datetime NOT NULL,
  `status` varchar(12) DEFAULT NULL,
  `status_reason` tinytext,
  `status_updated_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`Id`, `customer_id`, `title`, `description`, `remind_time`, `remind_date`, `priority`, `repeat_type`, `repeat_duration`, `complete`, `complete_updated_date`, `status`, `status_reason`, `status_updated_date`, `updated_date`, `created_date`) VALUES
(2, 2, 'Test Notification 2', 'generals', '17:33:00', '2017-10-29', 'High', 'M', '5', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2017-10-28 12:08:50'),
(3, 2, 'Test ', 'i am kajal', '22:44:00', '2017-10-30', 'High', 'M', '5', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '2017-10-28 12:19:18', '2017-10-28 12:15:38'),
(5, 2, 'New Reminder ', 'This is the reminder edit notification test', '10:23:00', '2017-11-02', 'High', 'WW', '5', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '2017-11-01 01:09:50', '2017-11-01 00:54:40'),
(6, 2, 'One More', 'Json object testing', '10:45:00', '2017-11-02', 'High', 'TT', '5', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2017-11-01 01:12:30'),
(7, 2, 'Big Text Style', 'Large text message', '11:46:00', '2017-11-02', 'High', 'F', '5', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '2017-11-02 00:18:40', '2017-11-01 01:17:23'),
(15, 1, 'Message Testing', 'Message sending to all recepants of this reminder', '11:20:00', '2017-11-07', 'High', 'W', '4', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '2017-11-02 00:17:04', '2017-11-01 01:51:39'),
(18, 4, 'Wakeup Testing 1', 'test by dev ', '06:00:00', '2017-11-04', 'High', 'T,W,SA,S,M,F,TH', '5', '49', '2017-11-05 06:16:41', '', '', '0000-00-00 00:00:00', '2017-11-02 23:42:34', '2017-11-02 23:41:16'),
(19, 4, 'Wakeup 2', 'testing ', '05:13:00', '2017-11-06', 'High', 'S', '2', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '2017-11-05 06:16:55', '2017-11-02 23:44:53'),
(20, 4, 'Test', 'test', '17:00:00', '2017-11-05', 'High', 'T,W,SA,M,S,F,THT,W,SA,M,S,F,TH', '5', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2017-11-05 06:18:12'),
(21, 5, 'Hi ', 'hi ', '11:22:00', '2017-11-09', 'High', 'T', '10', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '2017-11-08 23:52:54', '2017-11-06 09:59:42'),
(23, 6, 'Test', 'testing', '10:05:00', '2018-01-19', 'High', 'S,T,TH,F,W,M,SAS,T,TH,F,W,M,SA', '6', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2018-01-16 23:37:43'),
(26, 9, 'Meeting ', 'Android meeting with ', '12:45:00', '2018-02-13', 'High', 'SS', '5', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2018-02-13 02:11:39'),
(29, 11, 'my new reminder', 'testing', '12:55:00', '2018-02-28', 'High', 'T,SA,W,M,S,F,TH', '5', '49', '2018-02-28 05:51:05', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2018-02-26 14:41:30'),
(30, 11, 'my new reminder', 'testing', '12:55:00', '2018-02-28', 'High', 'T,SA,W,M,S,F,THM,S', '5', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2018-02-26 14:50:09'),
(33, 3, 'hello', 'hi', '00:16:00', '2018-04-16', 'High', 'THTH', '1', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2018-04-15 14:44:43'),
(34, 3, 'hello for title', 'hello Fort description', '08:54:00', '2018-04-18', 'High', 'TH', '1', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '2018-04-17 23:21:14', '2018-04-17 23:20:48'),
(41, 3, 'Hi', 'test', '18:19:00', '2018-05-01', 'High', 'M', '00:10', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '2018-04-30 08:34:22', '2018-04-29 10:18:18'),
(42, 3, 'test', 'hello how are you bro', '09:55:00', '2018-04-30', 'High', 'MM', '00:10', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2018-04-29 23:48:00'),
(44, 10, 'my reminder', 'for testing', '15:25:00', '2018-05-16', 'High', 'F,W,TH', '00:5', '', '0000-00-00 00:00:00', '', '', '0000-00-00 00:00:00', '2018-05-16 05:46:12', '2018-05-15 23:45:18');

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
(1, 654893, '9404105677', '1509201232', 'OTP_SUCCESS', '2017-10-28 10:33:58', '2017-10-28 10:33:52'),
(2, 310842, '9673703757', '1509205326', 'OTP_SUCCESS', '2017-10-28 11:42:14', '2017-10-28 11:42:06'),
(3, 986572, '9975036220', '1509527427', 'OTP_SUCCESS', '2017-11-01 05:10:37', '2017-11-01 05:10:27'),
(4, 203819, '7620123788', '1509636981', 'OTP_SENT', '0000-00-00 00:00:00', '2017-11-02 23:36:21'),
(5, 183675, '7620123788', '1509637082', 'OTP_SUCCESS', '2017-11-02 23:38:22', '2017-11-02 23:38:02'),
(6, 673891, '8888699994', '1509980293', 'OTP_SUCCESS', '2017-11-06 09:58:21', '2017-11-06 09:58:13'),
(7, 236879, '7506735494', '1516120525', 'OTP_SUCCESS', '2018-01-16 23:35:32', '2018-01-16 23:35:25'),
(8, 243167, '8446919163', '1518081381', 'OTP_SUCCESS', '2018-02-08 04:16:32', '2018-02-08 04:16:21'),
(9, 21467, '8446919163', '1518170724', 'OTP_SUCCESS', '2018-02-09 05:05:49', '2018-02-09 05:05:24'),
(10, 146728, '8446919163', '1518170761', 'OTP_SUCCESS', '2018-02-09 05:06:08', '2018-02-09 05:06:01'),
(11, 507421, '8446919163', '1518170919', 'OTP_SUCCESS', '2018-02-09 05:09:01', '2018-02-09 05:08:39'),
(12, 704832, '8446919163', '1518171379', 'OTP_SUCCESS', '2018-02-09 05:16:26', '2018-02-09 05:16:19'),
(13, 865104, '8668408306', '1518342090', 'OTP_SUCCESS', '2018-02-11 04:41:50', '2018-02-11 04:41:30'),
(14, 184590, '8668408306', '1518367478', 'OTP_SUCCESS', '2018-02-11 23:44:55', '2018-02-11 23:44:38'),
(15, 368975, '8668408306', '1518367502', 'OTP_SENT', '0000-00-00 00:00:00', '2018-02-11 23:45:02'),
(16, 210584, '8668408306', '1518367509', 'OTP_SUCCESS', '2018-02-11 23:45:15', '2018-02-11 23:45:09'),
(17, 364719, '9689180473', '1518505723', 'OTP_SUCCESS', '2018-02-13 02:08:58', '2018-02-13 02:08:43'),
(18, 840973, '9028221663', '1518884413', 'OTP_SUCCESS', '2018-02-17 11:20:32', '2018-02-17 11:20:13'),
(19, 905817, '9028221663', '1519629320', 'OTP_SUCCESS', '2018-02-26 14:15:45', '2018-02-26 14:15:20');

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
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `persons`
--
ALTER TABLE `persons`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tbl_otp`
--
ALTER TABLE `tbl_otp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
