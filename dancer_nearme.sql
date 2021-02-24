-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2021 at 07:27 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dancer_nearme`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `banner` varchar(255) NOT NULL,
  `is_active` enum('0','1') NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `banner`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Title', '4U2fHwz4hL.1608027492.jpeg', '1', '2020-12-15 11:18:12', NULL, NULL),
(2, 'Title 2', 'VYgkQzZMi4.1608027505.jpeg', '1', '2020-12-15 11:18:25', NULL, NULL),
(3, 'Title 3', 'ihNc5QXUJt.1608027516.jpeg', '1', '2020-12-15 11:18:36', '2020-12-15 10:38:33', NULL),
(4, 'Title 4', '2iVpwy9er7.1608027528.jpeg', '1', '2020-12-15 11:18:48', NULL, NULL),
(5, 'Title 5', 'CCbWmIgZEZ.1608027540.jpeg', '1', '2020-12-15 11:19:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `category_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `service_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `service_cost` decimal(8,2) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` varchar(100) DEFAULT NULL,
  `booking_status` enum('0','1','2','3','4') NOT NULL DEFAULT '0',
  `payment_status` enum('0','1','2','3','4') NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `vendor_id`, `service_id`, `category_id`, `category_name`, `service_name`, `service_cost`, `booking_date`, `booking_time`, `booking_status`, `payment_status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 4, 4, 2, 'Cleaning', 'Sofa Cleaning', '100.00', '2020-12-30', '10:00 AM', '2', '0', '2020-12-26 11:39:08', NULL, NULL),
(2, 2, 4, 6, 2, 'Cleaning', 'Sofa Cleaning', '100.00', '2020-12-30', '10:00 AM', '0', '0', '2020-12-26 11:39:21', NULL, NULL),
(3, 2, 7, 6, 2, 'Cleaning', 'Sofa Cleaning', '115.00', '2020-12-30', '10:00 AM', '0', '0', '2020-12-26 11:40:22', NULL, NULL),
(4, 2, 7, 6, 2, 'Cleaning', 'Sofa Cleaning', '115.00', '2020-12-30', '10:00 AM', '0', '0', '2020-12-26 12:01:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_status_histories`
--

CREATE TABLE `booking_status_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `status` enum('0','1','2','3','4') NOT NULL,
  `cancel_reason` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `booking_status_histories`
--

INSERT INTO `booking_status_histories` (`id`, `booking_id`, `user_id`, `status`, `cancel_reason`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 1, 2, '2', 'Lorem ipsum', '2020-12-26 13:47:25', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` enum('0','1') DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`, `image`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Hipop', 'KR6G3VzaEU.1613407557.png', '1', '2021-02-15 22:15:57', NULL, NULL),
(2, 'Break Dance', 'vXVC2eVuEc.1613407573.png', '1', '2021-02-15 22:16:13', NULL, NULL),
(3, 'Classic', 'qFuAwX76LA.1613407617.png', '1', '2021-02-15 22:16:25', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category_service`
--

