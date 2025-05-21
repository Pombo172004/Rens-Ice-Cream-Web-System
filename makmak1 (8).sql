-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost: 3306:4306
-- Generation Time: May 21, 2025 at 03:27 PM
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
-- Database: `makmak1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`, `full_name`, `created_at`, `last_login`, `status`) VALUES
(1, 'AdminRenato123', '$2y$10$quGYe.qlIEHIIShMOtKmkOykeDn9ORjN4pXknClDTmtDT3dZGngE2', 'Renato@gmail.com', 'Renato', '2025-05-05 14:20:17', '2025-05-21 12:21:06', 'active'),
(3, 'admin', '$2y$10$aTH/X6TOA1QTSrNGJrzfne10y.fQnXf6S5oJIRNm1KlSGChuvoDDC', 'admin@gmail.com', 'admin', '2025-05-21 12:15:39', '2025-05-21 12:15:55', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_type` varchar(20) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_time` time DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `instructions` varchar(255) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Pending',
  `feedback_rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `order_type`, `product_id`, `quantity`, `delivery_date`, `delivery_time`, `address`, `contact`, `instructions`, `payment_method`, `status`, `feedback_rating`) VALUES
(100, 14, 'pickup', 2, 1, '2025-05-20', '17:55:00', '', '', '', 'Cash', 'approved', NULL),
(101, 14, 'pickup', 1, 1, '2025-05-20', '17:00:00', '', '', '', 'Cash', 'approved', NULL),
(102, 14, 'delivery', 1, 3, '2025-06-20', '10:00:00', 'asdas', '09262418240', 'asds', 'Cash', 'approved', NULL),
(103, 14, 'delivery', 1, 2, '2025-05-25', '13:00:00', 'Purokk 3B Poblacion Valencia City ', '09262418240', 'Estanero Store', 'Cash', 'approved', NULL),
(104, 14, 'pickup', 4, 4, '2025-08-20', '10:00:00', '', '', '', 'Cash', 'approved', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_name`, `quantity`, `created_at`) VALUES
(111, 12, 1, 'Ube Ice Cream', 2, '2025-05-18 15:43:51'),
(161, 14, 6, 'Buko', 1, '2025-05-19 09:22:36');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('success','failed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `ip_address`, `attempt_time`, `status`) VALUES
(1, 'AdminRenato123', '::1', '2025-05-05 14:22:09', 'success'),
(2, 'AdminRenato123', '::1', '2025-05-05 16:27:25', 'success'),
(3, 'AdminRenato123', '::1', '2025-05-16 08:25:39', 'success'),
(4, 'AdminRenato123', '::1', '2025-05-17 07:34:13', 'success'),
(5, 'AdminRenato123', '::1', '2025-05-18 14:10:03', 'success'),
(6, 'AdminRenato123', '::1', '2025-05-18 15:21:02', 'success'),
(7, 'pombo172004', '::1', '2025-05-18 15:48:59', 'failed'),
(8, 'AdminRenato123', '::1', '2025-05-18 15:49:02', 'success'),
(9, 'AdminRenato123', '::1', '2025-05-18 16:24:16', 'success'),
(10, 'AdminRenato123', '::1', '2025-05-19 03:45:17', 'success'),
(11, 'pombo172004', '::1', '2025-05-19 05:54:40', 'failed'),
(12, 'AdminRenato123', '::1', '2025-05-19 05:54:45', 'success'),
(13, 'pombo172004', '::1', '2025-05-19 06:29:05', 'failed'),
(14, 'pombo172004', '::1', '2025-05-19 06:29:07', 'failed'),
(15, 'AdminRenato123', '::1', '2025-05-19 06:29:12', 'success'),
(16, 'AdminRenato123', '::1', '2025-05-19 07:32:56', 'success'),
(17, 'AdminRenato123', '::1', '2025-05-19 09:11:15', 'success'),
(18, 'admin', '::1', '2025-05-21 12:11:01', 'failed'),
(19, 'AdminRenato123', '::1', '2025-05-21 12:11:35', 'success'),
(20, 'admin', '::1', '2025-05-21 12:15:56', 'success'),
(21, 'AdminRenato123', '::1', '2025-05-21 12:21:06', 'success');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(67, 12, 'Your order #5 has been denied.', 0, '2025-05-16 05:53:44'),
(68, 12, 'Your order #7 has been approved.', 0, '2025-05-16 05:57:21'),
(69, 12, 'Your order #8 has been approved.', 0, '2025-05-16 06:07:10'),
(70, 12, 'Your order #9 has been approved.', 0, '2025-05-16 06:07:11'),
(71, 12, 'Your order #11 has been approved.', 0, '2025-05-16 06:07:12'),
(72, 12, 'Your order #10 has been approved.', 0, '2025-05-16 06:07:12'),
(73, 12, 'Your booking has been approved!', 0, '2025-05-16 06:09:23'),
(74, 12, 'Your booking has been approved!', 0, '2025-05-16 06:18:14'),
(75, 12, 'Your booking has been approved!', 0, '2025-05-16 06:19:38'),
(76, 13, 'Your order #12 has been approved.', 0, '2025-05-16 06:22:47'),
(77, 13, 'Your booking has been denied.', 0, '2025-05-16 06:23:44'),
(78, 13, 'Your booking has been approved!', 0, '2025-05-16 06:23:53'),
(79, 13, 'Your booking has been denied.', 0, '2025-05-16 06:24:10'),
(80, 13, 'Your booking has been approved!', 0, '2025-05-16 06:26:39'),
(81, 13, 'Your booking has been denied.', 0, '2025-05-16 06:26:44'),
(82, 13, 'Your booking has been approved!', 0, '2025-05-16 06:29:04'),
(83, 13, 'Your booking has been denied.', 0, '2025-05-16 06:29:09'),
(84, 13, 'Your order #12 has been denied.', 0, '2025-05-16 06:29:34'),
(85, 13, 'Your order #12 has been approved.', 0, '2025-05-16 06:30:25'),
(86, 13, 'Your order #12 has been approved.', 0, '2025-05-16 06:30:38'),
(87, 13, 'Your order #12 has been denied.', 0, '2025-05-16 06:30:38'),
(88, 13, 'Your order #12 has been approved.', 0, '2025-05-16 06:30:58'),
(89, 13, 'Your booking has been approved!', 0, '2025-05-16 06:31:37'),
(90, 13, 'Your booking has been denied.', 0, '2025-05-16 06:33:09'),
(91, 13, 'Your booking has been approved!', 0, '2025-05-16 07:07:03'),
(92, 13, 'Your booking has been approved!', 0, '2025-05-16 07:55:06'),
(93, 13, 'Your booking has been approved!', 0, '2025-05-16 07:56:21'),
(94, 13, 'Your booking has been approved!', 0, '2025-05-16 08:00:26'),
(95, 13, 'Your booking has been approved!', 0, '2025-05-16 08:00:26'),
(96, 13, 'Your booking has been approved!', 0, '2025-05-16 08:00:27'),
(97, 13, 'Your booking has been approved!', 0, '2025-05-16 08:00:27'),
(98, 13, 'Your order #13 has been approved.', 0, '2025-05-16 08:33:05'),
(99, 13, 'Your order #14 has been approved.', 0, '2025-05-16 08:33:06'),
(100, 13, 'Your order #15 has been approved.', 0, '2025-05-16 08:33:06'),
(101, 13, 'Your order #16 has been approved.', 0, '2025-05-16 08:33:07'),
(102, 13, 'Your booking has been approved!', 0, '2025-05-16 08:34:56'),
(103, 13, 'Your booking has been approved!', 0, '2025-05-16 08:34:57'),
(104, 13, 'Your booking has been approved!', 0, '2025-05-16 08:34:59'),
(105, 13, 'Your booking has been approved!', 0, '2025-05-16 08:35:00'),
(106, 13, 'Your booking has been approved!', 0, '2025-05-16 08:52:14'),
(107, 13, 'Your order #17 has been approved.', 0, '2025-05-16 08:53:40'),
(108, 13, 'Your order #13 has been denied.', 0, '2025-05-16 08:58:10'),
(109, 13, 'Your order #15 has been denied.', 0, '2025-05-16 08:58:12'),
(110, 13, 'Your order #18 has been denied.', 0, '2025-05-16 08:59:06'),
(111, 13, 'Your booking has been denied.', 0, '2025-05-16 09:06:15'),
(112, 13, 'Your order #19 has been approved.', 0, '2025-05-16 09:21:02'),
(113, 13, 'Your order #15 has been approved.', 0, '2025-05-16 09:23:03'),
(114, 13, 'Your order #14 has been denied.', 0, '2025-05-16 09:24:11'),
(115, 13, 'Your order #15 has been denied.', 0, '2025-05-16 09:24:12'),
(116, 13, 'Your order #16 has been denied.', 0, '2025-05-16 09:24:13'),
(117, 13, 'Your order #17 has been denied.', 0, '2025-05-16 09:24:13'),
(118, 13, 'Your order #14 has been approved.', 0, '2025-05-16 09:24:44'),
(119, 13, 'Your order #15 has been approved.', 0, '2025-05-16 09:24:45'),
(120, 13, 'Your order #16 has been approved.', 0, '2025-05-16 09:24:45'),
(121, 13, 'Your order #17 has been approved.', 0, '2025-05-16 09:24:46'),
(122, 13, 'Your order #18 has been approved.', 0, '2025-05-16 09:24:46'),
(123, 13, 'Your order #20 has been denied.', 0, '2025-05-16 09:27:25'),
(124, 13, 'Your order #20 has been approved.', 0, '2025-05-16 09:27:41'),
(125, 13, 'Your booking has been approved!', 0, '2025-05-16 09:31:29'),
(126, 13, 'Your booking has been denied.', 0, '2025-05-16 09:31:40'),
(127, 13, 'Your order #19 has been denied.', 0, '2025-05-16 09:32:15'),
(128, 13, 'Your order #20 has been denied.', 0, '2025-05-16 09:32:15'),
(129, 13, 'Your booking has been approved!', 0, '2025-05-16 09:51:24'),
(130, 12, 'Your booking has been approved!', 0, '2025-05-16 13:20:07'),
(131, 12, 'Your booking has been approved!', 0, '2025-05-16 13:20:09'),
(132, 12, 'Your order #22 has been approved.', 0, '2025-05-16 13:20:36'),
(133, 12, 'Your order #21 has been approved.', 0, '2025-05-16 13:20:38'),
(134, 12, 'Your order #21 has been denied.', 0, '2025-05-16 13:20:47'),
(135, 12, 'Your booking has been approved!', 0, '2025-05-17 07:35:20'),
(136, 12, 'Your booking has been approved!', 0, '2025-05-17 07:37:19'),
(137, 12, 'Your order #21 has been approved.', 0, '2025-05-17 07:40:08'),
(138, 12, 'Your order #23 has been approved.', 0, '2025-05-17 07:43:56'),
(139, 12, 'Your order #24 has been approved.', 0, '2025-05-17 07:43:57'),
(140, 12, 'Your order #25 has been approved.', 0, '2025-05-17 07:43:58'),
(141, 12, 'Your order #26 has been approved.', 0, '2025-05-17 07:52:12'),
(142, 12, 'Your order #26 has been approved.', 0, '2025-05-17 08:10:18'),
(143, 12, 'Your order #26 has been approved.', 0, '2025-05-17 08:12:08'),
(144, 12, 'Your order #26 has been approved.', 0, '2025-05-17 08:12:18'),
(145, 12, 'Your order #26 has been approved.', 0, '2025-05-17 08:13:05'),
(146, 12, 'Your order #26 has been approved.', 0, '2025-05-17 08:13:20'),
(147, 12, 'Your order #27 has been approved.', 0, '2025-05-17 08:14:21'),
(148, 12, 'Your order #28 has been approved.', 0, '2025-05-17 08:14:53'),
(149, 12, 'Your booking has been approved!', 0, '2025-05-17 08:19:33'),
(150, 12, 'Your order #29 has been approved.', 0, '2025-05-17 08:26:56'),
(151, 12, 'Your order #30 has been approved.', 0, '2025-05-17 08:29:08'),
(152, 12, 'Your order #30 has been approved.', 0, '2025-05-17 08:29:34'),
(153, 12, 'Your order #31 has been approved.', 0, '2025-05-17 08:29:54'),
(154, 12, 'Your order #34 has been approved.', 0, '2025-05-17 13:24:03'),
(155, 12, 'Your order #32 has been approved.', 0, '2025-05-17 13:49:00'),
(156, 12, 'Your order #33 has been approved.', 0, '2025-05-17 13:50:14'),
(157, 12, 'Your order #35 has been approved.', 0, '2025-05-17 14:00:23'),
(158, 12, 'Your order #38 has been approved.', 0, '2025-05-17 14:10:31'),
(159, 12, 'Your order #37 has been approved.', 0, '2025-05-17 14:15:59'),
(160, 12, 'Your order #36 has been approved.', 0, '2025-05-17 14:17:33'),
(161, 12, 'Your order #39 has been approved.', 0, '2025-05-17 14:19:07'),
(162, 12, 'Your order #40 has been approved.', 0, '2025-05-17 14:23:32'),
(163, 12, 'Your booking has been approved!', 0, '2025-05-17 14:25:07'),
(164, 12, 'Your booking has been approved!', 0, '2025-05-17 14:25:08'),
(165, 12, 'Your booking has been approved!', 0, '2025-05-17 14:25:08'),
(166, 12, 'Your order #41 has been approved.', 0, '2025-05-17 14:46:37'),
(167, 12, 'Your order #41 has been approved.', 0, '2025-05-17 14:46:37'),
(168, 12, 'Your order #42 has been approved.', 0, '2025-05-17 14:46:38'),
(169, 12, 'Your booking has been approved!', 0, '2025-05-17 14:47:07'),
(170, 12, 'Your order #43 has been approved.', 0, '2025-05-17 15:02:51'),
(171, 12, 'Your booking has been approved!', 0, '2025-05-18 15:50:43'),
(172, 12, 'Your booking has been approved!', 0, '2025-05-18 15:50:45'),
(173, 12, 'Your booking has been approved!', 0, '2025-05-18 15:50:46'),
(174, 12, 'Your booking has been approved!', 0, '2025-05-18 15:50:47'),
(175, 14, 'Your order #44 has been approved.', 0, '2025-05-18 16:25:27'),
(176, 14, 'Your order #45 has been denied.', 0, '2025-05-18 16:25:49'),
(177, 14, 'Your booking has been approved!', 0, '2025-05-18 16:26:26'),
(178, 14, 'Your booking has been denied.', 0, '2025-05-18 16:26:28'),
(179, 14, 'Your order #46 has been approved.', 0, '2025-05-18 16:41:48'),
(180, 14, 'Your order #47 has been approved.', 0, '2025-05-18 17:03:04'),
(181, 14, 'Your order #48 has been approved.', 0, '2025-05-18 17:03:04'),
(182, 14, 'Your order #49 has been approved.', 0, '2025-05-18 17:03:05'),
(183, 14, 'Your order #50 has been approved.', 0, '2025-05-19 04:32:06'),
(184, 14, 'Your order #51 has been approved.', 0, '2025-05-19 04:36:49'),
(185, 14, 'Your order #52 has been approved.', 0, '2025-05-19 04:40:42'),
(186, 14, 'Your order #53 has been approved.', 0, '2025-05-19 04:43:25'),
(187, 14, 'Your order #54 has been approved.', 0, '2025-05-19 04:44:20'),
(188, 14, 'Your order #55 has been approved.', 0, '2025-05-19 04:46:34'),
(189, 14, 'Your order #56 has been approved.', 0, '2025-05-19 04:47:48'),
(190, 14, 'Your order #57 has been approved.', 0, '2025-05-19 04:49:53'),
(191, 14, 'Your order #58 has been approved.', 0, '2025-05-19 04:50:16'),
(192, 14, 'Your booking has been approved!', 0, '2025-05-19 04:52:55'),
(193, 14, 'Your booking has been approved!', 0, '2025-05-19 04:53:04'),
(194, 14, 'Your order #64 has been approved.', 0, '2025-05-19 05:28:02'),
(195, 14, 'Your order #65 has been approved.', 0, '2025-05-19 05:57:00'),
(196, 14, 'Your order #70 has been approved.', 0, '2025-05-19 06:20:20'),
(197, 14, 'Your order #71 has been approved.', 0, '2025-05-19 06:20:21'),
(198, 14, 'Your order #72 has been approved.', 0, '2025-05-19 06:21:27'),
(199, 14, 'Your order #73 has been approved.', 0, '2025-05-19 06:24:46'),
(200, 14, 'Your order #74 has been approved.', 0, '2025-05-19 06:26:33'),
(201, 14, 'Your order #75 has been approved.', 0, '2025-05-19 06:29:42'),
(202, 14, 'Your booking has been approved!', 0, '2025-05-19 06:31:22'),
(203, 14, 'Your booking has been approved!', 0, '2025-05-19 06:31:23'),
(204, 14, 'Your order #76 has been approved.', 0, '2025-05-19 06:34:31'),
(205, 14, 'Your order #77 has been approved.', 0, '2025-05-19 06:37:24'),
(206, 14, 'Your order #80 has been approved.', 0, '2025-05-19 06:41:03'),
(207, 14, 'Your order #78 has been approved.', 0, '2025-05-19 07:00:10'),
(208, 14, 'Your order #79 has been approved.', 0, '2025-05-19 07:00:12'),
(209, 14, 'Your order #81 has been approved.', 0, '2025-05-19 07:32:57'),
(210, 14, 'Your order #86 has been approved.', 0, '2025-05-19 07:41:58'),
(211, 14, 'Your order #87 has been approved.', 0, '2025-05-19 07:42:00'),
(212, 14, 'Your order #88 has been approved.', 0, '2025-05-19 07:44:07'),
(213, 14, 'Your order #89 has been approved.', 0, '2025-05-19 07:44:08'),
(214, 14, 'Your order #90 has been approved.', 0, '2025-05-19 07:48:15'),
(215, 14, 'Your order #91 has been approved.', 0, '2025-05-19 07:50:39'),
(216, 14, 'Your order #92 has been approved.', 0, '2025-05-19 09:12:39'),
(217, 14, 'Your booking has been approved!', 0, '2025-05-19 09:18:36'),
(218, 14, 'Your booking has been approved!', 0, '2025-05-19 09:24:15'),
(219, 14, 'Your order #93 has been denied.', 0, '2025-05-19 09:25:31'),
(220, 14, 'Your order #94 has been approved.', 0, '2025-05-19 09:34:08'),
(221, 14, 'Your order #94 has been denied.', 0, '2025-05-19 09:34:14');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'pending',
  `rating` int(11) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `quantity`, `total_price`, `order_date`, `status`, `rating`, `shipping_fee`) VALUES
