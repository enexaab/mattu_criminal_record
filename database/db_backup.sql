-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 08, 2025 at 12:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mattu_crm_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action_type`, `description`, `target_id`, `target_type`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 8, 'user_create', 'Created user: jason', NULL, NULL, NULL, NULL, '2025-10-22 08:08:29'),
(2, 8, 'user_create', 'Created user: brandon', NULL, NULL, NULL, NULL, '2025-10-22 08:29:11'),
(3, 8, 'user_create', 'Created user: akanji', NULL, NULL, NULL, NULL, '2025-11-03 10:00:03'),
(4, 8, 'user_create', 'Created user: philp', NULL, NULL, NULL, NULL, '2025-11-03 12:25:34'),
(5, 8, 'user_create', 'Created user: ALEX', NULL, NULL, NULL, NULL, '2025-11-03 14:38:49'),
(6, 8, 'user_create', 'Created user: william', NULL, NULL, NULL, NULL, '2025-11-05 07:19:12'),
(7, 8, 'user_status', 'Changed status for user ID: 20 to inactive', NULL, NULL, NULL, NULL, '2025-11-05 07:20:10'),
(8, 8, 'user_status', 'Changed status for user ID: 20 to active', NULL, NULL, NULL, NULL, '2025-11-05 07:20:22'),
(9, 8, 'user_update', 'Updated user: william', NULL, NULL, NULL, NULL, '2025-11-05 07:20:42'),
(10, 8, 'password_reset', 'Reset password for user ID: 20', NULL, NULL, NULL, NULL, '2025-11-05 07:20:59'),
(11, 16, 'password_reset', 'Reset password for user ID: 8', NULL, NULL, NULL, NULL, '2025-11-05 07:22:47'),
(12, 8, 'user_update', 'Updated user: william', NULL, NULL, NULL, NULL, '2025-11-05 08:09:39'),
(13, 8, 'user_status', 'Changed status for user ID: 20 to inactive', NULL, NULL, NULL, NULL, '2025-11-05 08:11:20'),
(14, 8, 'user_create', 'Created user: manuel', NULL, NULL, NULL, NULL, '2025-11-07 08:55:18'),
(15, 8, 'user_create', 'Created user: bamlak', NULL, NULL, NULL, NULL, '2025-11-07 08:56:55'),
(16, 17, 'user_create', 'Created user: dfdsfdsFD', NULL, NULL, NULL, NULL, '2025-11-07 09:09:19'),
(17, 9, 'user_create', 'Created user: fdgzdfg', NULL, NULL, NULL, NULL, '2025-11-07 09:18:04'),
(18, 8, 'user_create', 'Created user: fdgg', NULL, NULL, NULL, NULL, '2025-11-07 09:31:44'),
(19, 8, 'user_create', 'Created user: dsfsdfdsf', NULL, NULL, NULL, NULL, '2025-11-07 09:32:14'),
(20, 8, 'user_create', 'Created user: jjj', NULL, NULL, NULL, NULL, '2025-11-08 11:38:49');

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `id` int(11) NOT NULL,
  `case_number` varchar(50) NOT NULL,
  `case_type` varchar(100) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date_reported` date NOT NULL,
  `status` enum('Open','In Progress','In Court','Closed','Suspended') DEFAULT 'Open',
  `priority` varchar(20) DEFAULT 'medium',
  `severity` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `assigned_officer_id` int(11) DEFAULT NULL,
  `lead_officer_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cases`
--