CREATE TABLE `category_service` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category_service`
--

INSERT INTO `category_service` (`id`, `category_id`, `service_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 2, 7, '2020-12-19 16:51:42', NULL, NULL),
(5, 2, 6, '2020-12-19 16:51:42', NULL, NULL),
(6, 2, 5, '2020-12-19 16:51:42', NULL, NULL),
(7, 2, 4, '2020-12-19 16:51:42', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `message` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `is_read` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 4, 'Hiii', NULL, '2020-12-26 09:29:30', '2020-12-26 09:29:30', NULL),
(2, 4, 2, 'Hello', NULL, '2020-12-26 09:29:53', '2020-12-26 09:29:53', NULL),
(3, 4, 2, 'Who are You?', NULL, '2020-12-26 09:30:46', '2020-12-26 09:30:46', NULL),
(4, 2, 4, 'I am john', NULL, '2020-12-26 09:51:09', '2020-12-26 09:51:09', NULL),
(5, 4, 2, 'Oh John', NULL, '2021-01-02 21:36:27', NULL, NULL),
(6, 2, 3, 'Ha bhai kab aa rahe ho', NULL, '2021-01-02 21:39:10', NULL, NULL),
(7, 3, 2, 'thodi der se abhi bahar hu mai', NULL, '2021-01-02 21:39:10', NULL, NULL),
(8, 2, 5, 'Please bhiai ye kam kar dena yrr', NULL, '2021-01-02 21:39:10', NULL, NULL),
(9, 5, 2, 'Ha yaad hai kar dunga mai', NULL, '2021-01-02 21:39:10', NULL, NULL),
(10, 2, 5, 'Thik hai par yaad se', NULL, '2021-01-02 21:39:10', NULL, NULL),
(11, 6, 2, 'Good Morning', NULL, '2021-01-02 21:39:10', NULL, NULL),
(12, 2, 6, 'Good morning :)', NULL, '2021-01-02 21:39:10', NULL, NULL),
(13, 2, 6, 'Uth gaye ', NULL, '2021-01-02 21:39:10', NULL, NULL),
(14, 6, 2, 'Sahi hai yrr', NULL, '2021-01-02 21:39:10', NULL, NULL),
(15, 7, 6, 'Hello Sym', NULL, '2021-01-02 21:39:10', NULL, NULL),
(16, 7, 6, 'Kaha ho bhai', NULL, '2021-01-02 21:39:28', NULL, NULL),
(17, 8, 7, 'Thik hai', NULL, '2021-01-02 21:39:28', NULL, NULL),
(18, 8, 2, 'Ha bat to shi hai yrr', NULL, '2021-01-02 21:39:28', NULL, NULL),
(19, 7, 4, 'Thank you', NULL, '2021-01-02 21:39:28', NULL, NULL),
(20, 2, 4, 'kuchh bhi', NULL, '2021-01-02 21:39:39', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `sub_title` varchar(100) NOT NULL,
  `price` int(11) DEFAULT 0,
  `recruit_rm_bonus_amount` int(11) NOT NULL DEFAULT 0,
  `first_generation` tinyint(4) NOT NULL,
  `second_generation` tinyint(4) NOT NULL,
  `third_generation` tinyint(4) NOT NULL,
  `is_active` enum('0','1') DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`id`, `code`, `title`, `sub_title`, `price`, `recruit_rm_bonus_amount`, `first_generation`, `second_generation`, `third_generation`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'platinum', 'GRP', 'GR Platinum', 5000, 0, 10, 30, 50, '1', '2020-12-11 13:17:24', NULL, NULL),
