-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2026 at 11:00 AM
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
-- Database: `frisucode_ms`
--

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `full_name` varchar(200) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `education_level` varchar(100) DEFAULT NULL,
  `school_name` varchar(200) DEFAULT NULL,
  `status` enum('active','graduated','dropped_out') DEFAULT 'active',
  `sponsor_id` int(11) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `location_name` varchar(255) DEFAULT '',
  `google_map_link` text DEFAULT '',
  `class_level` varchar(100) DEFAULT '',
  `guardian_name` varchar(255) DEFAULT '',
  `guardian_phone` varchar(50) DEFAULT '',
  `guardian_relation` varchar(100) DEFAULT '',
  `dropout_reason` text DEFAULT NULL,
  `dropout_date` date DEFAULT NULL,
  `dropout_recorded_by` int(11) DEFAULT NULL,
  `graduation_date` date DEFAULT NULL,
  `graduation_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beneficiaries`
--

INSERT INTO `beneficiaries` (`id`, `student_id`, `full_name`, `dob`, `gender`, `education_level`, `school_name`, `status`, `sponsor_id`, `bio`, `photo_url`, `registered_at`, `updated_at`, `location_name`, `google_map_link`, `class_level`, `guardian_name`, `guardian_phone`, `guardian_relation`, `dropout_reason`, `dropout_date`, `dropout_recorded_by`, `graduation_date`, `graduation_notes`) VALUES
(1, 'FSC-01-0001-2026', 'Albetina Kimaro', '0000-00-00', 'Female', 'University', 'TICD', 'graduated', NULL, '', NULL, '2026-03-05 11:54:12', '2026-06-29 08:15:46', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(2, 'FSC-01-0002-2026', 'Victor', '2001-09-21', 'Male', 'University', 'IAA', 'active', 3, 'Very good student', NULL, '2026-03-26 08:54:27', '2026-06-29 08:15:46', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(3, 'FSC-01-0003-2026', 'Amina Juma', '2026-06-26', 'Female', 'Secondary_O', 'Arusha School', 'active', 3, '', NULL, '2026-06-22 10:37:03', '2026-06-29 09:10:10', 'Arusha', '', '', 'Marium Juma', '25578459613', 'Mother', NULL, NULL, NULL, NULL, NULL),
(4, 'FSC-01-0004-2026', 'Peter (Cosmas) Warioba', '2010-11-14', 'Other', 'Primary', 'Idodi Primary School', 'active', 10, NULL, NULL, '2026-06-22 12:00:49', '2026-06-29 08:15:46', 'Iringa', '', 'Standard IV', '', '', '', NULL, NULL, NULL, NULL, NULL),
(5, 'FSC-01-0014-2026', 'Clarence Kisanga', '2010-04-14', 'Other', 'Primary', 'Manyire Primary School', 'active', 12, NULL, NULL, '2026-06-22 12:00:50', '2026-06-29 08:15:46', 'Manyire', '', 'Standard IV', '', '', '', NULL, NULL, NULL, NULL, NULL),
(6, 'FSC-01-0013-2026', 'Nora Amani', '2016-03-31', 'Other', 'Primary', 'Nguruma Primary Tengeru', 'active', NULL, NULL, NULL, '2026-06-22 12:00:50', '2026-06-29 08:15:46', 'Chama', '', 'Standard IV', '', '', '', NULL, NULL, NULL, NULL, NULL),
(7, 'FSC-01-0012-2026', 'Jordan Mwaga', '2013-02-18', 'Other', 'Primary', 'Kilimani Primary', 'active', NULL, NULL, NULL, '2026-06-22 12:00:50', '2026-06-29 08:15:46', 'Usa River', '', 'Standard V', '', '', '', NULL, NULL, NULL, NULL, NULL),
(8, 'FSC-01-0011-2026', 'Eliia Massawe', '2012-02-26', 'Other', 'Primary', 'Silver primary Kikwe', 'active', NULL, NULL, NULL, '2026-06-22 12:00:50', '2026-06-29 08:15:46', 'Kikwe', '', 'Standard VI', '', '', '', NULL, NULL, NULL, NULL, NULL),
(9, 'FSC-01-0010-2026', 'Ester (Enock) Mkimbo', '2009-09-12', 'Other', 'Primary', 'Levolosi Primary Arusha', 'active', 13, NULL, NULL, '2026-06-22 12:00:50', '2026-06-29 08:15:46', 'Levolosi', '', 'Standard VI', '', '', '', NULL, NULL, NULL, NULL, NULL),
(10, 'FSC-01-0009-2026', 'Careen Sumari', '2014-12-28', 'Other', 'Primary', 'Nganana Primary', 'active', 14, NULL, NULL, '2026-06-22 12:00:50', '2026-06-29 08:15:46', 'Nganana', '', 'Standard VI', '', '', '', NULL, NULL, NULL, NULL, NULL),
(11, 'FSC-01-0008-2026', 'Mohamed Rajab', '2011-12-25', 'Other', 'Primary', 'Maweni Primary', 'active', 15, NULL, NULL, '2026-06-22 12:00:50', '2026-06-29 08:15:46', 'Maweni', '', 'Standard VI', '', '', '', NULL, NULL, NULL, NULL, NULL),
(12, 'FSC-01-0007-2026', 'Assumini Hassan', '2010-05-06', 'Other', 'Primary', 'Maweni Primary', 'active', 16, NULL, NULL, '2026-06-22 12:00:50', '2026-06-29 08:15:46', 'Maweni', '', 'Standard VII', '', '', '', NULL, NULL, NULL, NULL, NULL),
(13, 'FSC-01-0006-2026', 'Zena (Ibrahim) Nizari', '2011-10-06', 'Other', 'Primary', 'Nganana Primary', 'active', 17, NULL, NULL, '2026-06-22 12:00:50', '2026-06-29 08:15:46', 'Nganana', '', 'Standard VII', '', '', '', NULL, NULL, NULL, NULL, NULL),
(14, 'FSC-01-0005-2026', 'Gloria Juma', '2011-09-16', 'Other', 'Primary', 'Amani Nursery and Primary', 'active', 18, NULL, NULL, '2026-06-22 12:00:50', '2026-06-29 08:15:46', 'Usa River', '', 'Standard VII', '', '', '', NULL, NULL, NULL, NULL, NULL),
(15, 'FSC-01-0022-2026', 'Juma Ramadhani', '2010-04-04', 'Other', 'Secondary_O', 'Kikwe Secondary', 'active', 20, NULL, NULL, '2026-06-22 12:00:51', '2026-06-29 08:15:46', 'Nganana', '', 'Form I', '', '', '', NULL, NULL, NULL, NULL, NULL),
(16, 'FSC-01-0021-2026', 'Rajab (Yahaya) Mkahiyo', '2009-05-23', 'Other', 'Secondary_O', 'Kikwe Secondary', 'active', 21, NULL, NULL, '2026-06-22 12:00:51', '2026-06-29 08:15:46', 'Nganana', '', 'Form I', '', '', '', NULL, NULL, NULL, NULL, NULL),
(17, 'FSC-01-0020-2026', 'Salma (Yahaya) Mkahiyo', '2009-05-23', 'Other', 'Secondary_O', 'Kikwe Secondary', 'active', 21, NULL, NULL, '2026-06-22 12:00:51', '2026-06-29 08:15:46', 'Nganana', '', 'Form I', '', '', '', NULL, NULL, NULL, NULL, NULL),
(18, 'FSC-01-0019-2026', 'Ezekiel (Thomas) Kisinza', '2007-06-06', 'Other', 'Secondary_O', 'Kikwe', 'active', 22, NULL, NULL, '2026-06-22 12:00:51', '2026-06-29 08:15:46', 'Nganana', '', 'Form I', '', '', '', NULL, NULL, NULL, NULL, NULL),
(19, 'FSC-01-0018-2026', 'Gracious Massawe', '2010-01-01', 'Other', 'Secondary_O', 'D\'alzon Girls Sec School', 'active', 23, NULL, NULL, '2026-06-22 12:00:51', '2026-06-29 08:15:46', 'Arusha', '', 'Form II', '', '', '', NULL, NULL, NULL, NULL, NULL),
(20, 'FSC-01-0017-2026', 'Ramadhan Rajab', '2006-05-05', 'Other', 'Secondary_O', 'Maweni Primary', 'active', NULL, NULL, NULL, '2026-06-22 12:00:51', '2026-06-29 08:15:46', 'Maweni', '', 'Form II', '', '', '', NULL, NULL, NULL, NULL, NULL),
(21, 'FSC-01-0016-2026', 'Thamali Mbile', '2008-04-13', 'Other', 'Secondary_O', 'The Voice Secondary', 'active', 25, NULL, NULL, '2026-06-22 12:00:51', '2026-06-29 08:15:46', 'Sakina Arusha', '', 'Form II', '', '', '', NULL, NULL, NULL, NULL, NULL),
(22, 'FSC-01-0015-2026', 'Silus Malongoza', '2008-01-03', 'Other', 'Secondary_O', 'Mlangarini Sec', 'active', NULL, NULL, NULL, '2026-06-22 12:00:51', '2026-06-29 08:15:46', 'Nambala', '', 'Form II', '', '', '', NULL, NULL, NULL, NULL, NULL),
(23, 'FSC-01-0029-2026', 'Christina Marceli', '2008-10-05', 'Other', 'Secondary_O', 'KIKWE SEC', 'active', 30, NULL, NULL, '2026-06-22 12:00:52', '2026-06-29 08:15:46', 'Nganana', '', 'Form III', '', '', '', NULL, NULL, NULL, NULL, NULL),
(24, 'FSC-01-0028-2026', 'Noreen Joshua', '2006-06-15', 'Other', 'Secondary_O', 'KIKWE SEC', 'active', 31, NULL, NULL, '2026-06-22 12:00:52', '2026-06-29 08:15:46', 'Nganana', '', 'Form III', '', '', '', NULL, NULL, NULL, NULL, NULL),
(25, 'FSC-01-0027-2026', 'Mwajuma (Hamis) Kingu', '2008-03-04', 'Other', 'Secondary_O', 'KIKWE SEC', 'active', 32, NULL, NULL, '2026-06-22 12:00:52', '2026-06-29 08:15:46', 'Nganana', '', 'Form III', '', '', '', NULL, NULL, NULL, NULL, NULL),
(26, 'FSC-01-0026-2026', 'Saidi (Omari) Kiula', '2007-01-20', 'Other', 'Secondary_O', 'KIKWE SEC', 'active', 33, NULL, NULL, '2026-06-22 12:00:52', '2026-06-29 08:15:46', 'Nganana', '', 'Form III', '', '', '', NULL, NULL, NULL, NULL, NULL),
(27, 'FSC-01-0025-2026', 'Eliakesia (Michael) Mzirai', '2002-10-02', 'Other', 'Secondary_O', 'KIKWE SEC', 'active', 34, NULL, NULL, '2026-06-22 12:00:52', '2026-06-29 08:15:46', 'Kikwe', '', 'Form IV', '', '', '', NULL, NULL, NULL, NULL, NULL),
(28, 'FSC-01-0024-2026', 'Mwajuma Abdi', '2003-06-20', 'Other', 'Secondary_O', 'Kibondo Seconday in Kigoma, goverment choose', 'active', 35, NULL, NULL, '2026-06-22 12:00:52', '2026-06-29 08:15:46', 'Kikwe', '', 'Form VI', '', '', '', NULL, NULL, NULL, NULL, NULL),
(29, 'FSC-01-0023-2026', 'Johnson Kitomari', '2005-03-16', 'Other', 'Secondary_O', '', 'active', 38, NULL, NULL, '2026-06-22 12:00:52', '2026-06-29 08:15:46', 'Sing\'isi', '', 'Form V', '', '', '', NULL, NULL, NULL, NULL, NULL),
(30, 'FSC-01-0035-2026', 'Dickson Swai', '2004-09-08', 'Other', 'Secondary_O', 'Sengerema Secondary Nganana', 'active', 39, NULL, NULL, '2026-06-22 12:00:53', '2026-06-29 08:15:46', 'Nambala', '', 'Form V', '', '', '', NULL, NULL, NULL, NULL, NULL),
(31, 'FSC-01-0034-2026', 'Nickson Samson', '2004-02-24', 'Other', 'Secondary_O', 'Chamwino Sec', 'active', 40, NULL, NULL, '2026-06-22 12:00:53', '2026-06-29 08:15:46', 'Nambala', '', 'Form VI', '', '', '', NULL, NULL, NULL, NULL, NULL),
(32, 'FSC-01-0033-2026', 'Mariamu Ramadhani', '2003-10-23', 'Other', 'University', 'Dar Es Salaam', 'active', 42, NULL, NULL, '2026-06-22 12:00:53', '2026-06-29 08:15:46', 'Singisi', '', 'until 2026', '', '', '', NULL, NULL, NULL, NULL, NULL),
(33, 'FSC-01-0032-2026', 'Amina Shabani', '2004-04-07', 'Other', 'University', 'Dar es Salaam TanzanI Institute for Accountants', 'active', 43, NULL, NULL, '2026-06-22 12:00:53', '2026-06-29 08:15:46', 'Nganana', '', '3 year, start October 2024', '', '', '', NULL, NULL, NULL, NULL, NULL),
(34, 'FSC-01-0031-2026', 'Victor Mbonea', '2001-09-21', 'Other', 'University', 'Institute of Accountancy Arusha IAA from Nov 2020 - duration 3 years - in Arusha', 'active', 45, '', NULL, '2026-06-22 12:00:53', '2026-06-29 09:06:21', 'Nambala', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(35, 'FSC-01-0030-2026', 'Joshua Labani', '2001-01-01', 'Other', 'University', 'Manyara Nangwa College for Science and Technology', 'active', 46, NULL, NULL, '2026-06-22 12:00:53', '2026-06-29 08:15:46', 'Manyara', '', 'Decision dec 22nd', '', '', '', NULL, NULL, NULL, NULL, NULL),
(36, 'FSC-01-0040-2026', 'Baraka (Hussein) Juma', '2004-04-17', 'Other', 'University', 'Buhare Institute Musoma', 'active', 49, NULL, NULL, '2026-06-22 12:00:54', '2026-06-29 08:15:46', 'Nambala', '', 'in 3 year', '', '', '', NULL, NULL, NULL, NULL, NULL),
(37, 'FSC-01-0039-2026', 'Bernard Baltasari', '2002-01-03', 'Other', 'University', 'Institute of Rural Development Planing', 'active', 50, NULL, NULL, '2026-06-22 12:00:54', '2026-06-29 08:15:46', 'Nambala', '', 'in 2nd year', '', '', '', NULL, NULL, NULL, NULL, NULL),
(38, 'FSC-01-0038-2026', 'Cosmas Warioba', '2001-08-15', 'Other', 'University', 'Muhimbili University', 'active', 52, NULL, NULL, '2026-06-22 12:00:54', '2026-06-29 08:15:46', 'Iringa', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(39, 'FSC-01-0037-2026', 'Ombeni Labani', '1999-08-01', 'Other', 'University', 'CDTI TENGERU', 'active', 55, NULL, NULL, '2026-06-22 12:00:54', '2026-06-29 08:15:46', 'Kikwe', '', '3 year', '', '', '', NULL, NULL, NULL, NULL, NULL),
(40, 'FSC-01-0036-2026', 'Adelina Albertina', '1998-05-24', 'Other', 'University', 'Community Development Training Institute in Tengeru', 'active', 56, NULL, NULL, '2026-06-22 12:00:54', '2026-06-29 08:15:46', 'Tengeru', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(41, '', 'Veneranda Tarimo', '2001-05-05', 'Other', 'University', 'Kilimatinde Nurses training school', 'graduated', NULL, '', NULL, '2026-06-22 12:00:55', '2026-06-29 08:20:47', 'Singida', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(42, 'FSC-01-0042-2026', 'Nurdin Ali', '2004-08-11', 'Other', 'Vocational', 'NANGWA VTC', 'active', 58, NULL, NULL, '2026-06-22 12:00:55', '2026-06-29 08:15:46', 'Nambala', '', 'finish 2025 July', '', '', '', NULL, NULL, NULL, NULL, NULL),
(43, 'FSC-01-0043-2026', 'Rashid Saidi', '2010-01-01', 'Other', 'Vocational', 'LEGURUKI VCT', 'active', 59, NULL, NULL, '2026-06-22 12:00:55', '2026-06-29 08:15:46', 'Karangai', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(44, 'FSC-01-0044-2026', 'Simon Ramadhani', '2003-06-19', 'Other', 'Vocational', 'Arusha Techinical', 'active', 49, NULL, NULL, '2026-06-22 12:00:55', '2026-06-29 08:15:46', 'Singisi', '', 'finish 2025 July', '', '', '', NULL, NULL, NULL, NULL, NULL),
(45, 'FSC-01-0045-2026', 'Riziki Malaki', '1998-04-07', 'Other', 'Primary', 'home, mother take care', 'active', 60, NULL, NULL, '2026-06-22 12:00:55', '2026-06-29 08:15:46', 'Usa River', '', 'Standard ', '', '', '', NULL, NULL, NULL, NULL, NULL),
(46, 'FSC-01-0046-2026', 'Joel Lyatuu', '2008-04-26', 'Other', 'Primary', 'Patandi Primary', 'active', 61, NULL, NULL, '2026-06-22 12:00:55', '2026-06-29 08:15:46', 'Manyara', '', 'Standard ', '', '', '', NULL, NULL, NULL, NULL, NULL),
(47, NULL, 'Veneranda Tarimo', '0000-00-00', 'Other', 'University', 'Kilimatinde Nurses training school', 'active', NULL, '', NULL, '2026-06-30 11:50:04', '2026-06-30 11:50:04', 'Singida', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(50, NULL, 'Simon Ramadhani', '0000-00-00', 'Other', 'Vocational', 'Arusha Techinical', 'active', NULL, '', NULL, '2026-06-30 11:50:04', '2026-06-30 11:50:04', 'Singisi', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(62, NULL, 'Victor Mbonea', '0000-00-00', 'Other', 'University', 'Institute of Accountancy Arusha IAA from Nov 2020 - duration 3 years - in Arusha', 'active', NULL, '', NULL, '2026-06-30 11:50:04', '2026-06-30 11:50:04', 'Nambala', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(67, NULL, 'Saidi (Omari) Kiula', '0000-00-00', 'Other', 'Secondary_O', 'KIKWE SEC', 'active', NULL, '', NULL, '2026-06-30 11:50:04', '2026-06-30 11:50:04', 'Nganana', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(73, NULL, 'Salma (Yahaya) Mkahiyo', '0000-00-00', 'Other', 'Secondary_O', 'Kikwe Secondary', 'active', NULL, '', NULL, '2026-06-30 11:50:04', '2026-06-30 11:50:04', 'Nganana', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(77, NULL, 'Thamali Mbile', '0000-00-00', 'Other', 'Secondary_O', 'The Voice Secondary', 'active', NULL, '', NULL, '2026-06-30 11:50:04', '2026-06-30 11:50:04', 'Sakina Arusha', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(78, NULL, 'Silus Malongoza', '0000-00-00', 'Other', 'Secondary_O', 'Mlangarini Sec', 'active', NULL, '', NULL, '2026-06-30 11:50:04', '2026-06-30 11:50:04', 'Nambala', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(87, NULL, 'Zena (Ibrahim) Nizari', '0000-00-00', 'Other', 'Primary', 'Nganana Primary', 'active', NULL, '', NULL, '2026-06-30 11:50:04', '2026-06-30 11:50:04', 'Nganana', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(91, NULL, 'Victor', '0000-00-00', 'Male', 'University', 'IAA', 'active', NULL, '', NULL, '2026-06-30 11:50:04', '2026-06-30 11:50:04', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL),
(93, 'FSC-01-0047-2026', 'Aisha Ramadhani', '1996-12-09', 'Female', 'University', '', 'graduated', NULL, 'Working', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Kikwe', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(94, 'FSC-01-0048-2026', 'Salibu Elibariki', '2002-04-23', 'Male', 'University', 'College for Tourism in Arusha', 'graduated', NULL, 'College for Tourism in Arusha', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Doluti', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(95, 'FSC-01-0049-2026', 'Joshua Akico', '1998-08-28', 'Male', 'University', 'Rukwa Institute of business management', 'graduated', NULL, 'Rukwa Institute of business management', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Rukwa', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(96, 'FSC-01-0050-2026', 'Frederick Kilomari', '2002-06-21', 'Male', 'University', '', 'graduated', NULL, '', NULL, '2026-07-02 11:09:41', '2026-07-13 08:58:59', 'Singisi', '', 'Year 4', '', '', '', NULL, NULL, NULL, '2026-07-13', ''),
(97, 'FSC-01-0051-2026', 'Anita Cutlhberth', '1999-02-01', 'Female', 'University', '', 'graduated', NULL, 'Working', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Arusha', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(98, 'FSC-01-0052-2026', 'James Joseph', '1997-09-12', 'Male', 'University', '', 'graduated', NULL, 'Working', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Mlangarini', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(99, 'FSC-01-0053-2026', 'Jetta Samuel', NULL, 'Male', 'University', '', 'graduated', NULL, 'Working', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Usa River', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(100, 'FSC-01-0054-2026', 'Raheli Akico', '1995-05-05', 'Female', 'University', '', 'graduated', NULL, 'Teacher', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Arusha', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(101, 'FSC-01-0055-2026', 'Saufa Juma', '1991-03-02', 'Female', 'University', '', 'graduated', NULL, 'Teacher', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Dodoma', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(102, 'FSC-01-0056-2026', 'Christian Joseph', '1991-03-20', 'Male', 'University', '', 'graduated', NULL, 'Teacher / Working as journalist', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Arusha', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(103, 'FSC-01-0057-2026', 'Godlove Labani', '1995-09-27', 'Male', 'University', '', 'graduated', NULL, 'Teacher', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', '', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(104, 'FSC-01-0058-2026', 'Elisha Daudi', '1996-03-25', 'Male', 'University', 'Leguruki Voc', 'graduated', NULL, 'Started his life driving motorcycle as his work', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Leguruki Voc', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(105, 'FSC-01-0059-2026', 'Godlove Akico', NULL, 'Male', 'University', '', 'graduated', NULL, 'Working as fundi constructor', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Nambala', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(106, 'FSC-01-0060-2026', 'Hamisi Ali Ramadhani', '1997-11-07', 'Male', 'University', 'Nangwa Vocational training centre', 'graduated', NULL, 'Working as fundi constructor, Nangwa Vocational training centre', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Manyara', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(107, 'FSC-01-0061-2026', 'Clara Kimaro', '1990-03-12', 'Female', 'University', 'Tanzanian International University', 'graduated', NULL, 'Working she employed herself she opened up a stationery, Tanzanian International University', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Dar Es Salaam', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(108, 'FSC-01-0062-2026', 'Diana Kisaka', '1999-09-30', 'Female', 'University', 'University of Dar es Salaam', 'graduated', NULL, 'University of Dar es Salaam', NULL, '2026-07-02 11:09:41', '2026-07-02 11:09:41', 'Dar es Salaam', '', 'Graduated', '', '', '', NULL, NULL, NULL, NULL, NULL),
(109, 'FSC-01-0063-2026', 'Jovin Unambwe', '2010-04-17', 'Male', 'Secondary', 'Nursery Nambala', 'dropped_out', NULL, 'Nursery Nambala - No further support needed', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Nambala', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(110, 'FSC-01-0064-2026', 'Elizabeth Moses', '2008-04-08', 'Female', 'Secondary', 'Nambala Primary', 'dropped_out', NULL, 'Nambala Primary PENDING/MATERNITY', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Kikwe', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(111, 'FSC-01-0065-2026', 'Upendo Abrahamu', '2004-12-06', 'Female', 'Secondary', '', 'dropped_out', NULL, 'OUT OF PROJECT - No further support needed', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Nganana', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(112, 'FSC-01-0066-2026', 'Zeituni Saidi', '1999-07-29', 'Female', 'Secondary', 'VTC Ngombe', 'dropped_out', NULL, 'Finished cook in VTC Ngombe, support further', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Nganana', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(113, 'FSC-01-0067-2026', 'Zilduna Juma', '2001-03-01', 'Female', 'Secondary', 'ELCT Reha Center', 'dropped_out', NULL, 'ELCT Reha Center, Abschluss Schneiderin', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Usa River', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(114, 'FSC-01-0068-2026', 'Kitemi Damas', '1997-08-06', 'Male', 'Secondary', '', 'dropped_out', NULL, 'Out of project, not contact for a long time', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', '', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(115, 'FSC-01-0069-2026', 'Najma Mohamed', '2000-01-01', 'Female', 'Secondary', '', 'dropped_out', NULL, 'Out of project, left without information', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Kikwe', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(116, 'FSC-01-0070-2026', 'Joseph Mihayo', '1993-08-03', 'Male', 'Secondary', 'University of Dar es Salaam', 'dropped_out', NULL, 'He joined University of Dar es Salaam and he never contact us', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Iringa', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(117, 'FSC-01-0071-2026', 'Anna Kisanga', '1998-12-23', 'Female', 'Secondary', '', 'dropped_out', NULL, 'Out of project', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Arusha', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(118, 'FSC-01-0072-2026', 'Shabani Juma', '1993-08-13', 'Male', 'Secondary', '', 'dropped_out', NULL, 'Looking for a job, University finished, without job', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Moshi', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(119, 'FSC-01-0073-2026', 'Prince Felix', '1994-04-10', 'Male', 'Secondary', 'College of African wildlife Management Moshi', 'dropped_out', NULL, 'Finished University 2019, we will consider out of program', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Iringa', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(120, 'FSC-01-0074-2026', 'Yusufu Ramadhani', '1994-01-01', 'Male', 'Secondary', '', 'dropped_out', NULL, 'Consider out of project as we never have contact, taken by relatives', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Singisi', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(121, 'FSC-01-0075-2026', 'Isaka Unambwe', '2000-09-18', 'Male', 'Secondary', 'Kikwe', 'dropped_out', NULL, 'Out of support for school', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Nambala', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(122, 'FSC-01-0076-2026', 'Rauza Maldini', '2008-01-01', 'Female', 'Secondary', 'Patanumbo Primary', 'dropped_out', NULL, 'Difficult to deal with family, shifted away, mother unreachable, never contact us', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Patanumbo', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(123, 'FSC-01-0077-2026', 'Jane Augustino', '1995-03-23', 'Female', 'Secondary', 'Makumira Teachers College', 'dropped_out', NULL, 'Makumira Teachers College', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Makumira', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(124, 'FSC-01-0078-2026', 'Elieshi Lyakurwa', '1995-06-12', 'Female', 'Secondary', 'Chuo cha Ujenzi Morogoro', 'dropped_out', NULL, 'Looking for a job, Chuo cha Ujenzi Morogoro', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Morogoro', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(125, 'FSC-01-0079-2026', 'Shamimu Lyakurwa', '1995-06-12', 'Female', 'Secondary', 'Local Government Training Institute', 'dropped_out', NULL, 'Looking for a job, Local Government Training Institute', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Dodoma', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(126, 'FSC-01-0080-2026', 'Yasinta Lyakurwa', NULL, 'Female', 'Secondary', 'Dodoma Administration College', 'dropped_out', NULL, 'Finished May 2020, Looking for a job', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Dodoma', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(127, 'FSC-01-0081-2026', 'Yona Kisanga', '1987-06-25', 'Male', 'Secondary', '', 'dropped_out', NULL, 'Disabled (Behinderung)', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Manyire', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(128, 'FSC-01-0082-2026', 'Arnold Matou', NULL, 'Male', 'Secondary', 'Mwenge University', 'dropped_out', NULL, 'Looking for a job', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Moshi', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(129, 'FSC-01-0083-2026', 'Frank Zephania', '1996-08-10', 'Male', 'Secondary', 'Arusha Sila College Tourism', 'dropped_out', NULL, 'Finished July 2020 - Looking for a job, Hotel Management', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Arusha', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(130, 'FSC-01-0084-2026', 'Ismail Isaac', '1995-08-01', 'Male', 'Secondary', 'Mbeya Technical', 'dropped_out', NULL, 'Mbeya Technical', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Mbeya', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(131, 'FSC-01-0085-2026', 'Halima Ally', '1995-06-18', 'Female', 'Secondary', 'Arusha Technical College', 'dropped_out', NULL, 'Looking for a job (lab technician)', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Arusha', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(132, 'FSC-01-0086-2026', 'Steven Williamson', '2002-07-04', 'Male', 'Secondary', '', 'dropped_out', NULL, 'Finished form IV, waiting for results', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Kikwe', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(133, 'FSC-01-0087-2026', 'Gifti Abrahamu', '2004-12-08', 'Female', 'Secondary', '', 'dropped_out', NULL, 'Out of project due to agreement breach', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Nganana', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(134, 'FSC-01-0088-2026', 'Ezekiel Selemani', '2000-12-26', 'Male', 'Secondary', 'Moshi vudoi', 'dropped_out', NULL, 'Moshi vudoi', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Nganana', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(135, 'FSC-01-0089-2026', 'Grace Kaleb', '2002-08-19', 'Female', 'Secondary', 'CDTI Tengeru', 'dropped_out', NULL, 'CDTI Tengeru', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Uraki', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(136, 'FSC-01-0090-2026', 'Jashira Marcell', '2002-08-30', 'Female', 'Secondary', 'Nangwa VCT', 'dropped_out', NULL, 'Nangwa VCT', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Nganana', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(137, 'FSC-01-0091-2026', 'Binuru Saidi', NULL, 'Female', 'Secondary', 'CDTI Tengeru', 'dropped_out', NULL, 'CDTI Tengeru', NULL, '2026-07-02 11:15:06', '2026-07-02 11:15:06', 'Dar es Salaam', '', 'Out', '', '', '', NULL, NULL, NULL, NULL, NULL),
(138, 'FSC-01-0092-2026', 'Rizic Malaki', '0000-00-00', 'Male', 'Primary', '', 'active', NULL, '', NULL, '2026-07-02 12:02:30', '2026-07-02 12:02:30', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `beneficiary_sponsors`
--

CREATE TABLE `beneficiary_sponsors` (
  `beneficiary_id` int(11) NOT NULL,
  `sponsor_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beneficiary_sponsors`
--

INSERT INTO `beneficiary_sponsors` (`beneficiary_id`, `sponsor_id`, `assigned_at`) VALUES
(2, 3, '2026-06-22 15:58:43'),
(3, 3, '2026-07-13 08:15:05'),
(3, 9, '2026-06-29 09:10:10'),
(4, 10, '2026-06-22 15:58:43'),
(5, 12, '2026-06-22 15:58:43'),
(9, 13, '2026-06-22 15:58:43'),
(10, 14, '2026-06-22 15:58:43'),
(11, 15, '2026-06-22 15:58:43'),
(12, 16, '2026-06-22 15:58:43'),
(13, 17, '2026-06-22 15:58:43'),
(14, 18, '2026-06-22 15:58:43'),
(15, 20, '2026-06-22 15:58:43'),
(16, 21, '2026-06-22 15:58:43'),
(17, 21, '2026-06-22 15:58:43'),
(18, 22, '2026-06-22 15:58:43'),
(19, 23, '2026-06-22 15:58:43'),
(21, 25, '2026-06-22 15:58:43'),
(23, 30, '2026-06-22 15:58:43'),
(24, 31, '2026-06-22 15:58:43'),
(25, 32, '2026-06-22 15:58:43'),
(26, 33, '2026-06-22 15:58:43'),
(27, 34, '2026-06-22 15:58:43'),
(28, 35, '2026-06-22 15:58:43'),
(29, 38, '2026-06-22 15:58:43'),
(30, 39, '2026-06-22 15:58:43'),
(31, 40, '2026-06-22 15:58:43'),
(32, 42, '2026-06-22 15:58:43'),
(33, 43, '2026-06-22 15:58:43'),
(34, 3, '2026-07-02 12:07:28'),
(34, 45, '2026-07-02 12:07:28'),
(35, 46, '2026-06-22 15:58:43'),
(36, 49, '2026-06-22 15:58:43'),
(37, 50, '2026-06-22 15:58:43'),
(38, 52, '2026-06-22 15:58:43'),
(39, 55, '2026-06-22 15:58:43'),
(40, 56, '2026-06-22 15:58:43'),
(42, 58, '2026-06-22 15:58:43'),
(43, 59, '2026-06-22 15:58:43'),
(44, 49, '2026-06-22 15:58:43'),
(45, 60, '2026-06-22 15:58:43'),
(46, 61, '2026-06-22 15:58:43');

-- --------------------------------------------------------

--
-- Table structure for table `finance_records`
--

CREATE TABLE `finance_records` (
  `id` int(11) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finance_records`
--

INSERT INTO `finance_records` (`id`, `type`, `amount`, `description`, `date`, `project_id`, `recorded_by`, `created_at`) VALUES
(1, 'expense', 100.01, 'sch;;l uni', '2026-04-07', NULL, 1, '2026-04-07 13:53:29'),
(2, 'income', 200.00, 'happ', '2026-04-07', NULL, 1, '2026-04-07 13:53:56');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text DEFAULT NULL,
  `media_url` varchar(255) DEFAULT NULL,
  `media_type` enum('image','video','none') DEFAULT 'none',
  `extra_media_1` varchar(255) DEFAULT NULL,
  `extra_media_2` varchar(255) DEFAULT NULL,
  `attachment_url` varchar(255) DEFAULT NULL,
  `attachment_name` varchar(150) DEFAULT NULL,
  `published_date` date DEFAULT NULL,
  `status` enum('published','draft') NOT NULL DEFAULT 'published',
  `category` varchar(100) DEFAULT 'General',
  `author` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `media_url`, `media_type`, `extra_media_1`, `extra_media_2`, `attachment_url`, `attachment_name`, `published_date`, `status`, `category`, `author`, `created_at`) VALUES
(1, 'Nambala School Renovation Completed', 'We are thrilled to announce the successful completion of the renovation of 3 classrooms at Nambala Primary School. The renovations included new roofing, fresh paint, new furniture, and proper sanitation facilities. This was made possible through the generous support of our donors. The renovation has directly benefited over 120 students who now learn in a safe, comfortable environment.', '/frisucode_ms/public/assets/uploads/news/1775722873_FRISUCODE_Founder-05.jpg', 'image', NULL, NULL, NULL, NULL, '2026-02-01', 'published', 'Education', 'FRISUCODE Team', '2026-04-03 07:16:15'),
(2, 'Hygiene and Health Workshop in Kikwe Village', 'FRISUCODE conducted a comprehensive hygiene and health workshop in Kikwe village, training over 50 families on clean water practices, basic hygiene, and disease prevention. The workshop was led by healthcare professionals and covered topics including handwashing, water purification, and nutritional guidance for children. Families received hygiene kits to continue practicing the skills learned.', '/frisucode_ms/public/assets/uploads/news/1775722852_FRISUCODE_Founder-05.jpg', 'image', NULL, NULL, NULL, NULL, '2026-01-25', 'published', 'Health', 'Dr. Amina Juma', '2026-04-03 07:16:15'),
(3, '5 Sponsored Students Graduate from Arusha Vocational College', 'A proud milestone for FRISUCODE! Five of our sponsored students have successfully graduated from Arusha Vocational College with diplomas in computer science, tailoring, plumbing, electrical engineering, and catering. These graduates represent the transformative power of education sponsorship and will now serve as role models for younger beneficiaries in their communities.', '/frisucode_ms/public/assets/uploads/news/1775337448_Wema_Wako_717854237_16_9_t5_free.mp4', 'video', NULL, NULL, NULL, 'Certificate Fri-SUCODE (3).pdf', '2026-01-10', 'published', 'Education', 'FRISUCODE Team', '2026-04-03 07:16:15');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT 0.00,
  `status` enum('planning','active','completed','cancelled') DEFAULT 'planning',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `start_date`, `end_date`, `budget`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Nambala Chiken Renovation', 'goals', '2026-04-03', '2026-04-24', 4000.00, 'active', 1, '2026-04-03 07:36:39', '2026-04-03 07:36:39'),
(2, 'Karangai Primary schooll renovation', '', '2026-06-24', '2027-01-04', 20000.00, 'active', 5, '2026-06-24 10:01:15', '2026-06-24 10:01:15'),
(3, 'VOP', 'Vocation orientation Programs', '2026-06-24', NULL, 400000.00, 'active', 5, '2026-06-24 10:01:48', '2026-06-24 10:01:48');

-- --------------------------------------------------------

--
-- Table structure for table `public_donations`
--

CREATE TABLE `public_donations` (
  `id` int(11) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `frequency` enum('once','monthly') DEFAULT 'once',
  `cause` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

CREATE TABLE `sponsors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `organization_name` varchar(200) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `sponsor_type` enum('individual','organization','government') DEFAULT 'individual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sponsors`
--

INSERT INTO `sponsors` (`id`, `user_id`, `organization_name`, `phone`, `address`, `sponsor_type`) VALUES
(1, 9, 'Nature Friends', '0756649579', NULL, 'organization'),
(2, 3, 'Simone Stodal', NULL, NULL, 'individual'),
(3, 10, 'Katharina Tennert', NULL, NULL, 'individual'),
(4, 11, 'Julia Prein', NULL, NULL, 'individual'),
(5, 12, 'Mirko Höfler', NULL, NULL, 'individual'),
(6, 13, 'Steffi und Marie Leitl', NULL, NULL, 'individual'),
(7, 14, 'Carmen und Klaus Leupold', NULL, NULL, 'individual'),
(8, 15, 'Ramona Starke', NULL, NULL, 'individual'),
(9, 16, 'Gildis Behrendt', NULL, NULL, 'individual'),
(10, 17, 'Marita Bullmann', NULL, NULL, 'individual'),
(11, 18, 'Daniel und Janine Flöck', NULL, NULL, 'individual'),
(12, 19, 'Detlef und Manuela Stich', NULL, NULL, 'individual'),
(13, 20, 'Jürgen Möller', NULL, NULL, 'individual'),
(14, 21, 'Michaela Berthold', NULL, NULL, 'individual'),
(15, 22, 'Barbara Tarnick', NULL, NULL, 'individual'),
(16, 23, 'Frank Antefuhr', NULL, NULL, 'individual'),
(17, 24, 'Wolfgang Langer', NULL, NULL, 'individual'),
(18, 25, 'Heike Platz', NULL, NULL, 'individual'),
(19, 26, 'Simone Leube', NULL, NULL, 'individual'),
(20, 27, 'Anette Fischer', NULL, NULL, 'individual'),
(21, 28, 'Kati Beier', NULL, NULL, 'individual'),
(22, 29, 'Kristin Koebe', NULL, NULL, 'individual'),
(23, 30, 'Dietmar und Brigitte Lehmann', NULL, NULL, 'individual'),
(24, 31, 'Angelika Kipp', NULL, NULL, 'individual'),
(25, 32, 'Jana und Sven Steinhaus', NULL, NULL, 'individual'),
(26, 33, 'Thorsten Rosenau', NULL, NULL, 'individual'),
(27, 34, 'Thomas Weist', NULL, NULL, 'individual'),
(28, 35, 'Silke Lübke', NULL, NULL, 'individual'),
(29, 36, 'Rita und Klaus Neumann', NULL, NULL, 'individual'),
(30, 37, 'Mirella Dieckmann', NULL, NULL, 'individual'),
(31, 38, 'Maria und Jacob Malmros', NULL, NULL, 'individual'),
(32, 39, 'Sybille Ott (Yassin)', NULL, NULL, 'individual'),
(33, 40, 'Sybille Ott', NULL, NULL, 'individual'),
(34, 41, 'Anna und Bernhard Ullrich', NULL, NULL, 'individual'),
(35, 42, 'Susanne und Michael Herrmann', NULL, NULL, 'individual'),
(36, 43, 'Veronika Markstein', NULL, NULL, 'individual'),
(37, 44, 'Reinhard Hevekerl', NULL, NULL, 'individual'),
(38, 45, 'Konstanze Bajerski', NULL, NULL, 'individual'),
(39, 46, 'Betty Schmidt', NULL, NULL, 'individual'),
(40, 47, 'Christine Schmitt', NULL, NULL, 'individual'),
(41, 48, 'Hendrick und Petra Lücke', NULL, NULL, 'individual'),
(42, 49, 'Almut Thomas und Helmut Stier', NULL, NULL, 'individual'),
(43, 50, 'Regine Zimmerer', NULL, NULL, 'individual'),
(44, 51, 'Wolfram Holl', NULL, NULL, 'individual'),
(45, 52, 'Dr. Rainer Heinrich', NULL, NULL, 'individual'),
(46, 53, 'Kai Hoppe', NULL, NULL, 'individual'),
(47, 54, 'Thomas Lindner', NULL, NULL, 'individual'),
(48, 55, 'Olaf und Karin Köslich', NULL, NULL, 'individual'),
(49, 56, 'Detlef und Astrid Apel', NULL, NULL, 'individual'),
(50, 57, 'Thomas Scholz', NULL, NULL, 'individual'),
(51, 58, 'Sybille Ott (Luca)', NULL, NULL, 'individual'),
(52, 59, 'Tobias Lehmann', NULL, NULL, 'individual'),
(53, 60, 'Michael Bunkenburg', NULL, NULL, 'individual'),
(54, 61, 'Carola Wolf und Freunde', NULL, NULL, 'individual');

-- --------------------------------------------------------

--
-- Table structure for table `student_reports`
--

CREATE TABLE `student_reports` (
  `id` int(11) NOT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `report_text` text DEFAULT NULL,
  `file_url` varchar(500) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `report_date` date NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_reports`
--

INSERT INTO `student_reports` (`id`, `beneficiary_id`, `title`, `report_text`, `file_url`, `file_name`, `report_date`, `created_by`, `created_at`) VALUES
(1, 34, 'Term 1 Academin progress', 'done real good with your leeter betwwen', '/frisucode_ms/assets/uploads/student_reports/1782724046_FRISUCODE_VOP_AI_Integration_Proposal.pdf', 'FRISUCODE_VOP_AI_Integration_Proposal.pdf', '2026-06-29', 5, '2026-06-29 09:07:26');

-- --------------------------------------------------------

--
-- Table structure for table `system_stats`
--

CREATE TABLE `system_stats` (
  `id` int(11) NOT NULL,
  `students_base` int(11) DEFAULT 1200,
  `retention_base` int(11) DEFAULT 95,
  `schools_base` int(11) DEFAULT 24,
  `families_base` int(11) DEFAULT 400,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_stats`
--

INSERT INTO `system_stats` (`id`, `students_base`, `retention_base`, `schools_base`, `families_base`, `updated_at`) VALUES
(1, 1200, 95, 24, 400, '2026-03-05 14:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','director','project_manager','finance','me_officer','field_officer','donor','guest') NOT NULL DEFAULT 'guest',
  `status` enum('active','inactive') DEFAULT 'active',
  `preferred_language` enum('en','sw','de','fr','es') DEFAULT 'en',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `profile_picture`, `phone`, `address`, `password`, `role`, `status`, `preferred_language`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'System Administrator', 'admin@frisucode.org', NULL, NULL, NULL, '$2y$10$ITGUGmOXdFCWA/7EyoBfiON.JoMLVMm2kHLIwY8YFD5KoZMe/vtem', 'super_admin', 'active', 'en', NULL, '2025-12-24 09:58:40', '2025-12-30 10:46:23'),
(3, 'Simone Stodal', 'simone@frisucode.org', NULL, NULL, NULL, '$2y$10$i7vZ8T5ToDp8z7iAcD9mlOcVCcILuwKWaBtF7KVPeV50EsfYavKyu', 'donor', 'active', 'en', NULL, '2026-03-05 11:56:40', '2026-03-05 11:56:40'),
(5, 'Baraka Mshana', 'baraka@frisucode.org', '/frisucode_ms/public/assets/uploads/profiles/profile_5_1782296771.png', '', '', '$2y$10$LnFB1c1nFfcP4yiM7bxyVutJtkbzpO1YYIRU1Qnxa/n2FhyfGhiSa', 'director', 'active', 'en', NULL, '2026-03-05 14:33:39', '2026-06-24 10:26:11'),
(6, 'Happy', 'happy@frisucode.org', NULL, NULL, NULL, '$2y$10$/ayqjcVq7sd9xy.JD.QXhO0CB9fYR9jKriO6uNOjwAHOREylDffuu', 'project_manager', 'active', 'en', NULL, '2026-03-05 14:34:08', '2026-03-05 14:34:08'),
(8, 'finance', 'fiance@frisucode.org', NULL, NULL, NULL, '$2y$10$4eL3/dJzvq8HhIaRnvnb9u7RfKiIm.2rg9lSXxJOmFYrfmcPQAGgG', 'finance', 'active', 'en', NULL, '2026-03-26 08:58:30', '2026-03-26 08:58:30'),
(9, 'Almut', 'nature@gmai.com', NULL, NULL, NULL, '$2y$10$ijMtHvtKcYmiSvE6S4ybNuCXJ1xInTlsk9rONWEuk.mLWsIGSmMNm', 'donor', 'active', 'en', NULL, '2026-04-03 07:38:19', '2026-04-03 07:38:19'),
(10, 'Katharina Tennert', 'katharina.tennert@example.com', NULL, NULL, NULL, '$2y$10$6dEVf6wcvlm.svkWst5ceeJ4E1PTlPbrI.WcAwWoioGO3KxMyDOfG', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:49', '2026-06-22 12:00:49'),
(11, 'Julia Prein', 'julia.prein@example.com', NULL, NULL, NULL, '$2y$10$mS0jp26qW2mUA7kNjoLEBOtT.vdRif.bgNGUMpwX1DyHt7uyHBxpO', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:50', '2026-06-22 12:00:50'),
(12, 'Mirko Höfler', 'mirko.hfler@example.com', NULL, NULL, NULL, '$2y$10$85NyN/2naupXv7txfYH1vOK5j.6YLjroaHYh8A7YqifvsVA88Esoq', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:50', '2026-06-22 12:00:50'),
(13, 'Steffi und Marie Leitl', 'steffi.und.marie.leitl@example.com', NULL, NULL, NULL, '$2y$10$TrIvUgcQFYBZQ4bUgGeYu.ctD5jP5zwaoM2phBuipT6G9QbPKO1qe', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:50', '2026-06-22 12:00:50'),
(14, 'Carmen und Klaus Leupold', 'carmen.und.klaus.leupold@example.com', NULL, NULL, NULL, '$2y$10$aBXI3pbkZSNYkHx5MBb9tOyJP4Yv6PTHYlSPU8G/eTDpohWbeyYYC', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:50', '2026-06-22 12:00:50'),
(15, 'Ramona Starke', 'ramona.starke@example.com', NULL, NULL, NULL, '$2y$10$3Brd4UE3NP8pRLdZXXtfa.s.NHoPA67rrpcP9CKyvsXDQQGwVSafi', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:50', '2026-06-22 12:00:50'),
(16, 'Gildis Behrendt', 'gildis.behrendt@example.com', NULL, NULL, NULL, '$2y$10$CKQnh62aQJ2UKYTfE.G3SOjfHrTZNShKI5/9PWkUwUIJ7UVWmE0hq', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:50', '2026-06-22 12:00:50'),
(17, 'Marita Bullmann', 'marita.bullmann@example.com', NULL, NULL, NULL, '$2y$10$nugcjIUL9HxOVMxpGLnVmOMx4oqrrc/swY3pQnx0L5bXzfqxOfrUq', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:50', '2026-06-22 12:00:50'),
(18, 'Daniel und Janine Flöck', 'daniel.und.janine.flck@example.com', NULL, NULL, NULL, '$2y$10$2Xj7qXPTdDBRM7arPOVauefvDQxZpsdOMAJ.gqvmPIl4H7PFjQfoK', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:50', '2026-06-22 12:00:50'),
(19, 'Detlef und Manuela Stich', 'detlef.und.manuela.stich@example.com', NULL, NULL, NULL, '$2y$10$2JDzzjIKBF40FWJwBXxBdOcr0rQcBHoktp7SjpUwVtHE38/StKHSG', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:50', '2026-06-22 12:00:50'),
(20, 'Jürgen Möller', 'jrgen.mller@example.com', NULL, NULL, NULL, '$2y$10$Um.FrcpuZ3t4Gt1sQ3XNqeVdk5QU6c80cAIsdnn2Q1fn4IFPGXOMm', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:51', '2026-06-22 12:00:51'),
(21, 'Michaela Berthold', 'michaela.berthold@example.com', NULL, NULL, NULL, '$2y$10$sdDFVS6FeZ2/79.rLMOpJu8ck5h62Zel8aAIRkTCgEu0O3CuIDVGO', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:51', '2026-06-22 12:00:51'),
(22, 'Barbara Tarnick', 'barbara.tarnick@example.com', NULL, NULL, NULL, '$2y$10$Zb45crGKZvwkdUBZSS580OIb3h11Av8qQ4yQtuVTm0yzM5uhLALMG', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:51', '2026-06-22 12:00:51'),
(23, 'Frank Antefuhr', 'frank.antefuhr@example.com', NULL, NULL, NULL, '$2y$10$mRK959JLSHFMAlsy2UV8GuaJmssiZf57ceMRWFXyRtek50jGm0.KS', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:51', '2026-06-22 12:00:51'),
(24, 'Wolfgang Langer', 'wolfgang.langer@example.com', NULL, NULL, NULL, '$2y$10$zPo8RVls8AiZk564yecHxuijyKdVUzi20a6x8JFdEeocKoFU.6r7m', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:51', '2026-06-22 12:00:51'),
(25, 'Heike Platz', 'heike.platz@example.com', NULL, NULL, NULL, '$2y$10$F/Mk2cClGdPN5na7DVGDmOOYSP/yKhdasCug0pPamSL01FsrSp4z.', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:51', '2026-06-22 12:00:51'),
(26, 'Simone Leube', 'simone.leube@example.com', NULL, NULL, NULL, '$2y$10$lY7c1Afr.9oj/QZNHqtOfu9OFUOn5XULueCVT8yW7FTOO/vse8Hg2', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:51', '2026-06-22 12:00:51'),
(27, 'Anette Fischer', 'anette.fischer@example.com', NULL, NULL, NULL, '$2y$10$BcRYF8NwE.ZlwvACm.dZf.doVPk0QBjWL2kV/HEgXiLY3f5yN78Bm', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:51', '2026-06-22 12:00:51'),
(28, 'Kati Beier', 'kati.beier@example.com', NULL, NULL, NULL, '$2y$10$9.oqeSfZDCSU0DU0kULkaudafcVXiumbEBwrFcIbq/mA5H7DZ2WDu', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:51', '2026-06-22 12:00:51'),
(29, 'Kristin Koebe', 'kristin.koebe@example.com', NULL, NULL, NULL, '$2y$10$uOnBnVPrHTyHZ4LlnpHl8eKcBBlaWVk1lVw5k6kWfBqFfKJVSqO16', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:52', '2026-06-22 12:00:52'),
(30, 'Dietmar und Brigitte Lehmann', 'dietmar.und.brigitte.lehmann@example.com', NULL, NULL, NULL, '$2y$10$mc88.lU2VR57MLnXjC7ZqeLQTvWNPbsdxUAnN4/UQOBnZeamjyczO', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:52', '2026-06-22 12:00:52'),
(31, 'Angelika Kipp', 'angelika.kipp@example.com', NULL, NULL, NULL, '$2y$10$ns2Ah5.uabiyl/IZhvVr6eoquDTMejiP241KQu9HNi4ENyy826EBu', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:52', '2026-06-22 12:00:52'),
(32, 'Jana und Sven Steinhaus', 'jana.und.sven.steinhaus@example.com', NULL, NULL, NULL, '$2y$10$5zISMFVHRIztZZ3FROWC5OG5k7eEg2Qa95wtc3Jz4XPc0R1xIHMYm', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:52', '2026-06-22 12:00:52'),
(33, 'Thorsten Rosenau', 'thorsten.rosenau@example.com', NULL, NULL, NULL, '$2y$10$Kj6Vyhbgu0CmgasB2YvGuu1ArfeuZsNr6TsoaY5.wXFN.ZI3X/nLi', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:52', '2026-06-22 12:00:52'),
(34, 'Thomas Weist', 'thomas.weist@example.com', NULL, NULL, NULL, '$2y$10$pvTT7Iuhg829n/phVoXB0uFFMUs7WWDJD7su51kE8npuj3qXlXJXO', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:52', '2026-06-22 12:00:52'),
(35, 'Silke Lübke', 'silke.lbke@example.com', NULL, NULL, NULL, '$2y$10$tv.cMrwv2XXGo3cYNBjTIOLvSQ.kitjz.0nlTebEVBHH2C2fjh2eK', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:52', '2026-06-22 12:00:52'),
(36, 'Rita und Klaus Neumann', 'rita.und.klaus.neumann@example.com', NULL, NULL, NULL, '$2y$10$xCWUHrQvVz1dUW5S4nddZuRdddU/MnbmYL/euyKJQCnr1BnSZRL/m', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:52', '2026-06-22 12:00:52'),
(37, 'Mirella Dieckmann', 'mirella.dieckmann@example.com', NULL, NULL, NULL, '$2y$10$snmOJdfzCEXqBlmDCeafKuPhtuQKHfcW0QD40EBvGeVKGMCroj8vG', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:52', '2026-06-22 12:00:52'),
(38, 'Maria und Jacob Malmros', 'maria.und.jacob.malmros@example.com', NULL, NULL, NULL, '$2y$10$IsmJ6L5IqMKO9keLlqakDuAwOZLaaFEXScFPXJ48W1UVCFspK8tqu', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:52', '2026-06-22 12:00:52'),
(39, 'Sybille Ott (Yassin)', 'sybille.ott.yassin@example.com', NULL, NULL, NULL, '$2y$10$/ZUjnBrQ.hQK.KVAndq8h.XUZ1eHomJZwMGVG1J/xPHqRO1WvXQOq', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:53', '2026-06-22 12:00:53'),
(40, 'Sybille Ott', 'sybille.ott@example.com', NULL, NULL, NULL, '$2y$10$HmX47E5X1Rbbp/7Bwmtz/OnDax/Ors2YVRDDqqDOXwd8qdigmdVGG', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:53', '2026-06-22 12:00:53'),
(41, 'Anna und Bernhard Ullrich', 'anna.und.bernhard.ullrich@example.com', NULL, NULL, NULL, '$2y$10$ypY09/1hHwxaojIXQQbHsuY/w8om5Wt3rP9jn7rEoniBKHw8M8uBm', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:53', '2026-06-22 12:00:53'),
(42, 'Susanne und Michael Herrmann', 'susanne.und.michael.herrmann@example.com', NULL, NULL, NULL, '$2y$10$yvHrrOSfavQ0untbKcYcJ.TY1FaBJAPkr4BJkncg4bPSZAkSZYElm', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:53', '2026-06-22 12:00:53'),
(43, 'Veronika Markstein', 'veronika.markstein@example.com', NULL, NULL, NULL, '$2y$10$zUeRX7C.hTVm7YA1ceQ58OpWkrWGPdFZ6ZQ.TulnPs7b3rLXta4Sm', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:53', '2026-06-22 12:00:53'),
(44, 'Reinhard Hevekerl', 'reinhard.hevekerl@example.com', NULL, NULL, NULL, '$2y$10$zLpOfsB/khABvXbpybe1U.vQPI/1M8XQKBnaLVvn62MYhXR9MLV76', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:53', '2026-06-22 12:00:53'),
(45, 'Konstanze Bajerski', 'konstanze.bajerski@example.com', NULL, NULL, NULL, '$2y$10$q4F60pKolR2Uk7DDCoNA2e/gSSnjv8BeFUULQbHMZOF7RhI1J5iLq', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:53', '2026-06-22 12:00:53'),
(46, 'Betty Schmidt', 'betty.schmidt@example.com', NULL, NULL, NULL, '$2y$10$6BksVwDJ8pAqP3U3KDEBuejcl7luFloWAN5V/s7RdkyjspidhNUUW', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:53', '2026-06-22 12:00:53'),
(47, 'Christine Schmitt', 'christine.schmitt@example.com', NULL, NULL, NULL, '$2y$10$OOp8AeHqyp/ixeBorRsL7uGDcqOVIv27/RRAtsRFFNPZ0mbau8b2q', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:53', '2026-06-22 12:00:53'),
(48, 'Hendrick und Petra Lücke', 'hendrick.und.petra.lcke@example.com', NULL, NULL, NULL, '$2y$10$GC169FSSycgOdvQ9jey37ep.sqzKS8v6kGFsAkszzNPeTN.PevBja', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:54', '2026-06-22 12:00:54'),
(49, 'Almut Thomas und Helmut Stier', 'almut.thomas.und.helmut.stier@example.com', NULL, NULL, NULL, '$2y$10$2r9CBCxvg4NjzsqZ8.vu8.oVCwDmKvU4QIESLVbox5GMpdJW0KwO6', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:54', '2026-06-22 12:00:54'),
(50, 'Regine Zimmerer', 'regine.zimmerer@example.com', NULL, NULL, NULL, '$2y$10$2lP5Ppi3wHYnuf2ZpoV.iu31OT/9/neTPSsI17EG1EvL8Zac7Rhhq', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:54', '2026-06-22 12:00:54'),
(51, 'Wolfram Holl', 'wolfram.holl@example.com', NULL, NULL, NULL, '$2y$10$vInoar8DQzzLIXu3u/p/3.cY3smlxLFtATc2KMhV9gkhkfPpZmUhG', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:54', '2026-06-22 12:00:54'),
(52, 'Dr. Rainer Heinrich', 'dr.rainer.heinrich@example.com', NULL, NULL, NULL, '$2y$10$HaV4YK3WErxRAXkxp7IB5OtygPDKn9Ll4SRTtj8noI4bRGF3sONZ6', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:54', '2026-06-22 12:00:54'),
(53, 'Kai Hoppe', 'kai.hoppe@example.com', NULL, NULL, NULL, '$2y$10$eiE0Cr2jaGYwo3ZmvpxBputy1x8JahPv0Yq5gvdLbz3i7GkqTmC5G', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:54', '2026-06-22 12:00:54'),
(54, 'Thomas Lindner', 'thomas.lindner@example.com', NULL, NULL, NULL, '$2y$10$5XjfqfnYETMDPru.MvhP4e09FH5jdDm87RmmWcuaqy.3Mwplj9hmW', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:54', '2026-06-22 12:00:54'),
(55, 'Olaf und Karin Köslich', 'olaf.und.karin.kslich@example.com', NULL, NULL, NULL, '$2y$10$ycUR7Pat0mO60yhkASizbul8MCOWBsnroab5qRq7V791Y9sMLTGgK', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:54', '2026-06-22 12:00:54'),
(56, 'Detlef und Astrid Apel', 'detlef.und.astrid.apel@example.com', NULL, NULL, NULL, '$2y$10$qA22FwpmxfcM4d0yxubFle79Q.f9QX3yEWbEvxTZU2RURzXz96Fsu', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:54', '2026-06-22 12:00:54'),
(57, 'Thomas Scholz', 'thomas.scholz@example.com', NULL, NULL, NULL, '$2y$10$dYn978/WIS3bx/XdL6Fej.Pzd4thCD/C.iwn.3xfaxEGDb9g955Me', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:55', '2026-06-22 12:00:55'),
(58, 'Sybille Ott (Luca)', 'sybille.ott.luca@example.com', NULL, NULL, NULL, '$2y$10$I6JXFDnG9Hnf42EGkAA9VeF21udNFEP65.LbYR2qAEDVXfMac2jqe', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:55', '2026-06-22 12:00:55'),
(59, 'Tobias Lehmann', 'tobias.lehmann@example.com', NULL, NULL, NULL, '$2y$10$jIZds6qaUFqXoLdnyqUQ3eTuQ/FvTWWA7a3rLySSPMjl7vjxwMkKy', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:55', '2026-06-22 12:00:55'),
(60, 'Michael Bunkenburg', 'michael.bunkenburg@example.com', NULL, NULL, NULL, '$2y$10$Hwl0jR3sZzohjQCsTyGqeeUlkFPy7h4/euIdq3xwT67/noxw1Wx1O', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:55', '2026-06-22 12:00:55'),
(61, 'Carola Wolf und Freunde', 'carola.wolf.und.freunde@example.com', NULL, NULL, NULL, '$2y$10$gLOENsnNaFSqF4HU9QZbF.0ErFw05br8DzRCQ7t//Bpycfs3E9DDW', 'donor', 'active', 'en', NULL, '2026-06-22 12:00:55', '2026-06-22 12:00:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `beneficiary_sponsors`
--
ALTER TABLE `beneficiary_sponsors`
  ADD PRIMARY KEY (`beneficiary_id`,`sponsor_id`);

--
-- Indexes for table `finance_records`
--
ALTER TABLE `finance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `public_donations`
--
ALTER TABLE `public_donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `student_reports`
--
ALTER TABLE `student_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beneficiary_id` (`beneficiary_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `system_stats`
--
ALTER TABLE `system_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `finance_records`
--
ALTER TABLE `finance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `public_donations`
--
ALTER TABLE `public_donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sponsors`
--
ALTER TABLE `sponsors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `student_reports`
--
ALTER TABLE `student_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `system_stats`
--
ALTER TABLE `system_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `finance_records`
--
ALTER TABLE `finance_records`
  ADD CONSTRAINT `finance_records_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `finance_records_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sponsors`
--
ALTER TABLE `sponsors`
  ADD CONSTRAINT `sponsors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_reports`
--
ALTER TABLE `student_reports`
  ADD CONSTRAINT `student_reports_ibfk_1` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_reports_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
