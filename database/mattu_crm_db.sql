-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 03, 2025 at 04:32 PM
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
(5, 8, 'user_create', 'Created user: ALEX', NULL, NULL, NULL, NULL, '2025-11-03 14:38:49');

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
(1, 'CASE-20251103-1110', 'Theft', 'Drug Offense Case - CASE-20251103-1110', 'ghgfgsfgs', 'Round Rock, TX', '2025-11-03', 'Open', 'low', 'Medium', 17, 17, 9, '2025-11-03 08:14:24', '2025-11-03 14:54:49');

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
(3, 1, 2, 'Suspect', NULL, 9, '2025-11-03 08:14:24');

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
(2, '9876543210987', 'Jason', 'Rostro', '2001-02-06', 'Male', 175.00, 60.00, 'Brown', 'Brown', 'jjj', 'uploads/criminal_photos/criminal_1762152851_690851934604d.jpg', 'First Offender', 9, '2025-11-03 06:54:11', '2025-11-03 06:54:11');

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
(2, 1, 'photo', 't4g4rg', 'uploads/evidence/evidence_1_1762169879_690894175f6ec.jpg', 15, NULL, 'frewf', '2025-11-03 11:37:59', 'fferf', '2025-11-03', '', 'collected', 15);

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
(2, 8, 'settings_update', 'Two-factor authentication settings updated', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 13:38:46');

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
(1, 'password_min_length', '8', 'Minimum password length', '2025-11-03 13:06:25'),
(2, 'password_require_uppercase', '1', 'Require uppercase letters in passwords', '2025-11-03 13:06:25'),
(3, 'password_require_numbers', '1', 'Require numbers in passwords', '2025-11-03 13:06:25'),
(4, 'password_require_special', '1', 'Require special characters in passwords', '2025-11-03 13:06:25'),
(5, 'login_max_attempts', '5', 'Maximum login attempts before lockout', '2025-11-03 13:06:25'),
(6, 'login_lockout_duration', '30', 'Lockout duration in minutes', '2025-11-03 13:06:25'),
(7, 'session_timeout', '60', 'Session timeout in minutes', '2025-11-03 13:06:25'),
(8, 'two_factor_enabled', '0', 'Enable two-factor authentication', '2025-11-03 13:38:46'),
(9, 'security_scan_enabled', '1', 'Enable security scanning', '2025-11-03 13:06:25');

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
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrator', 'System', 'Administrator', 'admin@mattucity.gov', NULL, 'ADM001', 'Administration', 1, NULL, '2025-10-13 12:43:41', '2025-10-13 12:43:41', 0, 0),
(2, 'chief_johnson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'chief', 'Michael', 'Johnson', 'chief@mattucity.gov', NULL, 'CHF001', 'Command', 1, NULL, '2025-10-13 12:43:41', '2025-10-13 12:43:41', 0, 0),
(3, 'officer_smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'officer', 'John', 'Smith', 'officer.smith@mattucity.gov', NULL, 'OFF001', 'Patrol', 1, NULL, '2025-10-13 12:43:41', '2025-10-13 12:43:41', 0, 0),
(4, 'clerk_davis', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'clerk', 'Sarah', 'Davis', 'clerk.davis@mattucity.gov', NULL, 'CLK001', 'Records', 1, NULL, '2025-10-13 12:43:41', '2025-10-13 12:43:41', 0, 0),
(8, 'enyew', '$2y$10$8rNdG6iZHCTkUka4ipeGUuUGzvuGGOgeYUIB1QzzFbXrCT4fN/Sam', 'administrator', 'Enyew', 'Abebe', 'enyew@gmail.com', '5556569569562', 'ADM001', 'fsd', 1, NULL, '2025-10-13 12:52:34', '2025-10-13 12:52:34', 0, 0),
(9, 'officer', '$2y$10$EMbNiqzkPY/Ixq5xfz.JHOeejB47uuj3lpuMb88f4c06qWqqtOsNu', 'officer', 'cdfsf', 'sffs', 'sfdfsf', 'sfsf', 'sfdf', 'fsf', 1, NULL, '2025-10-13 12:54:56', '2025-10-13 12:54:56', 0, 0),
(10, 'chief', '$2y$10$4VZh33WS6Vrh72YmgkYcyOsz1CF3O1ceFyvIJVdM8epmJRQOz1Lhm', 'chief', 'dfsdf', 'dff', 'sfsf', 'sfs', 'sff', 'sfs', 1, NULL, '2025-10-13 12:56:41', '2025-10-13 12:56:41', 0, 0),
(11, 'clerk', '$2y$10$Womck2N9eRA2mXSnbuH27.js.ig0L8rn4/rwGDrW03mNUi5QHgHIW', 'clerk', 'dfdfdf', 'frfdf', 'dfefd', 'df', 'dfd', 'dfzdf', 1, NULL, '2025-10-13 12:58:20', '2025-10-13 12:58:20', 0, 0),
(13, 'jason', '$2y$10$l6gsSmGwppTaPtNgNAdmVetXInCRxcctNEh5ba5d8lx1l8ORT5rR.', 'officer', 'Jason', 'Rostro', 'jayrostro1005@gmail.com', NULL, NULL, NULL, 1, NULL, '2025-10-22 08:08:29', '2025-10-22 08:08:29', 0, 0),
(14, 'brandon', '$2y$10$8wVjgzMaZOfe6PuLBicKquEakaH7dmfkrpQsrnf.wvt6E2eEaKDha', 'administrator', 'Brandon', 'Teng', 'brandon.t4680@gmail.com', NULL, NULL, NULL, 1, NULL, '2025-10-22 08:29:11', '2025-10-22 08:29:11', 0, 0),
(15, 'akanji', '$2y$10$IxCa32mf00kePEWzPIW5.u0WCRvYQ.bogD/tFccwA2t5dq80vVYa.', 'officer', 'akanji', 'Zhou', 'tzhoux721@gmail.com', NULL, NULL, NULL, 1, NULL, '2025-11-03 10:00:03', '2025-11-03 10:00:03', 0, 0),
(16, 'philp', '$2y$10$xqly.NhmU/O3PyiWOlFiIuoDWgnL/YY1QtPohjKEnsBcWiAJ2znt.', 'administrator', 'Phillip', 'Mrzyglocki', 'phillipcm.work@gmail.com', NULL, NULL, NULL, 1, NULL, '2025-11-03 12:25:34', '2025-11-03 12:25:34', 0, 0),
(17, 'ALEX', '$2y$10$G7l92m9dHk15UVwaR8TWxOCZl8UxbJSM0c4gqZQmyygD7z4pY6vnG', 'officer', 'Alex', 'Huang', 'alexhuang080@gmail.com', NULL, NULL, NULL, 1, NULL, '2025-11-03 14:38:49', '2025-11-03 14:38:49', 0, 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `case_files`
--
ALTER TABLE `case_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `case_notes`
--
ALTER TABLE `case_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_persons`
--
ALTER TABLE `case_persons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `case_reassignments`
--
ALTER TABLE `case_reassignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `criminal_records`
--
ALTER TABLE `criminal_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `evidence`
--
ALTER TABLE `evidence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `security_settings`
--
ALTER TABLE `security_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `system_news`
--
ALTER TABLE `system_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