(88, 14, 1, 1, 299.00, '2025-05-19 15:44:01', 'approved', NULL, 50.00),
(89, 14, 2, 1, 299.00, '2025-05-19 15:44:01', 'approved', NULL, 50.00),
(90, 14, 1, 1, 299.00, '2025-05-19 15:48:11', 'approved', NULL, 50.00),
(91, 14, 3, 5, 1495.00, '2025-05-19 15:50:26', 'approved', NULL, 50.00),
(92, 14, 1, 5, 1495.00, '2025-05-19 17:11:57', 'received', 5, 50.00),
(93, 14, 1, 1, 299.00, '2025-05-19 17:25:25', 'denied', NULL, 50.00),
(94, 14, 1, 1, 299.00, '2025-05-19 17:31:30', 'denied', NULL, 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `image`, `category`, `stock`) VALUES
(1, 'Ube Ice Cream', 'Delicious purple yam flavored ice cream. Creamy, rich, and perfect for summer days!', 299.00, 'Ube.png', 'Best Seller', 35),
(2, 'Mango Ice Cream', 'Sweet and tropical mango ice cream.', 299.00, 'Mango.png', 'Best Seller', 44),
(3, 'Chocolate Ice Cream', 'Classic chocolate flavor loved by all ages.', 299.00, 'chocolate.png', 'Regular', 45),
(4, 'Cookies & Cream', 'Crunchy cookies blended with creamy vanilla ice cream.', 299.00, 'cookies.png', 'Regular', 46),
(5, 'Vanilla Ice Cream', NULL, 299.00, '682ab2dfb3ab6_Vanilla.png', NULL, 50),
(6, 'Buko', 'This is a Buko Ice cream', 299.00, '682af84a5c7e0_Vanilla.png', 'Ice Cream', 50);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_code` varchar(6) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `email`, `password`, `reset_code`, `profile_pic`, `address`, `contact`) VALUES
(13, 'reymark', 'Pombo', 'makmak12', 'pombochristiandave@gmail.com', '$2y$10$TfbNjAEXwfTsTWDRz5.WXe9GEWz3D.lWAiMHTNJ/WSmcMwrl5hmLS', NULL, 'user_13_1747382583.png', 'P-3B Pob Valencia City, Bukidnon', '09926551735'),
(14, 'Christian', 'Val', 'pombo172004', 'christiandavepombo@gmail.com', '$2y$10$frgbD/9xRid7EQMpEe11Y.AYmagaa/WHP06jgWAxvZXsB0qV32QNW', '596962', NULL, NULL, NULL),
(16, '', '', '2301112027@student.buksu.edu.ph', '2301112027@student.buksu.edu.ph', '$2y$10$uNeQnk3F.u7cJLNru6nOZ.a9pNUqI/UL6KLWNmabOCQufSeSXS8LO', NULL, NULL, NULL, NULL),
(17, '', '', 'jyragracesamillano3@gmail.com', 'jyragracesamillano3@gmail.com', '$2y$10$h5zkebaKFA9DuQ5MQY6LX.cFGNWqZDXyfqQzIBNvpMrLEg6PgRHMy', NULL, NULL, NULL, NULL),
(18, 'user', 'user', 'user', 'pomboCD@gmail.com', '$2y$10$Vszriop9eYONR.jd31jxA.aXw6ElIcJ7qZvJ074JkMj97VB3es9ai', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `cnumber` varchar(20) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