INSERT INTO `cases` (`id`, `case_number`, `case_type`, `title`, `description`, `location`, `date_reported`, `status`, `priority`, `severity`, `assigned_officer_id`, `lead_officer_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CASE-20251103-1110', 'Theft', 'Drug Offense Case - CASE-20251103-1110', 'ghgfgsfgs', 'Round Rock, TX', '2025-11-03', 'Closed', 'low', 'Medium', 17, 17, 9, '2025-11-03 08:14:24', '2025-11-04 08:25:23'),
(2, 'CASE-20251104-1043', 'Robbery', 'Robbery Case - CASE-20251104-1043', 'dstrgs', 'Round Rock, TX', '2025-11-04', 'In Progress', 'high', 'Medium', 15, 15, 15, '2025-11-04 07:43:53', '2025-11-04 07:48:27'),
(3, 'CASE-20251104-1050', 'Domestic Violence', 'Domestic Violence Case - CASE-20251104-1050', '', 'San Francisco, CA, USA', '2025-11-04', 'Open', 'medium', 'Medium', 15, 15, 15, '2025-11-04 07:50:22', '2025-11-04 07:50:22'),
(4, 'CASE-20251106-1301', 'Fraud', 'Fraud Case - CASE-20251106-1301', 'gfgfg', 'San Francesco, Solofra, Avellino, ITA', '2025-11-06', 'Open', 'medium', 'Medium', 9, 9, 9, '2025-11-06 10:02:15', '2025-11-08 05:35:49'),
(5, 'CASE-20251108-1129', 'Fraud', 'Fraud Case - CASE-20251108-1129', 'dfdsf', 'San Francesco, Solofra, Avellino, ITA', '2025-11-08', 'Open', 'medium', 'Medium', 9, 9, 9, '2025-11-08 08:29:30', '2025-11-08 08:29:30'),
(6, 'CASE-20251108-1346', 'Drug Offense', 'Drug Offense Case - CASE-20251108-1346', '', 'San Francisco, CA, USA', '2025-11-08', 'Open', 'medium', 'Medium', 9, 9, 9, '2025-11-08 10:47:06', '2025-11-08 10:47:06'),
(7, 'CASE-20251108-1348', 'Burglary', 'Burglary Case - CASE-20251108-1348', '', 'San Francisco, CA, USA', '2025-11-08', 'Open', 'medium', 'Medium', 9, 9, 9, '2025-11-08 10:48:09', '2025-11-08 10:48:09');

-- --------------------------------------------------------

--
-- Table structure for table `case_files`
--

CREATE TABLE `case_files` (
  `id` int(11) NOT NULL,
  `case_number` varchar(50) NOT NULL,
  `case_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `date_reported` date NOT NULL,
  `status` enum('Open','In Progress','In Court','Closed','Suspended') DEFAULT 'Open',
  `priority` varchar(20) DEFAULT 'medium',
  `lead_officer_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_notes`
--

CREATE TABLE `case_notes` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `note_text` text NOT NULL,
  `note_type` enum('general','investigation','court','followup') DEFAULT 'general',
  `is_important` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `case_notes`
--

INSERT INTO `case_notes` (`id`, `case_id`, `user_id`, `note_text`, `note_type`, `is_important`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 'ngcngfn', 'investigation', 1, '2025-11-04 08:18:38', '2025-11-04 08:18:38');

-- --------------------------------------------------------

--
-- Table structure for table `case_persons`
--

CREATE TABLE `case_persons` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `record_id` int(11) NOT NULL,
  `role` enum('Suspect','Witness','Victim','Complainant') DEFAULT 'Suspect',
  `relationship_to_case` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `case_persons`
--

INSERT INTO `case_persons` (`id`, `case_id`, `record_id`, `role`, `relationship_to_case`, `added_by`, `added_at`) VALUES
(3, 1, 2, 'Suspect', NULL, 9, '2025-11-03 08:14:24'),
(4, 2, 7, 'Suspect', NULL, 15, '2025-11-04 07:43:53'),
(5, 3, 7, 'Suspect', NULL, 15, '2025-11-04 07:50:22'),
(6, 4, 8, 'Suspect', NULL, 9, '2025-11-06 10:02:15'),
(7, 5, 2, 'Suspect', NULL, 9, '2025-11-08 08:29:30'),
(8, 6, 2, 'Suspect', NULL, 9, '2025-11-08 10:47:06'),
(9, 7, 2, 'Suspect', NULL, 9, '2025-11-08 10:48:09');

-- --------------------------------------------------------

--
-- Table structure for table `case_reassignments`
--

CREATE TABLE `case_reassignments` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `previous_officer_id` int(11) DEFAULT NULL,
  `new_officer_id` int(11) NOT NULL,
  `reassigned_by` int(11) NOT NULL,
  `reason` text NOT NULL,
  `reassigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `case_reassignments`
--

INSERT INTO `case_reassignments` (`id`, `case_id`, `previous_officer_id`, `new_officer_id`, `reassigned_by`, `reason`, `reassigned_at`) VALUES
(1, 1, 9, 17, 10, 'ggg', '2025-11-03 14:54:49');

-- --------------------------------------------------------

--
-- Table structure for table `criminal_records`
--

CREATE TABLE `criminal_records` (
  `id` int(11) NOT NULL,
  `national_id` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `eye_color` varchar(30) DEFAULT NULL,
  `hair_color` varchar(30) DEFAULT NULL,
  `distinguishing_marks` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('First Offender','Repeat Offender','Wanted','In Custody') DEFAULT 'First Offender',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criminal_records`
--

INSERT INTO `criminal_records` (`id`, `national_id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `height`, `weight`, `eye_color`, `hair_color`, `distinguishing_marks`, `photo`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '1234567890123', 'John', 'Doe', '1990-05-15', 'Male', 175.50, 70.20, 'Brown', 'Black', 'Scar on left cheek', 'uploads/criminal_photos/test_photo.jpg', 'First Offender', 1, '2025-11-03 06:42:31', '2025-11-03 06:42:31'),
(2, '9876543210987', 'Jason', 'Rostro', '2001-02-06', 'Male', 175.00, 60.00, 'Brown', 'Brown', 'jjj', 'uploads/criminal_photos/criminal_1762152851_690851934604d.jpg', 'First Offender', 9, '2025-11-03 06:54:11', '2025-11-03 06:54:11'),
(3, '47575', 'Alex', 'Zhou', '2002-04-24', 'Female', 82.00, 42.00, 'Blue', 'Red', 'ggsgdfd', 'uploads/criminal_photos/criminal_1762241269_6909aaf57f16b.png', 'First Offender', 15, '2025-11-04 07:27:49', '2025-11-04 07:27:49'),
(4, '475755', 'Alex', 'Zhou', '2002-04-24', 'Female', 82.00, 42.00, 'Blue', 'Red', 'ggsgdfd', 'uploads/criminal_photos/criminal_1762241516_6909abec9a495.png', 'First Offender', 15, '2025-11-04 07:31:56', '2025-11-04 07:31:56'),
(5, '4444\\55', 'Enyew', 'Huang', '2002-02-25', 'Male', 55.00, 57.00, 'Black', 'Blonde', 'jhgfjth', 'uploads/criminal_photos/criminal_1762242047_6909adffd1aa1.png', 'First Offender', 15, '2025-11-04 07:40:47', '2025-11-04 07:40:47'),
(6, '4545\\745', 'rre', 'grg', '1954-04-22', 'Female', 78.00, 22.00, 'Brown', 'Blonde', 'hghg', 'uploads/criminal_photos/criminal_1762242118_6909ae46d7bb7.png', 'First Offender', 15, '2025-11-04 07:41:58', '2025-11-04 07:41:58'),
(7, '57557', 'Enyew', 'Rostro', '2002-05-05', 'Female', 75.00, 56.00, 'Black', 'Blonde', 'gshdfgdf', 'uploads/criminal_photos/criminal_1762242212_6909aea41020e.png', 'First Offender', 15, '2025-11-04 07:43:32', '2025-11-04 07:43:32'),
(8, '54546262323', 'Abebe', 'fdf', '2002-02-24', 'Male', 175.00, 50.00, 'Black', 'Brown', 'enfehfjehgjeghefjkgh', 'uploads/criminal_photos/criminal_1762423240_690c71c810b61.jpg', 'First Offender', 9, '2025-11-06 10:00:40', '2025-11-06 10:00:40');

-- --------------------------------------------------------

--
-- Table structure for table `evidence`
--

CREATE TABLE `evidence` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `evidence_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `collected_by` int(11) DEFAULT NULL,
  `collected_date` date DEFAULT NULL,
  `chain_of_custody` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `location_found` varchar(255) DEFAULT NULL,
  `date_found` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'collected',
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evidence`
--

INSERT INTO `evidence` (`id`, `case_id`, `evidence_type`, `description`, `file_path`, `collected_by`, `collected_date`, `chain_of_custody`, `created_at`, `location_found`, `date_found`, `notes`, `status`, `created_by`) VALUES
(1, 1, 'video', 'jjj', 'uploads/evidence/evidence_1_1762163870_69087c9e574d8.mp4', 9, NULL, 'hh', '2025-11-03 09:57:50', 'hjhhh', '2025-11-03', '', 'collected', 9),
(2, 1, 'photo', 't4g4rg', 'uploads/evidence/evidence_1_1762169879_690894175f6ec.jpg', 15, NULL, 'frewf', '2025-11-03 11:37:59', 'fferf', '2025-11-03', '', 'collected', 15),
(3, 3, 'fingerprint', 'dfgdfg', 'uploads/evidence/evidence_3_1762244472_6909b77825206.png', 15, NULL, '', '2025-11-04 08:21:12', 'fferf', '2025-11-04', '', 'collected', 15),
(4, 4, 'video', 'sgfgfg', 'uploads/evidence/evidence_4_1762423385_690c7259c6d36.mp4', 9, NULL, '', '2025-11-06 10:03:05', 'masha', '2025-11-06', '', 'collected', 9);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `format` varchar(10) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `generated_by` int(11) NOT NULL,
  `generated_at` datetime DEFAULT current_timestamp(),
  `file_size` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
(1, 'Administrator', 'Full system access'),
(2, 'Chief', 'Department head access'),
(3, 'Police Officer', 'Law enforcement officer'),
(4, 'Clerk', 'Data entry and basic access');

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_logs`
--

INSERT INTO `security_logs` (`id`, `user_id`, `event_type`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 8, 'settings_update', 'Two-factor authentication settings updated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 13:38:39'),
(2, 8, 'settings_update', 'Two-factor authentication settings updated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 13:38:46'),
(3, 8, 'settings_update', 'Password policy updated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 07:18:39'),
(4, 8, 'security_action', 'Forced password reset for all users', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 07:32:16'),
(5, 8, 'security_action', 'Locked all user sessions', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 07:46:37'),
(6, 8, 'settings_update', 'Password policy updated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 07:50:01'),
(7, 8, 'security_action', 'Locked all active user sessions', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 08:12:05'),
(8, 9, 'security_action', 'Locked all user sessions', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 08:13:30'),
(9, 8, 'security_action', 'Locked all user sessions', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 08:36:58'),
(10, 8, 'security_action', 'Forced password reset for all users', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 08:38:32'),
(11, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:19:52'),
(12, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:24:31'),
(13, 8, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:24:53'),
(14, 8, 'failed_login', 'Failed login attempt for user ID: 8', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:24:58'),
(15, 8, 'failed_login', 'Failed login attempt for user: enyew', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:24:58'),
(16, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:25:07'),
(17, 8, 'policy_update', 'Security policy updated: password_policy', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:25:25'),
(18, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:25:36'),
(19, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:25:59'),
(20, 9, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:26:20'),
(21, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:26:26'),
(22, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:26:31'),
(23, 8, 'security_action', 'Locked all user sessions', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:26:51'),
(24, 8, 'security_action', 'Locked all user sessions. Cleared 8 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:37:15'),
(25, 8, 'security_action', 'Locked all user sessions - 8 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:37:15'),
(26, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:38:55'),
(27, 9, 'security_action', 'Locked all user sessions. Cleared 1 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:39:08'),
(28, 9, 'security_action', 'Locked all user sessions - 1 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:39:08'),
(29, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:39:13'),
(30, 9, 'security_scan', 'Comprehensive security scan initiated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:39:34'),
(31, 9, 'settings_update', 'Two-factor authentication settings updated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:40:00'),
(32, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:40:07'),
(33, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:44:34'),
(34, 9, 'security_action', 'Locked all user sessions. Cleared 1 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:45:00'),
(35, 9, 'security_action', 'Locked all user sessions - 1 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:45:00'),
(36, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:45:06'),
(37, 10, 'failed_login', 'Failed login attempt for user ID: 10', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:45:30'),
(38, 10, 'failed_login', 'Failed login attempt for user: chief', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:45:30'),
(39, 10, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:45:36'),
(40, 10, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:45:58'),
(41, 10, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:46:02'),
(42, 10, 'security_action', 'Locked all user sessions. Cleared 1 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:46:39'),
(43, 10, 'security_action', 'Locked all user sessions - 1 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:46:39'),
(44, 10, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:47:02'),
(45, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:49:25'),
(46, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:50:43'),
(47, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:50:49'),
(48, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:50:59'),
(49, 15, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:51:11'),
(50, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:51:15'),
(51, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:53:50'),
(52, 11, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:54:10'),
(53, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:54:13'),
(54, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:55:58'),
(55, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:58:26'),
(56, NULL, 'login_attempt', 'Attempted login to deactivated account: william', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:58:42'),
(57, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:59:05'),
(58, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:59:12'),
(59, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 09:59:27'),
(60, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 10:05:58'),
(61, 10, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 10:08:14'),
(62, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 10:08:40'),
(63, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 10:11:06'),
(64, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 10:16:14'),
(65, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 11:02:36'),
(66, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 07:25:12'),
(67, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 07:35:09'),
(68, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 07:35:14'),
(69, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:01:57'),
(70, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:07:30'),
(71, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:09:39'),
(72, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:11:28'),
(73, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:12:10'),
(74, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:14:04'),
(75, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:14:10'),
(76, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:15:49'),
(77, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:30:41'),
(78, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:38:40'),
(79, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:39:03'),
(80, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:41:11'),
(81, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:41:16'),
(82, 15, 'security_scan', 'Comprehensive security scan initiated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:42:26'),
(83, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:42:51'),
(84, 8, 'user_created', 'User manuel created successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:55:18'),
(85, 8, 'user_creation_failed', 'Failed to create user manuel: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'manuel\' for key \'username\'', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:56:03'),
(86, 8, 'user_created', 'User bamlak created successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 08:56:55'),
(87, 23, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:03:20'),
(88, 23, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:03:40'),
(89, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:03:51'),
(90, 8, 'security_action', 'Locked all user sessions. Cleared 4 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:04:09'),
(91, 8, 'security_action', 'Locked all user sessions - 4 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:04:09'),
(92, 23, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:04:12'),
(93, 23, 'security_action', 'Locked all user sessions. Cleared 1 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:04:18'),
(94, 23, 'security_action', 'Locked all user sessions - 1 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:04:18'),
(95, 23, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:04:39'),
(96, 23, 'security_action', 'Locked all user sessions. Cleared 1 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:04:47'),
(97, 23, 'security_action', 'Locked all user sessions - 1 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:04:47'),
(98, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:04:59'),
(99, 9, 'security_action', 'Locked all user sessions. Cleared 1 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:05:05'),
(100, 9, 'security_action', 'Locked all user sessions - 1 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:05:05'),
(101, 10, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:05:16'),
(102, 10, 'security_action', 'Locked all user sessions. Cleared 1 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:05:21'),
(103, 10, 'security_action', 'Locked all user sessions - 1 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:05:21'),
(104, 15, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:05:48'),
(105, 21, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:05:55'),
(106, 17, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:06:02'),
(107, NULL, 'failed_login', 'Attempted login with non-existent username: hello', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:06:13'),
(108, 17, 'user_created', 'User dfdsfdsFD created successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:09:19'),
(109, 24, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:09:26'),
(110, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:09:40'),
(111, 8, 'security_action', 'Locked all user sessions. Cleared 1 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:09:58'),
(112, 8, 'security_action', 'Locked all user sessions - 1 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:09:58'),
(113, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:10:06'),
(114, 8, 'policy_update', 'Security policy updated: password_policy', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:10:18'),
(115, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:10:30'),
(116, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:10:43'),
(117, 9, 'user_created', 'User fdgzdfg created successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:18:04'),
(118, NULL, 'failed_login', 'Attempted login with non-existent username: fgfg', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:19:45'),
(119, NULL, 'failed_login', 'Attempted login with non-existent username: fgfg', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:25:11'),
(120, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:25:15'),
(121, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:25:21'),
(122, 8, 'user_creation_failed', 'Failed to create user enyew: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'enyew\' for key \'username\'', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:31:28'),
(123, 8, 'user_created', 'User fdgg created successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:31:44'),
(124, 8, 'user_created', 'User dsfsdfdsf created successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:32:14'),
(125, 8, 'security_action', 'Forced password reset for all users', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:32:23'),
(126, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:32:37'),
(127, 9, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:33:03'),
(128, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:33:07'),
(129, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:33:27'),
(130, 8, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:33:42'),
(131, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:33:46'),
(132, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:36:10'),
(133, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:47:25'),
(134, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:47:50'),
(135, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:48:02'),
(136, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:48:08'),
(137, 9, 'security_action', 'Locked ALL user sessions. Cleared 2 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:48:15'),
(138, 9, 'security_action', 'Locked all user sessions - 2 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:48:15'),
(139, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:49:59'),
(140, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:50:05'),
(141, 8, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:50:18'),
(142, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:50:22'),
(143, 23, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:52:55'),
(144, 23, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:53:09'),
(145, 23, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:53:13'),
(146, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:53:19'),
(147, 9, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:53:32'),
(148, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:53:36'),
(149, 9, 'security_action', 'Locked ALL user sessions. Cleared 1 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:53:44'),
(150, 9, 'security_action', 'Locked all user sessions - 1 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 09:53:44'),
(151, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 10:16:31'),
(152, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 10:16:49'),
(153, 9, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 10:17:00'),
(154, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 10:17:04'),
(155, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 10:17:12'),
(156, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 10:17:23'),
(157, 8, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 10:17:37'),
(158, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 05:28:54'),
(159, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 05:30:03'),
(160, 9, 'security_action', 'Locked ALL user sessions. Cleared 3 session files.', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 05:30:15'),
(161, 9, 'security_action', 'Locked all user sessions - 3 sessions terminated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 05:30:15'),
(162, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 05:30:21'),
(163, 9, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 05:30:40'),
(164, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 05:30:47'),
(165, 10, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 05:41:12'),
(166, 10, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 05:41:26'),
(167, 10, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 05:41:40'),
(168, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 07:12:50'),
(169, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:29:07'),
(170, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:37:40'),
(171, 10, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:37:46'),
(172, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:38:10'),
(173, 11, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:38:20'),
(174, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:38:25'),
(175, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:44:27'),
(176, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:07:26'),
(177, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:16:34'),
(178, 8, 'password_change', 'User changed password successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:16:45'),
(179, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:16:49'),
(180, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:19:12'),
(181, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:20:43'),
(182, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:20:53'),
(183, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:40:22'),
(184, 10, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:42:00'),
(185, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 10:02:55'),
(186, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 10:50:49'),
(187, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 10:50:59'),
(188, 10, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 10:51:09'),
(189, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 10:51:31'),
(190, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 10:51:51'),
(191, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:36:49'),
(192, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:38:07'),
(193, 8, 'user_created', 'User jjj created successfully', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:38:49'),
(194, 29, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:38:59'),
(195, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:39:31'),
(196, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:41:10'),
(197, 11, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:41:28'),
(198, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:41:51'),
(199, 8, 'security_scan', 'Comprehensive security scan initiated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:42:33'),
(200, 8, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:51:13'),
(201, 9, 'successful_login', 'User logged in successfully from IP: ::1', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 11:51:24');

-- --------------------------------------------------------

--
-- Table structure for table `security_settings`
--

CREATE TABLE `security_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_settings`
--

INSERT INTO `security_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'password_min_length', '6', 'Minimum password length', '2025-11-07 09:10:18'),
(2, 'password_require_uppercase', '1', 'Require uppercase letters in passwords', '2025-11-05 07:50:01'),
(3, 'password_require_numbers', '1', 'Require numbers in passwords', '2025-11-05 07:50:01'),
(4, 'password_require_special', '1', 'Require special characters in passwords', '2025-11-05 07:50:01'),
(5, 'login_max_attempts', '5', 'Maximum login attempts before lockout', '2025-11-03 13:06:25'),
(6, 'login_lockout_duration', '30', 'Lockout duration in minutes', '2025-11-03 13:06:25'),
(7, 'session_timeout', '60', 'Session timeout in minutes', '2025-11-03 13:06:25'),
(8, 'two_factor_enabled', '1', 'Enable two-factor authentication', '2025-11-06 09:40:00'),
(9, 'security_scan_enabled', '1', 'Enable security scanning', '2025-11-03 13:06:25'),
(32, 'last_policy_update', '1762506618', NULL, '2025-11-07 09:10:18'),
(33, 'global_session_reset', '1762579815', NULL, '2025-11-08 05:30:15'),
(50, 'force_session_check', '1', NULL, '2025-11-07 09:48:15');

-- --------------------------------------------------------

--
-- Table structure for table `system_news`
--

CREATE TABLE `system_news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `priority` enum('low','medium','high') DEFAULT 'low',
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('administrator','chief','officer','clerk') NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `badge_number` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `login_attempts` int(11) DEFAULT 0,
  `force_password_change` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `first_name`, `last_name`, `email`, `phone`, `badge_number`, `department`, `is_active`, `last_login`, `created_at`, `updated_at`, `login_attempts`, `force_password_change`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrator', 'System', 'Administrator', 'admin@mattucity.gov', NULL, 'ADM001', 'Administration', 1, NULL, '2025-10-13 12:43:41', '2025-11-05 07:32:16', 0, 1),
(2, 'chief_johnson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'chief', 'Michael', 'Johnson', 'chief@mattucity.gov', NULL, 'CHF001', 'Command', 1, NULL, '2025-10-13 12:43:41', '2025-11-05 07:32:16', 0, 1),
(3, 'officer_smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'officer', 'John', 'Smith', 'officer.smith@mattucity.gov', NULL, 'OFF001', 'Patrol', 1, NULL, '2025-10-13 12:43:41', '2025-11-05 07:32:16', 0, 1),
(4, 'clerk_davis', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'clerk', 'Sarah', 'Davis', 'clerk.davis@mattucity.gov', NULL, 'CLK001', 'Records', 1, NULL, '2025-10-13 12:43:41', '2025-11-05 07:32:16', 0, 1),
(8, 'enyew', '$2y$10$JIVrQEMkBhIdKKfzaZVQSOvNb5AdMmx0qagep/ptyd0E3xtmsm5l2', 'administrator', 'Enyew', 'Abebe', 'enyew@gmail.com', '5556569569562', 'ADM001', 'fsd', 1, '2025-11-08 14:51:13', '2025-10-13 12:52:34', '2025-11-08 11:51:13', 0, 0),
(9, 'officer', '$2y$10$RCfbPzuyn38SfGK1h5lHDupf8lQLs4p6r8dbUxOfEsp.NUg8.3kw2', 'officer', 'cdfsf', 'sffs', 'sfdfsf', 'sfsf', 'sfdf', 'fsf', 1, '2025-11-08 14:51:24', '2025-10-13 12:54:56', '2025-11-08 11:51:24', 0, 0),
(10, 'chief', '$2y$10$vM5B0lvA9D8BOVPvc9MGiuXZ95z5xh9iyoTQJP4YAWdKICb9Ds9Ra', 'chief', 'dfsdf', 'dff', 'sfsf', 'sfs', 'sff', 'sfs', 1, '2025-11-08 13:51:09', '2025-10-13 12:56:41', '2025-11-08 10:51:09', 0, 0),
(11, 'clerk', '$2y$10$DeV8gMw3JqrD3wh/lsYROej9M0LiSW5QMuPvFwsterRMkLvhrMHo2', 'clerk', 'dfdfdf', 'frfdf', 'dfefd', 'df', 'dfd', 'dfzdf', 1, '2025-11-08 14:41:28', '2025-10-13 12:58:20', '2025-11-08 11:41:28', 0, 0),
(13, 'jason', '$2y$10$l6gsSmGwppTaPtNgNAdmVetXInCRxcctNEh5ba5d8lx1l8ORT5rR.', 'officer', 'Jason', 'Rostro', 'jayrostro1005@gmail.com', NULL, NULL, NULL, 1, NULL, '2025-10-22 08:08:29', '2025-11-05 07:32:16', 0, 1),
(14, 'brandon', '$2y$10$8wVjgzMaZOfe6PuLBicKquEakaH7dmfkrpQsrnf.wvt6E2eEaKDha', 'administrator', 'Brandon', 'Teng', 'brandon.t4680@gmail.com', NULL, NULL, NULL, 1, NULL, '2025-10-22 08:29:11', '2025-11-05 07:32:16', 0, 1),
(15, 'akanji', '$2y$10$DjpKBeMnkPMoWtIA3UT5wuXLhGr50xkqNuBS8Rx8A5SM6DtM7Jkei', 'officer', 'akanji', 'Zhou', 'tzhoux721@gmail.com', NULL, NULL, NULL, 1, '2025-11-07 12:05:48', '2025-11-03 10:00:03', '2025-11-07 09:32:23', 0, 1),
(16, 'philp', '$2y$10$xqly.NhmU/O3PyiWOlFiIuoDWgnL/YY1QtPohjKEnsBcWiAJ2znt.', 'administrator', 'Phillip', 'Mrzyglocki', 'phillipcm.work@gmail.com', NULL, NULL, NULL, 1, NULL, '2025-11-03 12:25:34', '2025-11-05 07:32:16', 0, 1),
(17, 'ALEX', '$2y$10$G7l92m9dHk15UVwaR8TWxOCZl8UxbJSM0c4gqZQmyygD7z4pY6vnG', 'officer', 'Alex', 'Huang', 'alexhuang080@gmail.com', NULL, NULL, NULL, 1, '2025-11-07 12:06:02', '2025-11-03 14:38:49', '2025-11-07 09:06:02', 0, 1),
(20, 'william', '$2y$10$Y5jHfdnTM9KUJXmkqUS8.u0OKwrc2FvvW5KLFxc94L8OIVXWo7pui', 'chief', 'Williamaa', 'Huang', 'whuang.work@gmail.com', NULL, NULL, NULL, 0, NULL, '2025-11-05 07:19:12', '2025-11-05 08:11:20', 0, 1),
(21, 'manuel', '$2y$10$LAW73I.5nGcsC6IZoE0Ic./cLvWYm5FCklu5hGFbNdqxHgSk6VsFW', 'clerk', 'David', 'Huang', 'thisisdavidhuang90@gmail.com', NULL, NULL, NULL, 1, '2025-11-07 12:05:55', '2025-11-07 08:55:18', '2025-11-07 09:32:23', 0, 1),
(23, 'bamlak', '$2y$10$yD2Aocai3Iz10bJge2qYDe3jXFHrEoNiA5hTy2RfjIEgAZHjHkADG', 'clerk', 'bamlak', 'abebe', 'Bam@12345', NULL, NULL, NULL, 1, '2025-11-07 12:53:13', '2025-11-07 08:56:55', '2025-11-07 09:53:44', 0, 1),
(24, 'dfdsfdsFD', '$2y$10$n.iL38C9aPKDASb/8a4/OOTD5a0s7IqcauxYX2bPP7prwRfvbguPi', 'officer', 'fgzdfg', 'ddsdsfsd', 'ffds@fgdf', NULL, NULL, NULL, 1, '2025-11-07 12:09:26', '2025-11-07 09:09:19', '2025-11-07 09:32:23', 0, 1),
(25, 'fdgzdfg', '$2y$10$yVmUYzLIRh4MVL4i3nxOA.7nyGtq6MJxUfAbAEhEBm3NRvRKGB2.W', 'clerk', 'cghg', 'fgdfg', 'zfgz@ghg', NULL, NULL, NULL, 1, NULL, '2025-11-07 09:18:04', '2025-11-07 09:32:23', 0, 1),
(27, 'fdgg', '$2y$10$ylFeQSxchzZ0uCdho43Qiu/7VCP2JMzZbj2ChM0q7PKXWSNtxcSB2', 'officer', 'dfgdfgd', 'fdgfd', 'gdfgdfgdf#@g', NULL, NULL, NULL, 1, NULL, '2025-11-07 09:31:44', '2025-11-07 09:32:23', 0, 1),
(28, 'dsfsdfdsf', '$2y$10$j0Aq4OGjN5/BifzzCAe7XeafZ4PBT0EkOVYFTrJCLsiak4DnrzUpO', 'officer', 'zfgdfv', 'dfzgdsvs', 'dsfdsfW@fdg', NULL, NULL, NULL, 1, NULL, '2025-11-07 09:32:14', '2025-11-07 09:32:23', 0, 1),
(29, 'jjj', '$2y$10$vs10yJVUJEVyv9X.VpV/k.t68477Q7r7Lx8PHHw3bPeNMYVQB0kLa', 'chief', 'Tommy', 'Zhou', 'tzhoux721@gmail.com', NULL, NULL, NULL, 1, '2025-11-08 14:38:59', '2025-11-08 11:38:49', '2025-11-08 11:38:59', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_login_history`
--

CREATE TABLE `user_login_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_login_history`
--

INSERT INTO `user_login_history` (`id`, `user_id`, `login_time`, `ip_address`, `user_agent`) VALUES
(1, 1, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(2, 2, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(3, 3, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(4, 8, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(5, 9, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(6, 4, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(7, 10, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(8, 11, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(9, 13, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(10, 14, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(11, 15, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(12, 16, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(13, 17, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(14, 17, '2025-11-07 11:11:07', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(15, 1, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(16, 2, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(17, 3, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(18, 8, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(19, 4, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(20, 9, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(21, 10, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(22, 11, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(23, 13, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(24, 14, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(25, 15, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(26, 16, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(27, 17, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(28, 17, '2025-11-07 11:16:14', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(29, 1, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(30, 2, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(31, 3, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(32, 4, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(33, 8, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(34, 9, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(35, 10, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(36, 11, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(37, 13, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(38, 14, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(39, 15, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(40, 16, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(41, 17, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(42, 17, '2025-11-07 11:16:30', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(43, 2, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(44, 1, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(45, 3, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(46, 4, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(47, 9, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(48, 8, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(49, 10, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(50, 11, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(51, 13, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(52, 14, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(53, 15, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(54, 16, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(55, 17, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36'),
(56, 17, '2025-11-07 11:16:36', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `view_history`
--

CREATE TABLE `view_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `target_type` enum('record','case') NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_title` varchar(255) DEFAULT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_logs_user` (`user_id`),
  ADD KEY `idx_activity_logs_date` (`created_at`);

--
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_number` (`case_number`),
  ADD KEY `lead_officer_id` (`lead_officer_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_cases_number` (`case_number`),
  ADD KEY `idx_cases_status` (`status`),
  ADD KEY `idx_cases_officer` (`assigned_officer_id`);

--
-- Indexes for table `case_files`
--
ALTER TABLE `case_files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_number` (`case_number`),
  ADD KEY `idx_case_number` (`case_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_lead_officer` (`lead_officer_id`);

--
-- Indexes for table `case_notes`
--
ALTER TABLE `case_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `case_persons`
--
ALTER TABLE `case_persons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `idx_case_persons_case` (`case_id`),
  ADD KEY `idx_case_persons_record` (`record_id`);

--
-- Indexes for table `case_reassignments`
--
ALTER TABLE `case_reassignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_case_reassignments_case_id` (`case_id`);

--
-- Indexes for table `criminal_records`
--
ALTER TABLE `criminal_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_criminal_records_national_id` (`national_id`),
  ADD KEY `idx_criminal_records_name` (`first_name`,`last_name`);

--
-- Indexes for table `evidence`
--
ALTER TABLE `evidence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `collected_by` (`collected_by`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_security_logs_user_id` (`user_id`),
  ADD KEY `idx_security_logs_created_at` (`created_at`);

--
-- Indexes for table `security_settings`
--
ALTER TABLE `security_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `system_news`
--
ALTER TABLE `system_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_login_history`
--
ALTER TABLE `user_login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `idx_user_sessions_user_id` (`user_id`),
  ADD KEY `idx_user_sessions_last_activity` (`last_activity`);

--
-- Indexes for table `view_history`
--
ALTER TABLE `view_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_view_history_user` (`user_id`),
  ADD KEY `idx_view_history_target` (`target_type`,`target_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `case_files`
--
ALTER TABLE `case_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `case_notes`
--
ALTER TABLE `case_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `case_persons`
--
ALTER TABLE `case_persons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `case_reassignments`
--
ALTER TABLE `case_reassignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `criminal_records`
--
ALTER TABLE `criminal_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `evidence`
--
ALTER TABLE `evidence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT for table `security_settings`
--
ALTER TABLE `security_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `system_news`
--
ALTER TABLE `system_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `user_login_history`
--
ALTER TABLE `user_login_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `view_history`
--
ALTER TABLE `view_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `cases`
--
ALTER TABLE `cases`
  ADD CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`assigned_officer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`lead_officer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cases_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `case_notes`
--
ALTER TABLE `case_notes`
  ADD CONSTRAINT `case_notes_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `case_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `case_persons`
--
ALTER TABLE `case_persons`
  ADD CONSTRAINT `case_persons_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `case_persons_ibfk_2` FOREIGN KEY (`record_id`) REFERENCES `criminal_records` (`id`),
  ADD CONSTRAINT `case_persons_ibfk_3` FOREIGN KEY (`added_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `case_reassignments`
--
ALTER TABLE `case_reassignments`
  ADD CONSTRAINT `fk_case_reassignments_case_id` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `criminal_records`
--
ALTER TABLE `criminal_records`
  ADD CONSTRAINT `criminal_records_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `evidence`
--
ALTER TABLE `evidence`
  ADD CONSTRAINT `evidence_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evidence_ibfk_2` FOREIGN KEY (`collected_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD CONSTRAINT `security_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `system_news`
--
ALTER TABLE `system_news`
  ADD CONSTRAINT `system_news_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_login_history`
--
ALTER TABLE `user_login_history`
  ADD CONSTRAINT `user_login_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `view_history`
--
ALTER TABLE `view_history`
  ADD CONSTRAINT `view_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
