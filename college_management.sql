-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2026 at 08:17 PM
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
-- Database: `college_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `before_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before_data`)),
  `after_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`after_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `before_data`, `after_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-IN) WindowsPowerShell/5.1.26100.9444', '2026-09-27 12:36:39'),
(2, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-IN) WindowsPowerShell/5.1.26100.9444', '2026-09-27 12:36:58'),
(3, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-IN) WindowsPowerShell/5.1.26100.9444', '2026-09-27 12:38:10'),
(4, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-IN) WindowsPowerShell/5.1.26100.9444', '2026-09-27 12:39:01'),
(5, 1, 'department_created', 'departments', 3, NULL, '{\"csrf_token\":\"374aed948807f0a46fa17fe327c82260ea2c006735a7da007957fc905c614e80\",\"name\":\"Electrical Engineering\",\"code\":\"EE\"}', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-IN) WindowsPowerShell/5.1.26100.9444', '2026-09-27 12:39:01'),
(6, 1, 'faculty_created', 'faculty', 1, NULL, '{\"email\":\"alan.turing@college.local\",\"csrf_token\":\"374aed948807f0a46fa17fe327c82260ea2c006735a7da007957fc905c614e80\",\"name\":\"Dr. Alan Turing\",\"department_id\":\"1\",\"designation\":\"Professor\"}', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-IN) WindowsPowerShell/5.1.26100.9444', '2026-09-27 12:39:02'),
(7, 1, 'student_enrolled', 'students', 1, NULL, '{\"admission_no\":\"CMS-2026-0001\",\"name\":\"John Doe\"}', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-IN) WindowsPowerShell/5.1.26100.9444', '2026-09-27 12:39:02'),
(8, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 12:41:03'),
(9, 1, 'student_enrolled', 'students', 2, NULL, '{\"admission_no\":\"CMS-2026-0002\",\"name\":\"Parth Panewar\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 12:41:50'),
(10, 1, 'role_changed', 'users', 4, '{\"id\":4,\"name\":\"Parth Panewar\",\"email\":\"parthpanewar75@gmail.com\",\"password\":\"$2y$10$rEBungGrrATkjzsauIb55e7iLtGsCaITmqBVKx4CgsjSFnWPGEoVu\",\"role\":\"student\",\"two_factor_enabled\":0,\"failed_attempts\":0,\"locked_until\":null,\"last_login_at\":null,\"created_at\":\"2026-09-27 12:41:49\"}', '{\"csrf_token\":\"a6a9c33834a81ad6b28d75e210017578786c7ca9a251611deac3516f05bff70b\",\"role\":\"admin\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 12:42:30'),
(11, 1, 'attendance_marked', 'attendance', NULL, NULL, '{\"course_id\":1,\"date\":\"2026-09-27\",\"count\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 12:42:42'),
(12, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 12:44:39'),
(13, 1, 'attendance_marked', 'attendance', NULL, NULL, '{\"course_id\":1,\"date\":\"2026-09-27\",\"count\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 13:32:18'),
(14, 1, 'user_updated', 'users', 4, '{\"id\":4,\"name\":\"Parth Panewar\",\"email\":\"parthpanewar75@gmail.com\",\"password\":\"$2y$10$rEBungGrrATkjzsauIb55e7iLtGsCaITmqBVKx4CgsjSFnWPGEoVu\",\"role\":\"admin\",\"avatar\":null,\"phone\":null,\"status\":\"active\",\"reset_token\":null,\"reset_expires_at\":null,\"two_factor_enabled\":0,\"failed_attempts\":0,\"locked_until\":null,\"last_login_at\":null,\"created_at\":\"2026-09-27 12:41:49\",\"deleted_at\":null}', '{\"name\":\"Parth Panewar\",\"email\":\"parthpanewar75@gmail.com\",\"role\":\"admin\",\"phone\":\"07620392130\",\"status\":\"active\",\"password\":\"$2y$10$b0SBltG4KF1SgV2o11eVdeYa\\/jikAnZE4mhQygaDIXGPrmepQDPc2\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 13:33:20'),
(15, 1, 'logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 13:34:17'),
(16, 7, 'login_success', 'users', 7, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 13:34:25'),
(17, 7, 'logout', 'users', 7, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 13:58:13'),
(18, 6, 'login_success', 'users', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 13:58:16'),
(19, 6, 'logout', 'users', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 13:59:02'),
(20, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 13:59:04'),
(21, 1, 'student_enrolled', 'students', 3, NULL, '{\"admission_no\":\"CMS-2026-0003\",\"name\":\"Parth Panewar\",\"fee_generated\":2500}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-27 14:06:16'),
(22, 5, 'login_success', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 17:54:52'),
(23, 5, 'logout', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:30:29'),
(24, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:30:36'),
(25, 1, 'logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:30:55'),
(26, 5, 'login_success', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:30:58'),
(27, 5, 'fee_payment_recorded', 'fees', 1, '{\"id\":1,\"student_id\":3,\"fee_type\":\"Annual Tuition Fee\",\"amount\":\"2500.00\",\"amount_paid\":\"0.00\",\"due_date\":\"2026-10-27\",\"status\":\"pending\",\"last_payment_at\":null,\"created_at\":\"2026-09-27 14:06:16\",\"deleted_at\":null}', '{\"amount_paid\":2500,\"status\":\"paid\",\"transaction_id\":1,\"transaction_no\":\"TXN-20261008-9A95BD\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:31:14'),
(28, 5, 'logout', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:31:40'),
(29, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:31:46'),
(30, 1, 'logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:33:30'),
(31, 7, 'login_success', 'users', 7, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:33:33'),
(32, 7, 'logout', 'users', 7, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:40:35'),
(33, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:40:39'),
(34, 1, 'user_updated', 'users', 10, '{\"id\":10,\"name\":\"Parth Panewar\",\"email\":\"parthpanewar755@gmail.com\",\"password\":\"$2y$10$nu7XuluOQAeDsd1o9tO4dOakmISqXb6yY3e3APxPUTWCQGfqnx.Zu\",\"role\":\"student\",\"avatar\":null,\"phone\":\"+917620392130\",\"status\":\"active\",\"reset_token\":null,\"reset_expires_at\":null,\"two_factor_enabled\":0,\"failed_attempts\":0,\"locked_until\":null,\"last_login_at\":null,\"created_at\":\"2026-09-27 14:06:16\",\"deleted_at\":null}', '{\"name\":\"Parth Panewar\",\"email\":\"parthpanewar755@gmail.com\",\"role\":\"admin\",\"phone\":\"+917620392130\",\"status\":\"active\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:42:06'),
(35, 1, 'role_permissions_updated', 'roles', 3, NULL, '{\"role\":\"faculty\",\"permission_count\":7}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:44:29'),
(36, 1, 'logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:44:34'),
(37, 5, 'login_success', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:44:41'),
(38, 5, 'complaint_submitted', 'complaints', 1, NULL, '{\"title\":\"wifi problem\",\"category\":\"infrastructure\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:46:03'),
(39, 5, 'logout', 'users', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:46:31'),
(40, 6, 'login_success', 'users', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:46:36'),
(41, 6, 'attendance_marked', 'attendance', NULL, NULL, '{\"course_id\":1,\"date\":\"2026-10-08\",\"count\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:46:48'),
(42, 6, 'notice_posted', 'notices', 1, NULL, '{\"csrf_token\":\"365d54f469522421e805dc362ee30cd4e4fea50adac0649520c1aa1ef4d6af8c\",\"title\":\"sem\",\"body\":\"sem\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:47:23'),
(43, 6, 'logout', 'users', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:48:09'),
(44, 7, 'login_success', 'users', 7, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:48:12'),
(45, 7, 'logout', 'users', 7, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:50:10'),
(46, 13, 'user_registered', 'users', 13, NULL, '{\"name\":\"Parth Panewar\",\"email\":\"shubhamkawale1432@gmail.com\",\"role\":\"student\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:51:49'),
(47, 13, 'login_failed', 'users', 13, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:53:23'),
(48, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:53:45'),
(49, 1, 'user_updated', 'users', 13, '{\"id\":13,\"name\":\"Parth Panewar\",\"email\":\"shubhamkawale1432@gmail.com\",\"password\":\"$2y$10$th54qq1PaMmp8SIZJD0Fu.OYdZ\\/9bXNhB7uYLvaQZPgd8d0Y63P\\/e\",\"role\":\"student\",\"avatar\":null,\"phone\":null,\"status\":\"active\",\"reset_token\":null,\"reset_expires_at\":null,\"two_factor_enabled\":0,\"failed_attempts\":1,\"locked_until\":null,\"last_login_at\":null,\"created_at\":\"2026-10-08 18:51:49\",\"deleted_at\":null}', '{\"name\":\"shubham kawale\",\"email\":\"shubhamkawale1432@gmail.com\",\"role\":\"student\",\"phone\":\"\",\"status\":\"active\",\"password\":\"$2y$10$UHnY4lAwudkFTIH3uUBlqeOBMnX42BZ.Mv8gO0\\/PJalrazI8ZfMuq\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:54:32'),
(50, 1, 'logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:54:39'),
(51, 13, 'login_success', 'users', 13, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 18:55:01'),
(52, 13, 'logout', 'users', 13, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 19:14:13'),
(53, 1, 'login_success', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 19:14:17'),
(54, 1, 'logout', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 19:14:59'),
(55, 4, 'login_success', 'users', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-08 19:17:06');

-- --------------------------------------------------------

--
-- Table structure for table `api_tokens`
--

CREATE TABLE `api_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_tokens`
--

INSERT INTO `api_tokens` (`id`, `user_id`, `name`, `token_hash`, `expires_at`, `created_at`) VALUES
(1, 1, 'api-login', '7682d26de47039cdcad2d01db6b98f9650361b0696a6898d688427ceaedb7cf1', '2026-10-27 11:38:10', '2026-09-27 12:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late') NOT NULL DEFAULT 'present',
  `marked_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `attendance_date`, `status`, `marked_by`, `created_at`) VALUES
(1, 1, '2026-09-27', 'present', 1, '2026-09-27 12:42:42'),
(2, 2, '2026-09-27', 'present', 1, '2026-09-27 12:42:42'),
(3, 1, '2026-10-08', 'present', 6, '2026-10-08 18:46:48'),
(4, 2, '2026-10-08', 'present', 6, '2026-10-08 18:46:48'),
(5, 3, '2026-10-08', 'present', 6, '2026-10-08 18:46:48');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `ticket_no` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `category` enum('academic','hostel','fees','library','infrastructure','other') NOT NULL DEFAULT 'academic',
  `description` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `admin_notes` text DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `ticket_no`, `user_id`, `title`, `category`, `description`, `attachment`, `status`, `priority`, `admin_notes`, `resolved_at`, `deleted_at`, `created_at`) VALUES
(1, 'TKT-261008-765D', 5, 'wifi problem', 'infrastructure', 'wifi problem', NULL, 'open', 'medium', NULL, NULL, NULL, '2026-10-08 22:16:03');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `duration_years` tinyint(4) NOT NULL DEFAULT 4,
  `credits` tinyint(4) NOT NULL DEFAULT 3,
  `description` text DEFAULT NULL,
  `fee_per_year` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `code`, `department_id`, `duration_years`, `credits`, `description`, `fee_per_year`, `created_at`, `deleted_at`) VALUES
(1, 'B.Sc. Computer Science', 'BSC-CS', 1, 3, 3, NULL, 0.00, '2026-09-27 16:02:29', NULL),
(2, 'BBA', 'BBA', 2, 3, 3, NULL, 0.00, '2026-09-27 16:02:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `head_of_department` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `head_of_department`, `description`, `created_at`, `deleted_at`) VALUES
(1, 'Computer Science', 'CS', NULL, NULL, '2026-09-27 16:02:29', NULL),
(2, 'Business Administration', 'BA', NULL, NULL, '2026-09-27 16:02:29', NULL),
(3, 'Electrical Engineering', 'EE', NULL, NULL, '2026-09-27 12:39:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `exam_date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `qualification` varchar(150) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `user_id`, `name`, `photo`, `email`, `phone`, `department_id`, `designation`, `qualification`, `joining_date`, `created_at`, `deleted_at`) VALUES
(1, 2, 'Dr. Alan Turing', NULL, 'alan.turing@college.local', NULL, 1, 'Professor', NULL, NULL, '2026-09-27 12:39:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fee_type` varchar(100) NOT NULL DEFAULT 'Tuition Fee',
  `amount` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_date` date NOT NULL,
  `status` enum('pending','partial','paid') NOT NULL DEFAULT 'pending',
  `last_payment_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `student_id`, `fee_type`, `amount`, `amount_paid`, `due_date`, `status`, `last_payment_at`, `created_at`, `deleted_at`) VALUES
(1, 3, 'Annual Tuition Fee', 2500.00, 2500.00, '2026-10-27', 'paid', '2026-10-08 18:31:14', '2026-09-27 14:06:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `library_books`
--

CREATE TABLE `library_books` (
  `id` int(11) NOT NULL,
  `isbn` varchar(30) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(150) NOT NULL,
  `category` varchar(100) NOT NULL,
  `total_copies` int(11) NOT NULL DEFAULT 1,
  `available_copies` int(11) NOT NULL DEFAULT 1,
  `rack_location` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `library_books`
--

INSERT INTO `library_books` (`id`, `isbn`, `title`, `author`, `category`, `total_copies`, `available_copies`, `rack_location`, `created_at`) VALUES
(1, '978-0131103627', 'The C Programming Language', 'Brian W. Kernighan, Dennis M. Ritchie', 'Computer Science', 5, 5, 'CS-RACK-01', '2026-09-27 16:24:36'),
(2, '978-0262033848', 'Introduction to Algorithms', 'Thomas H. Cormen', 'Computer Science', 8, 7, 'CS-RACK-02', '2026-09-27 16:24:36'),
(3, '978-0132350884', 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 'Software Engineering', 6, 6, 'SE-RACK-01', '2026-09-27 16:24:36'),
(4, '978-1119577553', 'Principles of Management', 'Harold Koontz', 'Business', 4, 4, 'BA-RACK-01', '2026-09-27 16:24:36');

-- --------------------------------------------------------

--
-- Table structure for table `library_issues`
--

CREATE TABLE `library_issues` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `fine_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `status` enum('issued','returned','overdue') NOT NULL DEFAULT 'issued',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `body` text NOT NULL,
  `posted_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `title`, `body`, `posted_by`, `created_at`) VALUES
(1, 'sem', 'sem', 6, '2026-10-08 18:47:23');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `target_role` varchar(50) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','danger') NOT NULL DEFAULT 'info',
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `target_role`, `title`, `message`, `type`, `link`, `is_read`, `created_at`) VALUES
(1, NULL, NULL, 'Fall 2026 Semester Registration Open', 'All students and faculty are reminded that registration and syllabus verification for Fall 2026 is now active.', 'info', 'courses', 1, '2026-09-27 16:24:36'),
(2, NULL, 'student', 'Tuition Fee Payment Reminder', 'The deadline for Fall semester fee payment is approaching. Please visit the Fees module to pay.', 'warning', 'fees', 1, '2026-09-27 16:24:36'),
(3, NULL, NULL, 'New Notice: sem', 'sem...', 'info', 'notices', 1, '2026-10-08 22:17:23');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `module`, `description`, `created_at`) VALUES
(1, 'dashboard.view', 'Dashboard', 'View dashboard metrics and charts', '2026-09-27 16:24:35'),
(2, 'users.manage', 'Users', 'Create, update, delete system users', '2026-09-27 16:24:35'),
(3, 'roles.manage', 'Roles', 'Configure roles and assign permissions', '2026-09-27 16:24:35'),
(4, 'students.view', 'Students', 'View student directories and records', '2026-09-27 16:24:35'),
(5, 'students.manage', 'Students', 'Create, edit, soft-delete, and restore students', '2026-09-27 16:24:35'),
(6, 'departments.manage', 'Departments', 'Create, edit, and delete departments', '2026-09-27 16:24:35'),
(7, 'courses.manage', 'Courses', 'Create, edit, and delete courses', '2026-09-27 16:24:35'),
(8, 'faculty.manage', 'Faculty', 'Manage faculty and staff records', '2026-09-27 16:24:35'),
(9, 'attendance.mark', 'Attendance', 'Record and update attendance', '2026-09-27 16:24:35'),
(10, 'results.manage', 'Results', 'Enter exam marks and publish transcripts', '2026-09-27 16:24:35'),
(11, 'fees.manage', 'Fees & Transactions', 'Create invoices, record payments, and print receipts', '2026-09-27 16:24:35'),
(12, 'complaints.manage', 'Complaints', 'Review and resolve helpdesk complaints', '2026-09-27 16:24:35'),
(13, 'complaints.submit', 'Complaints', 'Submit student or staff complaints', '2026-09-27 16:24:35'),
(14, 'library.manage', 'Library', 'Manage library catalog and book issues', '2026-09-27 16:24:35'),
(15, 'reports.view', 'Reports', 'Generate and export PDF, Excel, and Print reports', '2026-09-27 16:24:35'),
(16, 'settings.manage', 'Settings', 'Update institution settings, logo, and email configs', '2026-09-27 16:24:35'),
(17, 'activity_logs.view', 'Activity Logs', 'Audit trails and system security activity', '2026-09-27 16:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `marks_obtained` decimal(6,2) NOT NULL,
  `max_marks` decimal(6,2) NOT NULL DEFAULT 100.00,
  `grade` varchar(5) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `label`, `description`, `created_at`) VALUES
(1, 'admin', 'System Administrator', 'Full system access and configurations', '2026-09-27 16:24:35'),
(2, 'operator', 'Manager / Academic Operator', 'Manages academic operations, transactions, and reports', '2026-09-27 16:24:35'),
(3, 'faculty', 'Faculty / Staff Member', 'Manages courses, marks attendance, and posts notices', '2026-09-27 16:24:35'),
(4, 'student', 'Student / Customer', 'Access to view attendance, results, fees, complaints, library', '2026-09-27 16:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(2, 1),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 14),
(2, 15),
(2, 17),
(3, 1),
(3, 4),
(3, 7),
(3, 9),
(3, 10),
(3, 13),
(3, 14),
(4, 1),
(4, 13),
(4, 14);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `created_at`, `updated_at`) VALUES
(1, 'institute_name', 'Metropolitan Institute of Technology & Management', 'general', '2026-09-27 16:24:36', '2026-09-27 16:24:36'),
(2, 'institute_email', 'contact@college.local', 'general', '2026-09-27 16:24:36', '2026-09-27 16:24:36'),
(3, 'institute_phone', '+1 (555) 234-5678', 'general', '2026-09-27 16:24:36', '2026-09-27 16:24:36'),
(4, 'institute_address', '100 University Boulevard, Tech Campus, NY 10001', 'general', '2026-09-27 16:24:36', '2026-09-27 16:24:36'),
(5, 'academic_year', '2026-2027', 'general', '2026-09-27 16:24:36', '2026-09-27 16:24:36'),
(6, 'currency_symbol', '$', 'general', '2026-09-27 16:24:36', '2026-09-27 16:24:36'),
(7, 'currency_code', 'USD', 'general', '2026-09-27 16:24:36', '2026-09-27 16:24:36'),
(8, 'email_notifications', '1', 'notifications', '2026-09-27 16:24:36', '2026-09-27 16:24:36'),
(9, 'theme_mode', 'light', 'appearance', '2026-09-27 16:24:36', '2026-09-27 16:24:36'),
(10, 'allow_registration', '1', 'auth', '2026-09-27 16:24:36', '2026-09-27 16:24:36');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admission_no` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `semester` tinyint(4) NOT NULL DEFAULT 1,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_phone` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive','graduated') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `admission_no`, `name`, `photo`, `gender`, `email`, `phone`, `department_id`, `course_id`, `semester`, `date_of_birth`, `address`, `guardian_name`, `guardian_phone`, `status`, `created_at`, `deleted_at`) VALUES
(1, 3, 'CMS-2026-0001', 'John Doe', NULL, NULL, 'john.doe@student.local', '1234567890', 1, 1, 1, '2002-05-15', '123 Campus Way', NULL, NULL, 'active', '2026-09-27 12:39:02', NULL),
(2, 4, 'CMS-2026-0002', 'Parth Panewar', NULL, NULL, 'parthpanewar75@gmail.com', '07620392130', 1, 1, 1, '2008-11-11', 'Prathmesh seeds and pesticide Kinwat road bhokar', NULL, NULL, 'active', '2026-09-27 12:41:49', NULL),
(3, 10, 'CMS-2026-0003', 'Parth Panewar', NULL, 'male', 'parthpanewar755@gmail.com', '+917620392130', 1, 1, 8, NULL, 'Prathmesh seeds and pesticide Kinwat road bhokar', '', '07620392130', 'active', '2026-09-27 14:06:16', NULL),
(4, 13, 'CMS-2026-0013', 'Parth Panewar', NULL, NULL, 'shubhamkawale1432@gmail.com', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 'active', '2026-10-08 22:21:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `transaction_no` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','upi_online','bank_transfer','cheque') NOT NULL DEFAULT 'cash',
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `notes` varchar(255) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `fee_id`, `student_id`, `transaction_no`, `amount`, `payment_method`, `payment_date`, `notes`, `recorded_by`, `created_at`) VALUES
(1, 1, 3, 'TXN-20261008-9A95BD', 2500.00, 'upi_online', '2026-10-08 18:31:14', '', 5, '2026-10-08 22:01:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator','faculty','student') NOT NULL DEFAULT 'student',
  `avatar` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `failed_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `avatar`, `phone`, `status`, `reset_token`, `reset_expires_at`, `two_factor_enabled`, `failed_attempts`, `locked_until`, `last_login_at`, `created_at`, `deleted_at`) VALUES
(1, 'System Admin', 'admin@college.local', '$2y$10$oLfYbyaIKXvY0j1l.Z.sQ.OgyPnySUXtKQjdgK/xlhn6qZSAVdUd2', 'admin', NULL, NULL, 'active', NULL, NULL, 0, 0, NULL, '2026-10-08 19:14:17', '2026-09-27 16:02:29', NULL),
(2, 'Dr. Alan Turing', 'alan.turing@college.local', '$2y$10$tHR6dLjot2xDuO0cfB4z3e7fhZ1d6TpO7iamb/cK1mjyXYJmxOmG.', 'faculty', NULL, NULL, 'active', NULL, NULL, 0, 0, NULL, NULL, '2026-09-27 12:39:02', NULL),
(3, 'John Doe', 'john.doe@student.local', '$2y$10$anyplwbvepeVlyCm0feW1ORNXP5GX3Tt0mq.CqCqGrxNEofyuDk2a', 'student', NULL, NULL, 'active', NULL, NULL, 0, 0, NULL, NULL, '2026-09-27 12:39:02', NULL),
(4, 'Parth Panewar', 'parthpanewar75@gmail.com', '$2y$10$b0SBltG4KF1SgV2o11eVdeYa/jikAnZE4mhQygaDIXGPrmepQDPc2', 'admin', NULL, '07620392130', 'active', NULL, NULL, 0, 0, NULL, '2026-10-08 19:17:06', '2026-09-27 12:41:49', NULL),
(5, 'Academic Dean (Operator)', 'operator@college.local', '$2y$10$oLfYbyaIKXvY0j1l.Z.sQ.OgyPnySUXtKQjdgK/xlhn6qZSAVdUd2', 'operator', NULL, NULL, 'active', NULL, NULL, 0, 0, NULL, '2026-10-08 18:44:41', '2026-09-27 16:24:36', NULL),
(6, 'Dr. Alan Turing', 'faculty@college.local', '$2y$10$oLfYbyaIKXvY0j1l.Z.sQ.OgyPnySUXtKQjdgK/xlhn6qZSAVdUd2', 'faculty', NULL, NULL, 'active', NULL, NULL, 0, 0, NULL, '2026-10-08 18:46:36', '2026-09-27 16:24:36', NULL),
(7, 'Jane Smith (Student)', 'student@college.local', '$2y$10$oLfYbyaIKXvY0j1l.Z.sQ.OgyPnySUXtKQjdgK/xlhn6qZSAVdUd2', 'student', NULL, NULL, 'active', NULL, NULL, 0, 0, NULL, '2026-10-08 18:48:12', '2026-09-27 16:24:36', NULL),
(10, 'Parth Panewar', 'parthpanewar755@gmail.com', '$2y$10$nu7XuluOQAeDsd1o9tO4dOakmISqXb6yY3e3APxPUTWCQGfqnx.Zu', 'admin', NULL, '+917620392130', 'active', NULL, NULL, 0, 0, NULL, NULL, '2026-09-27 14:06:16', NULL),
(13, 'shubham kawale', 'shubhamkawale1432@gmail.com', '$2y$10$UHnY4lAwudkFTIH3uUBlqeOBMnX42BZ.Mv8gO0/PJalrazI8ZfMuq', 'student', NULL, '', 'active', NULL, NULL, 0, 0, NULL, '2026-10-08 18:55:01', '2026-10-08 18:51:49', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_hash` (`token_hash`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_student_date` (`student_id`,`attendance_date`),
  ADD KEY `marked_by` (`marked_by`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_no` (`ticket_no`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `library_books`
--
ALTER TABLE `library_books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `library_issues`
--
ALTER TABLE `library_issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posted_by` (`posted_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_exam_student` (`exam_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admission_no` (`admission_no`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_no` (`transaction_no`),
  ADD KEY `fee_id` (`fee_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `recorded_by` (`recorded_by`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `library_books`
--
ALTER TABLE `library_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `library_issues`
--
ALTER TABLE `library_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD CONSTRAINT `api_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `faculty_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `library_issues`
--
ALTER TABLE `library_issues`
  ADD CONSTRAINT `library_issues_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `library_books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `library_issues_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `notices_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`fee_id`) REFERENCES `fees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