(2, 'gold', 'GRG', 'GR Gold', 2500, 0, 7, 20, 30, '1', '2020-12-11 13:17:52', NULL, NULL),
(3, 'silver', 'GRS', 'GR Silver', 500, 0, 5, 10, 20, '1', '2020-12-11 13:17:52', NULL, NULL),
(4, 'member', 'GRM', 'GR Member', 188, 0, 0, 0, 0, '1', '2020-12-11 13:17:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `sender_id` int(11) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) DEFAULT NULL,
  `is_read` enum('0','1') NOT NULL DEFAULT '0',
  `notification_id` bigint(20) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `meta_data` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `body`, `sender_id`, `receiver_id`, `is_read`, `notification_id`, `type`, `meta_data`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Your product like byRakesh Purohit', 'POCO X3 (Shadow Gray, 128 GB)  (6 GB RAM)', 3, 0, '0', NULL, NULL, 'a:0:{}', '2020-12-18 08:21:43', NULL, NULL),
(2, 'Your product like byRakesh Purohit', 'POCO X3 (Shadow Gray, 128 GB)  (6 GB RAM)', 3, 0, '0', NULL, NULL, 'a:1:{s:2:\"id\";s:1:\"9\";}', '2020-12-18 08:23:50', NULL, NULL),
(3, 'New order place byRakesh Purohit', 'emutz Pink Teddy Bear With Cap - 12 Inch (Pink) - 12 inch  (Pink)', 3, 0, '0', NULL, NULL, 'a:1:{s:2:\"id\";s:2:\"14\";}', '2020-12-18 08:39:48', NULL, NULL),
(4, 'New order place byRakesh Purohit', 'emutz Pink Teddy Bear With Cap - 12 Inch (Pink) - 12 inch  (Pink)', 3, 0, '0', NULL, NULL, 'a:1:{s:2:\"id\";i:15;}', '2020-12-18 08:40:38', NULL, NULL),
(5, 'Successfully accepted byTony Stark', 'emutz Pink Teddy Bear With Cap - 12 Inch (Pink) - 12 inch  (Pink)', 2, 0, '0', NULL, NULL, 'a:1:{s:2:\"id\";s:1:\"3\";}', '2020-12-18 09:01:35', NULL, NULL),
(6, 'Order accepted byTony Stark', 'emutz Pink Teddy Bear With Cap - 12 Inch (Pink) - 12 inch  (Pink)', 2, 0, '0', NULL, NULL, 'a:1:{s:2:\"id\";s:1:\"3\";}', '2020-12-18 09:05:33', NULL, NULL),
(7, 'Order declined byTony Stark', 'emutz Pink Teddy Bear With Cap - 12 Inch (Pink) - 12 inch  (Pink)', 2, 0, '0', NULL, NULL, 'a:1:{s:2:\"id\";s:1:\"3\";}', '2020-12-18 09:07:37', NULL, NULL),
(8, 'Order accepted byTony Stark', 'emutz Pink Teddy Bear With Cap - 12 Inch (Pink) - 12 inch  (Pink)', 2, 0, '0', NULL, NULL, 'a:1:{s:2:\"id\";s:1:\"3\";}', '2020-12-18 09:07:57', NULL, NULL),
(9, 'Order accepted', 'Order accepted byA One Technology', 2, 0, '0', NULL, NULL, 'a:1:{s:2:\"id\";s:1:\"3\";}', '2020-12-18 09:23:34', NULL, NULL),
(10, 'Order accepted', 'Order accepted byA One Technology', 2, 0, '0', NULL, NULL, 'a:1:{s:2:\"id\";s:1:\"3\";}', '2020-12-18 10:44:04', NULL, NULL),
(11, 'Order accepted', 'Order accepted byA One Technology', 2, 0, '0', NULL, NULL, 'a:1:{s:2:\"id\";s:1:\"3\";}', '2020-12-18 10:54:34', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('admin@gmail.com', '$2y$10$mHVOfAYVnmJxHtjgGsxiVOInfjjREcaFMYXQhIbusUStkfOOw5Fxq', '2020-12-10 05:52:27'),
('wstkpeng@gmail.com', '$2y$10$PKZLjOGFgSjkqrzjSn4XKO6fgcPNeG2o7uAUeZpe0Z/fPrdx7y9Yy', '2020-12-12 09:39:48'),
('ivan.grceo@gmail.com', '$2y$10$D6Rcdrz7iuRRuztlFjkfV.E7KgxwKEtCGWA1sSYjI1xIldopSV4VG', '2020-12-14 05:19:55'),
('dcy89@hotmail.my', '$2y$10$2zmmHGAe/jPo4r9sX3rc/OKyGa.pkX.s5hA2EWQzr/8SpuQL2NoAi', '2020-12-14 06:07:53'),
('shivamyadav8959@gmail.com', '$2y$10$l96tcGZG1TXNrJZJQDaTDeXhlrRBIrHeEzDADZIxSZD0kUbmNgCjq', '2020-12-14 06:18:30'),
('ericshim8688@gmail.com', '$2y$10$WFKx1n49BEskOPhqeLF7Yet1pZP/6zeRfHGTI1GZ63oYTgNcyb0GG', '2020-12-14 06:21:08'),
('ericshim1988@gmail.com', '$2y$10$5/gWKACnNGpLLjKpZv8OreclwlJvCRgWcEouj7nlZx0TYQZ/1mDtq', '2020-12-14 06:21:29'),
('tony.stark@gmail.com', '$2y$10$nKo8CWznfRETW/jKl2QQjOnegtJsQHY4hT.creKfhutWpsmhW8Swa', '2020-12-25 12:58:34');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(250) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `request_day` tinyint(4) NOT NULL,
  `is_active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `title`, `price`, `request_day`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Gold', '100.00', 0, '1', '2021-01-10 13:31:11', NULL, NULL),
(2, 'Basic', '0.00', 3, '1', '2021-01-10 13:31:21', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rating_reviews`
--

CREATE TABLE `rating_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `rating` enum('1','2','3','4','5') NOT NULL DEFAULT '1',
  `review` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rating_reviews`
--

INSERT INTO `rating_reviews` (`id`, `user_id`, `booking_id`, `rating`, `review`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, '5', 'Lorem ipsum', '2020-12-26 14:13:39', NULL, '2020-12-26 08:44:20'),
(2, 2, 1, '5', 'Lorem ipsum', '2020-12-26 14:14:20', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `image`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `title`) VALUES
(4, NULL, '1', '2020-12-19 16:50:14', NULL, NULL, 'House Cleaning'),
(5, NULL, '1', '2020-12-19 16:50:29', NULL, NULL, 'Carpet Cleaning'),
(6, NULL, '1', '2020-12-19 16:50:45', NULL, NULL, 'Sofa Cleaning'),
(7, NULL, '1', '2020-12-19 16:51:01', NULL, NULL, 'Water Tank');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` enum('1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '1 for admin, 2 for Vendor, 3 for User',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` int(10) DEFAULT NULL,
  `device_type` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '1 for android, 2 for IOS',
  `device_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `bio` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `social_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_type` enum('web','andoid','ios','') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_notify` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `deleted_reason` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `deactive_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `travel_distance` int(11) DEFAULT NULL,
  `business_info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lng` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `phone`, `profile_image`, `email_verified_at`, `password`, `city`, `address`, `zip_code`, `device_type`, `device_token`, `is_active`, `bio`, `social_id`, `login_type`, `remember_token`, `is_notify`, `deleted_reason`, `deactive_reason`, `travel_distance`, `business_info`, `lat`, `lng`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '1', 'Dance Near Me', 'admin@gmail.com', '999999999', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, NULL, NULL, '0', NULL, '1', NULL, '', NULL, 'J5V5inMynpaIEGxuTnOEY6mbCLnbUT1TForcsB7owDI4OS8rEofBiHx9irnh', '1', NULL, NULL, NULL, '', NULL, NULL, NULL, '2020-12-10 05:46:52', NULL),
(2, '3', 'Tony Stark', 'tony.stark@gmail.com', '7896541258', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', 'Indore', '56/A Shivaji Nagar, Indore', 452010, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-11 10:59:46', '2020-12-26 06:09:39', NULL),
(3, '3', 'Rakesh Purohit', 'john.smith@gmail.com', '8888888888', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, '56/A Shivaji Nagar, Indore', NULL, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-11 11:03:02', '2020-12-17 10:02:13', NULL),
(4, '2', 'ivangr', 'ivan.grceo@gmail.com', '0123383842', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', 'SRI PETALING', '37-2,JALAN RADIN BAGUS,BANDAR BARU SRI PETALING', 57000, '1', NULL, '0', NULL, '', NULL, NULL, '1', 'Lorem ipsum', 'Lorem ipsm', NULL, '', NULL, NULL, '2020-12-11 13:45:57', '2020-12-26 05:55:59', NULL),
(5, '3', 'ivangr', 'ocaivan@gmail.com', '0127173842', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, 'SERDANG VILLA', NULL, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-11 14:02:51', NULL, NULL),
(6, '3', 'wstkpeng', 'wstkpeng@gmail.com', '0123383860', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, 'a-15-10 galleria', NULL, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-12 10:18:44', NULL, NULL),
(7, '2', 'wstkpeng', 'wstkpen@gmail.com', '0123456789', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', 'abc', NULL, 1234, '1', NULL, '1', NULL, '', NULL, 'NmC2m4Yv4qiNaorzeYFFmgpq0rLqpCy4cuE6baLc0pyPABGkF2VvySHt1TwL', '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-12 10:29:24', '2020-12-26 12:49:58', NULL),
(8, '2', 'wstkpeng', 'wstkpe@gmail.com', '0123456788', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', 'abc', 'abc', 1234, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-12 10:30:03', NULL, NULL),
(9, '2', 'wstkpeng', 'wstkp@gmail.com', '0123456787', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', 'abc', 'abc', 1234, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-12 10:30:23', NULL, NULL),
(10, '2', 'abc', 'shivamyadav8959@gmail.com', '9876543210', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', '123', 'abc', 123, '1', NULL, '1', NULL, '', NULL, 'bHdSquDr5QmhrgiPIAoxka3Qi8TDuxXDhQWTkjN0qY655CvIY39LchEqmm1x', '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-12 10:32:54', '2020-12-14 06:03:04', NULL),
(11, '3', 'Eric', 'ericshim1988@gmail.com', '0123433842', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, 'marshall11', NULL, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-14 06:41:51', NULL, NULL),
(12, '3', 'Dylan Yong', 'dcy89@hotmail.my', '0125383913', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, 'c 16-13, Jalan PJU 1a/3, Ara Damansara, 47301 Petaling Jaya, Selangor.', NULL, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-14 06:44:30', NULL, NULL),
(13, '2', 'ericshim8688@gmail.com', 'ericshim8688@gmail.com', '0128868707', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', 'Shah alam', 'kota kemuning', 40460, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-14 06:49:30', NULL, NULL),
(14, '2', 'Dylan Yong', 'dylancy89@gmail.com', '0125393632', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', 'Petaling jaya', 'c 16-13, Jalan PJU 1a /3, Ara Damansara, 47301 Petaling Jaya Selangor', 47301, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-14 06:51:41', '2020-12-19 06:26:39', NULL),
(15, '3', 'SaSa Cheok', 'sasacheok@gmail.com', '0173433842', NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, 'No 32, Jln Sg Merbau 32/89, Greenville', NULL, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-14 06:59:53', NULL, NULL),
(16, '3', NULL, NULL, NULL, NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, NULL, NULL, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-25 17:34:56', NULL, NULL),
(17, '3', 'Shivam', NULL, NULL, NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, NULL, NULL, '1', NULL, '1', NULL, '', NULL, NULL, '1', NULL, NULL, NULL, '', '25.342432', '27.234434', '2020-12-25 17:36:04', '2020-12-25 12:48:43', NULL),
(18, '1', NULL, NULL, NULL, NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, NULL, NULL, '1', NULL, '1', NULL, '242343242343324', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-25 18:09:34', '2020-12-25 12:39:46', NULL),
(19, '1', NULL, NULL, NULL, NULL, NULL, '$2y$10$FAmAR44yqC5Qj/J6QRwHQeAy7ANfRDk3.lgdIbZ6VwpGZE5TC0RtS', NULL, NULL, NULL, '1', NULL, '1', NULL, '2423432423433241', NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-25 18:10:08', '2020-12-25 12:40:08', NULL),
(20, '2', 'shivan yadav', 'shivamyadav@gmail.com', '12345678', NULL, NULL, '$2y$10$y0pnJtBPjzr01QCOK5MQeeb5mL251b1hChHjPsDg5ipCuSUnk26i6', NULL, NULL, NULL, '0', NULL, '1', NULL, NULL, 'web', NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-27 10:24:21', NULL, NULL),
(21, '2', 'Shivam Yadav', 'shiavamyadav8959@gmail.com', '430543543', NULL, NULL, '$2y$10$7Qn3uCQrcyvbTG5ov3gwk.5dtj5kYPToFTwHgxczj4wupAbCxtB7m', NULL, NULL, NULL, '0', NULL, '1', NULL, NULL, 'web', NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-27 10:32:20', NULL, NULL),
(22, '2', 'Tony Stark', 'tony@gmail.com', '24324234', NULL, NULL, '$2y$10$nZxZg1SQ.3rFv8XjIPgnaeG7Vdp5i7AB3fRLc8TM54Ldzmurt34tm', NULL, NULL, NULL, '0', NULL, '1', NULL, NULL, 'web', NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-27 10:50:23', NULL, NULL),
(23, '2', 'tarun sagar', 'rarun@gmail.com', '24249', NULL, NULL, '$2y$10$cmQ4FSiNdscKnY2pumcdeeCfObu/JeGh4di15/XjXm9qSPzyGK7pm', NULL, NULL, NULL, '0', NULL, '1', NULL, NULL, 'web', NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-27 10:52:48', NULL, NULL),
(24, '2', 'makhan tiwari', 'makhan@gmail.com', '32423424', NULL, NULL, '$2y$10$Q../y8I6FaDaKKxhXSaTvuei6Fl49HDwMe76kTH6FKzv0OJwCaR5i', NULL, NULL, NULL, '0', NULL, '1', NULL, NULL, 'web', 'Esl0M3BbuvSwqqQ7hPtT0bZU1YU6KeS059peTbrB3Rkxv8rhbv3o0TogvnM5', '1', NULL, NULL, NULL, '', NULL, NULL, '2020-12-27 10:55:44', NULL, NULL),
(25, '3', NULL, NULL, NULL, NULL, NULL, '$2y$10$E5LrmCwT.DrNJ2EfPQt3/.mDuojI/EsCGIqU3MKjzQ3Q69Dz7jiyq', NULL, NULL, NULL, '1', NULL, '1', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2021-01-16 17:16:34', NULL, NULL),
(26, '3', NULL, NULL, NULL, NULL, NULL, '$2y$10$gtD3ziWH9lCJ//fL4hMOVO/AdNXbLuYRvqR/HqOsd7uzOsueCOYji', NULL, NULL, NULL, '1', NULL, '1', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2021-01-16 17:17:44', NULL, NULL),
(27, '3', NULL, NULL, NULL, NULL, NULL, '$2y$10$50vRpzmiRzLX30g/Xb3d5umxFLRK4H2i/HhSBCyF1RtH1gvQNSX3y', NULL, NULL, NULL, '1', NULL, '1', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2021-01-16 17:20:30', NULL, NULL),
(28, '3', NULL, NULL, NULL, NULL, NULL, '$2y$10$8tyi04vdLzEwDUzA7WgOVua0x.KrLOB9omNmM4J2u3epH41gon55u', NULL, NULL, NULL, '1', NULL, '1', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '', NULL, NULL, '2021-01-16 17:33:05', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_dances`
--

CREATE TABLE `user_dances` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `dance_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_images`
--

CREATE TABLE `user_images` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `image` varchar(250) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_plans`
--

CREATE TABLE `user_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(250) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `request_day` tinyint(4) NOT NULL,
  `plan_active_date` datetime DEFAULT NULL,
  `plan_expiry_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `payment_status` enum('0','1','') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_plans`
--

INSERT INTO `user_plans` (`id`, `user_id`, `plan_id`, `title`, `price`, `request_day`, `plan_active_date`, `plan_expiry_date`, `created_at`, `updated_at`, `deleted_at`, `payment_status`) VALUES
(14, 10, 1, 'Gold', '100.00', 0, '2021-01-16 17:55:49', '2021-04-16 17:55:49', '2021-01-16 23:25:49', NULL, NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `user_question_answers`
--

CREATE TABLE `user_question_answers` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_social_links`
--

CREATE TABLE `user_social_links` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `link` varchar(250) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_services`
--

CREATE TABLE `vendor_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `service_cost` decimal(8,2) DEFAULT NULL,
  `is_active` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vendor_services`
--

INSERT INTO `vendor_services` (`id`, `vendor_id`, `service_id`, `service_cost`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 4, '100.00', '1', '2020-12-26 10:05:29', '2020-12-26 15:23:11', NULL),
(2, 7, 4, '110.00', '1', '2020-12-26 10:05:29', NULL, NULL),
(3, 4, 7, '123.00', '1', '2020-12-26 10:05:52', '2020-12-26 15:24:03', NULL),
(4, 7, 6, '115.00', '1', '2020-12-26 10:05:52', NULL, NULL),
(5, 4, 6, '111.00', '1', '2020-12-26 15:56:00', '2020-12-26 15:56:00', NULL),
(6, 7, 5, '159.00', '1', '2020-12-26 18:20:33', '2020-12-26 18:20:33', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_status_histories`
--
ALTER TABLE `booking_status_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_service`
--
ALTER TABLE `category_service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`(191));

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rating_reviews`
--
ALTER TABLE `rating_reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_images`
--
ALTER TABLE `user_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_plans`
--
ALTER TABLE `user_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_question_answers`
--
ALTER TABLE `user_question_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_social_links`
--
ALTER TABLE `user_social_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_services`
--
ALTER TABLE `vendor_services`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `booking_status_histories`
--
ALTER TABLE `booking_status_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `category_service`
--
ALTER TABLE `category_service`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rating_reviews`
--
ALTER TABLE `rating_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user_images`
--
ALTER TABLE `user_images`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_plans`
--
ALTER TABLE `user_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_question_answers`
--
ALTER TABLE `user_question_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_social_links`
--
ALTER TABLE `user_social_links`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_services`
--
ALTER TABLE `vendor_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
